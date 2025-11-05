<?php

namespace App\Providers;

use App\Models\User;
use App\Policies\UserPolicy;
use App\Models\Task;
use App\Policies\TaskPolicy;
use App\Models\Contribution;
use App\Policies\ContributionPolicy;
use App\Models\Department;
use App\Policies\DepartmentPolicy;
use App\Models\Role;
use App\Policies\RolePolicy;
use App\Models\Group;
use App\Policies\GroupPolicy;
use App\Models\Campaign;
use App\Policies\CampaignPolicy;
use App\Models\Meeting;
use App\Observers\MeetingObserver;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use App\Channels\SmsChannel;
use Illuminate\Database\Eloquent\Builder;

class AppServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        User::class => UserPolicy::class,
        Task::class => TaskPolicy::class,
        Contribution::class => ContributionPolicy::class,
        Department::class => DepartmentPolicy::class,
        Role::class => RolePolicy::class,
        Group::class => GroupPolicy::class,
        Campaign::class => CampaignPolicy::class,
    ];

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register policies
        $this->registerPolicies();

        // Observers
        Meeting::observe(MeetingObserver::class);

        // Register custom notification channels
        Notification::extend('sms', function ($app) {
            return new SmsChannel();
        });

        // Simple admin gate using User::isAdmin
        Gate::define('admin', function (\App\Models\User $user) {
            return $user->isAdmin();
        });

        // Pagination defaults macros
        Builder::macro('paginateDefault', function (array $columns = ['*']) {
            $perPage = (int) request('per_page', (int) config('pagination.per_page', 20));
            $maxPerPage = (int) config('pagination.max_per_page', 100);
            $perPage = max(1, min($perPage, $maxPerPage));
            /* @var Builder $this */
            return $this->paginate($perPage, $columns);
        });

        Builder::macro('simplePaginateDefault', function (array $columns = ['*']) {
            $perPage = (int) request('per_page', (int) config('pagination.per_page', 20));
            $maxPerPage = (int) config('pagination.max_per_page', 100);
            $perPage = max(1, min($perPage, $maxPerPage));
            /* @var Builder $this */
            return $this->simplePaginate($perPage, $columns);
        });
    }

    /**
     * Register the application's policies.
     */
    public function registerPolicies(): void
    {
        foreach ($this->policies as $model => $policy) {
            Gate::policy($model, $policy);
        }
    }
}
