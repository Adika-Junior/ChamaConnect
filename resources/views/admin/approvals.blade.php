<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Admin Approvals - TaskFlow</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="max-w-7xl mx-auto py-10 px-4">
        <div class="flex items-center justify-between mb-8">
            <h1 class="text-3xl font-bold">Pending Approvals</h1>
            <a href="{{ route('dashboard') }}" class="text-sm text-slate-600 hover:underline">Back to Dashboard</a>
        </div>

        @php($isAdmin = auth()->check() && auth()->user()->isAdmin())
        @unless($isAdmin)
            <p class="text-red-600">You are not authorized to view this page.</p>
        @else
            @if($pendingUsers->isEmpty())
                <div class="bg-white p-8 rounded-xl border border-slate-200 text-slate-600">No pending users.</div>
            @else
                <div class="bg-white rounded-xl border border-slate-200 overflow-hidden">
                    <table class="min-w-full divide-y divide-slate-200">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Employee ID</th>
                                <th class="px-6 py-3 text-left text-xs font-semibold text-slate-700">Phone</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-slate-700">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @foreach($pendingUsers as $user)
                                <tr>
                                    <td class="px-6 py-4 text-sm">{{ $user->name }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $user->email }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $user->employee_id }}</td>
                                    <td class="px-6 py-4 text-sm">{{ $user->phone }}</td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button data-user-id="{{ $user->id }}" data-action="approve" class="approve-btn inline-flex items-center px-3 py-2 bg-green-600 text-white rounded-md text-sm hover:bg-green-700">Approve</button>
                                        <button data-user-id="{{ $user->id }}" data-action="reject" class="reject-btn inline-flex items-center px-3 py-2 bg-red-600 text-white rounded-md text-sm hover:bg-red-700" data-bs-toggle="modal" data-bs-target="#rejectModal{{ $user->id }}">Reject</button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        @endunless
    </div>

    <!-- Reject Modals -->
    @foreach($pendingUsers as $user)
        <div class="modal fade" id="rejectModal{{ $user->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Reject User Registration</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="rejectForm{{ $user->id }}">
                        <div class="modal-body">
                            <p class="mb-3">Rejecting registration for: <strong>{{ $user->name }}</strong> ({{ $user->email }})</p>
                            <div class="mb-3">
                                <label for="reason{{ $user->id }}" class="form-label">Reason (optional)</label>
                                <textarea name="reason" id="reason{{ $user->id }}" class="form-control" rows="3" placeholder="Reason for rejection..."></textarea>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-danger">Reject Registration</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endforeach

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function postJson(url, data = {}) {
            const res = await fetch(url, {
                method: 'POST',
                headers: { 
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                },
                credentials: 'same-origin',
                body: JSON.stringify(data)
            });
            if (!res.ok) throw new Error('Request failed');
            return res.json();
        }

        document.querySelectorAll('.approve-btn').forEach(btn => {
            btn.addEventListener('click', async () => {
                const id = btn.getAttribute('data-user-id');
                try {
                    await postJson(`/auth/admin/approve/${id}`);
                    location.reload();
                } catch (e) {
                    alert('Action failed.');
                }
            });
        });

        @foreach($pendingUsers as $user)
        document.getElementById('rejectForm{{ $user->id }}').addEventListener('submit', async (e) => {
            e.preventDefault();
            const reason = document.getElementById('reason{{ $user->id }}').value;
            try {
                await postJson(`/auth/admin/reject/{{ $user->id }}`, { reason });
                location.reload();
            } catch (e) {
                alert('Action failed.');
            }
        });
        @endforeach
    </script>
</body>
</html>


