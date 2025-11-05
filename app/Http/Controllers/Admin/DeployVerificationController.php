<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;

class DeployVerificationController extends Controller
{
    public function index()
    {
        $code = Artisan::call('deploy:verify', [
            '--base-url' => config('app.url')
        ]);
        $output = Artisan::output();
        return view('admin.deploy.verify', compact('code', 'output'));
    }
}


