<?php

namespace App\Http\Controllers;

use App\Models\SaccoRegistration;
use App\Models\Group;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class SaccoRegistrationController extends Controller
{
    public function create()
    {
        return view('sacco.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'registration_number' => 'required|string|max:100|unique:sacco_registrations,registration_number',
            'registered_at' => 'nullable|date',
            'address' => 'nullable|string|max:255',
            'county' => 'nullable|string|max:100',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'certificate' => 'nullable|file|mimetypes:application/pdf,image/*|max:10240',
            'bylaws' => 'nullable|file|mimetypes:application/pdf,image/*|max:10240',
            'chair_name' => 'required|string|max:255',
            'chair_phone' => 'required|string|max:20',
            'secretary_name' => 'required|string|max:255',
            'secretary_phone' => 'required|string|max:20',
            'treasurer_name' => 'required|string|max:255',
            'treasurer_phone' => 'required|string|max:20',
        ]);

        $certificatePath = $request->file('certificate')?->store('sacco_docs', 'public');
        $bylawsPath = $request->file('bylaws')?->store('sacco_docs', 'public');

        $reg = SaccoRegistration::create([
            'name' => $validated['name'],
            'registration_number' => $validated['registration_number'],
            'registered_at' => $validated['registered_at'] ?? null,
            'address' => $validated['address'] ?? null,
            'county' => $validated['county'] ?? null,
            'contact_email' => $validated['contact_email'],
            'contact_phone' => $validated['contact_phone'],
            'certificate_path' => $certificatePath,
            'bylaws_path' => $bylawsPath,
            'officials' => [
                'chair' => ['name' => $validated['chair_name'], 'phone' => $validated['chair_phone']],
                'secretary' => ['name' => $validated['secretary_name'], 'phone' => $validated['secretary_phone']],
                'treasurer' => ['name' => $validated['treasurer_name'], 'phone' => $validated['treasurer_phone']],
            ],
            'submitted_by' => Auth::id(),
            'status' => 'pending',
        ]);

        return redirect()->route('sacco.register')->with('status', 'Registration submitted. Our admins will review and contact you.');
    }

    public function adminIndex()
    {
        $this->authorize('viewAny', Group::class); // reuse group policy for admins
        $registrations = SaccoRegistration::orderByDesc('created_at')->paginate(20);
        return view('admin.sacco-registrations.index', compact('registrations'));
    }

    public function approve(Request $request, SaccoRegistration $registration)
    {
        $this->authorize('create', Group::class);
        if ($registration->status !== 'pending') {
            return back()->with('error', 'Registration already processed.');
        }

        // Create SACCO group on approval
        $group = Group::create([
            'name' => $registration->name,
            'type' => 'sacco',
            'description' => 'SACCO registered via onboarding',
            'is_public' => true,
            'accepting_applications' => true,
            'registration_number' => $registration->registration_number,
            'registered_at' => $registration->registered_at,
            'location' => $registration->address,
            'contact_email' => $registration->contact_email,
            'contact_phone' => $registration->contact_phone,
            'created_by' => $request->user()->id,
        ]);

        $registration->update([
            'status' => 'approved',
            'reviewed_by' => $request->user()->id,
        ]);

        return back()->with('status', 'SACCO approved and group created.');
    }

    public function reject(Request $request, SaccoRegistration $registration)
    {
        $this->authorize('create', Group::class);
        $validated = $request->validate([
            'reason' => 'nullable|string|max:500',
        ]);
        $registration->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['reason'] ?? null,
            'reviewed_by' => $request->user()->id,
        ]);
        return back()->with('status', 'SACCO registration rejected.');
    }
}


