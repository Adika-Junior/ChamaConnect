<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\Auth\InviteController;
use App\Models\User;
use Illuminate\Http\Request;

class DebugInvite extends Command
{
    protected $signature = 'debug:invite {email=debug@example.com}';
    protected $description = 'Call InviteController->invite with a fake admin user and print the response';

    public function handle()
    {
        $email = $this->argument('email');

        // create a lightweight fake admin user object that will satisfy
        // authorization checks (we return true for any ability here).
        $admin = new class {
            public $id = 1;
            public $status = 'active';
            public function can($ability, $model = null) { return true; }
        };

        $request = Request::create('/auth/invite', 'POST', ['email' => $email]);
        $request->setUserResolver(fn () => $admin);
        $request->headers->set('Accept', 'application/json');

        $controller = $this->laravel->make(InviteController::class);

        $response = $controller->invite($request);

        $this->info('Status: ' . $response->getStatusCode());
        $this->info('Content: ' . $response->getContent());

        return 0;
    }
}
