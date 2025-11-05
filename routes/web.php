<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
})->middleware('cache.headers:public;max_age=300;etag');
// Public donation receipt routes
Route::get('/donations/{reference}/receipt', [App\Http\Controllers\Donations\DonationReceiptController::class, 'show'])
    ->middleware('cache.headers:public;max_age=300;etag')
    ->name('donations.receipt.show');
Route::get('/donations/{reference}/receipt/download', [App\Http\Controllers\Donations\DonationReceiptController::class, 'download'])
    ->middleware('cache.headers:public;max_age=300;etag')
    ->name('donations.receipt.download');

// Public docs landing
Route::get('/docs', function () { return view('docs.index'); })->middleware('cache.headers:public;max_age=600;etag')->name('docs');
Route::get('/docs/quick-start', function () { return view('docs.quickstart'); })->middleware('cache.headers:public;max_age=600;etag')->name('docs.quickstart');
Route::get('/docs/features', function () { return view('docs.features'); })->middleware('cache.headers:public;max_age=600;etag')->name('docs.features');
Route::get('/docs/admin-handbook', function () { return view('docs.admin'); })->middleware('cache.headers:public;max_age=600;etag')->name('docs.admin');
Route::get('/docs/walkthroughs', function () { return view('docs.walkthroughs'); })->middleware('cache.headers:public;max_age=600;etag')->name('docs.walkthroughs');

// Invite page
// Campaign hub (public)
Route::get('/campaigns', [App\Http\Controllers\CampaignHubController::class, 'index'])
    ->middleware('cache.headers:public;max_age=120;etag')
    ->name('campaigns.hub');

// Campaign receipts
Route::get('/campaigns/{campaign}/donations/{reference}/receipt', [App\Http\Controllers\CampaignReceiptController::class, 'donor'])
    ->name('campaigns.receipts.donor');
Route::get('/campaigns/{campaign}/receipts/organizer.csv', [App\Http\Controllers\CampaignReceiptController::class, 'organizerCsv'])
    ->middleware(['auth','can:admin'])
    ->name('campaigns.receipts.organizer');

// Campaign pledges
Route::post('/campaigns/{campaign}/pledges', [App\Http\Controllers\CampaignPledgeController::class, 'store'])
    ->middleware(['throttle:10,1','antispam'])
    ->name('campaigns.pledges.store');
Route::put('/campaign-pledges/{pledge}', [App\Http\Controllers\CampaignPledgeController::class, 'update'])
    ->middleware('auth')
    ->name('campaigns.pledges.update');
Route::post('/campaign-pledges/{pledge}/cancel', [App\Http\Controllers\CampaignPledgeController::class, 'cancel'])
    ->middleware('auth')
    ->name('campaigns.pledges.cancel');
Route::post('/campaign-pledges/{pledge}/fulfill', [App\Http\Controllers\CampaignPledgeController::class, 'fulfill'])
    ->middleware(['auth','can:admin'])
    ->name('campaigns.pledges.fulfill');
Route::get('/invite', function () {
    return view('invite');
});

// Public SACCO registration
Route::get('/sacco/register', [App\Http\Controllers\SaccoRegistrationController::class, 'create'])->name('sacco.register');
Route::post('/sacco/register', [App\Http\Controllers\SaccoRegistrationController::class, 'store'])->name('sacco.register.store');

// Dashboard (protected route)
Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])
    ->middleware('auth')
    ->name('dashboard');

// Notification center
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [App\Http\Controllers\Notifications\NotificationCenterController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/mark-all-read', [App\Http\Controllers\Notifications\NotificationCenterController::class, 'markAllRead'])->name('notifications.mark_all_read');
    Route::post('/notifications/{id}/read', [App\Http\Controllers\Notifications\NotificationCenterController::class, 'markRead'])->name('notifications.mark_read');
    Route::post('/notifications/bulk', [App\Http\Controllers\Notifications\NotificationCenterController::class, 'bulk'])->name('notifications.bulk');
    // Settings
    Route::get('/settings', [App\Http\Controllers\SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SettingsController::class, 'update'])->name('settings.update');
});

// Login routes
Route::get('/login', function () {
    return view('login');
})->middleware('guest')->name('login');

Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login'])
    ->middleware('throttle:5,1') // 5 attempts per minute
    ->name('login.post');

Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Admin approvals dashboard
Route::get('/admin/approvals', [App\Http\Controllers\AdminDashboardController::class, 'approvals'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.approvals');

// Admin - Payments webhooks dashboard
Route::middleware(['auth','can:admin','log.admin'])
    ->group(function () {
        Route::get('/admin/payments/webhooks', [App\Http\Controllers\Admin\PaymentsWebhookAdminController::class, 'index'])
            ->name('admin.payments.webhooks.index');
        Route::post('/admin/payments/webhooks/{event}/retry', [App\Http\Controllers\Admin\PaymentsWebhookAdminController::class, 'retry'])
            ->name('admin.payments.webhooks.retry');

        // Donations exports
        Route::get('/admin/donations/export', [App\Http\Controllers\Admin\DonationExportController::class, 'index'])
            ->name('admin.donations.export');
        Route::post('/admin/donations/export', [App\Http\Controllers\Admin\DonationExportController::class, 'export'])
            ->name('admin.donations.export.download');

        // Recurring contribution rules
        Route::get('/admin/recurring-rules', [App\Http\Controllers\Admin\RecurringContributionRuleController::class, 'index'])
            ->name('admin.recurring_rules.index');
        Route::get('/admin/recurring-rules/create', [App\Http\Controllers\Admin\RecurringContributionRuleController::class, 'create'])
            ->name('admin.recurring_rules.create');
        Route::post('/admin/recurring-rules', [App\Http\Controllers\Admin\RecurringContributionRuleController::class, 'store'])
            ->name('admin.recurring_rules.store');
        Route::get('/admin/recurring-rules/{rule}/edit', [App\Http\Controllers\Admin\RecurringContributionRuleController::class, 'edit'])
            ->name('admin.recurring_rules.edit');
        Route::put('/admin/recurring-rules/{rule}', [App\Http\Controllers\Admin\RecurringContributionRuleController::class, 'update'])
            ->name('admin.recurring_rules.update');
        Route::delete('/admin/recurring-rules/{rule}', [App\Http\Controllers\Admin\RecurringContributionRuleController::class, 'destroy'])
            ->name('admin.recurring_rules.destroy');

        // Contributions exports and reports
        Route::get('/admin/contributions/export', [App\Http\Controllers\Admin\ContributionReportController::class, 'exportForm'])
            ->name('admin.contributions.export');
        Route::post('/admin/contributions/export', [App\Http\Controllers\Admin\ContributionReportController::class, 'export'])
            ->name('admin.contributions.export.download');
        Route::get('/admin/contributions/summary', [App\Http\Controllers\Admin\ContributionReportController::class, 'summary'])
            ->name('admin.contributions.summary');
        // Admin activity log
        Route::get('/admin/activities', [App\Http\Controllers\Admin\AdminActivityController::class, 'index'])
            ->name('admin.activities.index');
        Route::get('/admin/activities/export', [App\Http\Controllers\Admin\AdminActivityController::class, 'export'])
            ->name('admin.activities.export');
        // Health dashboard
        Route::get('/admin/health', [App\Http\Controllers\Admin\HealthDashboardController::class, 'index'])
            ->name('admin.health.index');
        // Metrics (Prometheus)
        Route::get('/admin/metrics', [App\Http\Controllers\Admin\MetricsController::class, 'index'])
            ->name('admin.metrics');
        // Deploy verification
        Route::get('/admin/deploy/verify', [App\Http\Controllers\Admin\DeployVerificationController::class, 'index'])
            ->name('admin.deploy.verify');
        // SACCO role templates
        Route::get('/admin/sacco-role-templates', [App\Http\Controllers\Admin\SaccoRoleTemplateController::class, 'index'])
            ->name('admin.sacco_role_templates.index');
        Route::get('/admin/sacco-role-templates/{template}', [App\Http\Controllers\Admin\SaccoRoleTemplateController::class, 'show'])
            ->name('admin.sacco_role_templates.show');
        Route::put('/admin/sacco-role-templates/{template}', [App\Http\Controllers\Admin\SaccoRoleTemplateController::class, 'update'])
            ->name('admin.sacco_role_templates.update');
        // Group ledger export
        Route::get('/admin/groups/{group}/ledger', [App\Http\Controllers\Admin\GroupLedgerExportController::class, 'form'])
            ->name('admin.groups.ledger.form');
        Route::get('/admin/groups/{group}/ledger.csv', [App\Http\Controllers\Admin\GroupLedgerExportController::class, 'export'])
            ->name('admin.groups.ledger.export');
        Route::get('/admin/groups/{group}/statement', [App\Http\Controllers\Admin\GroupLedgerExportController::class, 'statement'])
            ->name('admin.groups.statement');
        // Donor wall moderation
        Route::get('/admin/campaigns/{campaign}/donor-wall', [App\Http\Controllers\Admin\DonorWallController::class, 'index'])
            ->name('admin.donor_wall.index');
        Route::post('/admin/donations/{donation}/moderate', [App\Http\Controllers\Admin\DonorWallController::class, 'moderate'])
            ->name('admin.donor_wall.moderate');
        // Permission matrix
        Route::get('/admin/permissions/matrix', [App\Http\Controllers\Admin\PermissionMatrixController::class, 'index'])
            ->name('admin.permissions.matrix');
        Route::put('/admin/roles/{role}/permissions', [App\Http\Controllers\Admin\PermissionMatrixController::class, 'updateRole'])
            ->name('admin.roles.permissions.update');
    });

// User profile
Route::get('/profile', [App\Http\Controllers\UserProfileController::class, 'show'])
    ->middleware('auth')
    ->name('profile.show');
Route::post('/profile', [App\Http\Controllers\UserProfileController::class, 'update'])
    ->middleware('auth')
    ->name('profile.update');

// Password reset routes
Route::get('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'showForgotPasswordForm'])
    ->middleware('guest')
    ->name('password.request');

Route::post('/forgot-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLink'])
    ->middleware('guest')
    ->name('password.email');

Route::get('/reset-password/{token}', [App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])
    ->middleware('guest')
    ->name('password.reset');

Route::post('/reset-password', [App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])
    ->middleware('guest')
    ->name('password.update');

// Tasks
Route::resource('tasks', App\Http\Controllers\TaskController::class)
    ->middleware('auth');

// Task comments & attachments
Route::post('tasks/{task}/comments', [App\Http\Controllers\TaskCommentController::class, 'store'])
    ->middleware('auth')
    ->name('task-comments.store');

Route::post('tasks/{task}/attachments', [App\Http\Controllers\TaskAttachmentController::class, 'store'])
    ->middleware('auth')
    ->name('task-attachments.store');

// Task assignees
Route::post('tasks/{task}/assign', [App\Http\Controllers\TaskController::class, 'assign'])
    ->middleware('auth')
    ->name('tasks.assign');
Route::post('tasks/{task}/unassign/{user}', [App\Http\Controllers\TaskController::class, 'unassign'])
    ->middleware('auth')
    ->name('tasks.unassign');

// Chats
Route::resource('chats', App\Http\Controllers\ChatController::class)
    ->only(['index','create','store','show'])
    ->middleware('auth');
Route::post('chats/{chat}/messages', [App\Http\Controllers\MessageController::class, 'store'])
    ->middleware('auth')
    ->name('messages.store');

// Contributions
Route::resource('contributions', App\Http\Controllers\ContributionController::class)
    ->middleware('auth');
Route::get('contributions/{contribution}/report', [App\Http\Controllers\ContributionController::class, 'report'])
    ->middleware('auth')
    ->name('contributions.report');
Route::get('contributions/{contribution}/export/csv', [App\Http\Controllers\ContributionController::class, 'exportCsv'])
    ->middleware('auth')
    ->name('contributions.export.csv');
Route::post('contributions/{contribution}/participants', [App\Http\Controllers\ContributionController::class, 'addParticipant'])
    ->middleware('auth')
    ->name('contributions.participants.add');
Route::delete('contributions/{contribution}/participants/{user}', [App\Http\Controllers\ContributionController::class, 'removeParticipant'])
    ->middleware('auth')
    ->name('contributions.participants.remove');
Route::post('contributions/{contribution}/payments', [App\Http\Controllers\ContributionPaymentController::class, 'store'])
    ->middleware('auth')
    ->name('contributions.payments.store');

// Pledges
Route::post('contributions/{contribution}/pledges', [App\Http\Controllers\PledgeController::class, 'store'])
    ->middleware('auth')
    ->name('pledges.store');
Route::get('pledges/{pledge}/fulfill', [App\Http\Controllers\PledgeController::class, 'fulfill'])
    ->middleware('auth')
    ->name('pledges.fulfill');

// M-Pesa sandbox endpoints
Route::post('contributions/{contribution}/mpesa/initiate', [App\Http\Controllers\MpesaController::class, 'initiate'])
    ->middleware('auth')
    ->name('contributions.mpesa.initiate');
Route::post('payments/mpesa/callback', [App\Http\Controllers\MpesaController::class, 'callback'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
    ->name('payments.mpesa.callback');

// Bank webhook stub
Route::post('payments/bank/incoming', [App\Http\Controllers\BankWebhookController::class, 'incoming'])
    ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
    ->name('payments.bank.incoming');

// Departments & Roles (admin only, requires 2FA)
Route::resource('departments', App\Http\Controllers\DepartmentController::class)
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class]);
Route::resource('roles', App\Http\Controllers\RoleController::class)
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class]);

// Groups (SACCO-Style)
Route::resource('groups', App\Http\Controllers\GroupController::class)
    ->middleware('auth');
Route::get('groups/discover', [App\Http\Controllers\GroupApplicationController::class, 'index'])
    ->middleware('auth')
    ->name('groups.discover');
Route::get('groups/{group}/apply', [App\Http\Controllers\GroupApplicationController::class, 'create'])
    ->middleware('auth')
    ->name('groups.apply');
Route::post('groups/{group}/apply', [App\Http\Controllers\GroupApplicationController::class, 'store'])
    ->middleware('auth')
    ->name('groups.applications.store');
Route::post('groups/{group}/applications/{application}/approve', [App\Http\Controllers\GroupApplicationController::class, 'approve'])
    ->middleware('auth')
    ->name('groups.applications.approve');
Route::post('groups/{group}/applications/{application}/reject', [App\Http\Controllers\GroupApplicationController::class, 'reject'])
    ->middleware('auth')
    ->name('groups.applications.reject');
Route::post('groups/{group}/members', [App\Http\Controllers\GroupController::class, 'addMember'])
    ->middleware('auth')
    ->name('groups.members.store');
Route::delete('groups/{group}/members/{user}', [App\Http\Controllers\GroupController::class, 'removeMember'])
    ->middleware('auth')
    ->name('groups.members.destroy');
// SACCO Invitations
Route::get('groups/{group}/invite', [App\Http\Controllers\SaccoInvitationController::class, 'create'])
    ->middleware('auth')
    ->name('sacco.invitations.create');
Route::post('groups/{group}/invite', [App\Http\Controllers\SaccoInvitationController::class, 'store'])
    ->middleware(['auth','throttle:10,1'])
    ->name('sacco.invitations.store');
Route::get('invite/accept/{token}', [App\Http\Controllers\SaccoInvitationController::class, 'accept'])
    ->name('sacco.invitations.accept');
Route::get('groups/{group}/report', [App\Http\Controllers\GroupController::class, 'report'])
    ->middleware('auth')
    ->name('groups.report');
Route::post('groups/{group}/expenses', [App\Http\Controllers\GroupExpenseController::class, 'store'])
    ->middleware('auth')
    ->name('group-expenses.store');
Route::post('groups/{group}/expenses/{expense}/approve', [App\Http\Controllers\GroupExpenseController::class, 'approve'])
    ->middleware('auth')
    ->name('group-expenses.approve');
Route::post('groups/{group}/expenses/{expense}/reject', [App\Http\Controllers\GroupExpenseController::class, 'reject'])
    ->middleware('auth')
    ->name('group-expenses.reject');

// Campaigns/Fundraising
Route::get('campaigns', [App\Http\Controllers\CampaignController::class, 'index'])->name('campaigns.index');
Route::resource('campaigns', App\Http\Controllers\CampaignController::class)
    ->only(['create', 'store', 'show', 'update', 'edit'])
    ->middleware('auth');
Route::post('campaigns/{campaign}/donate', [App\Http\Controllers\CampaignController::class, 'donate'])->name('campaigns.donate');
Route::post('campaigns/{campaign}/donations/{donation}/resend', [App\Http\Controllers\CampaignController::class, 'resendDonation'])
    ->name('campaigns.donations.resend');
Route::post('campaigns/{campaign}/updates', [App\Http\Controllers\CampaignController::class, 'addUpdate'])->name('campaigns.updates.store');
Route::get('campaigns/{campaign}/transparency', [App\Http\Controllers\CampaignController::class, 'transparency'])->name('campaigns.transparency');
Route::post('campaigns/{campaign}/expenses', [App\Http\Controllers\CampaignExpenseController::class, 'store'])
    ->middleware('auth')
    ->name('campaign-expenses.store');
Route::delete('campaigns/{campaign}/expenses/{expense}', [App\Http\Controllers\CampaignExpenseController::class, 'destroy'])
    ->middleware('auth')
    ->name('campaign-expenses.destroy');
Route::get('admin/campaign-approvals', [App\Http\Controllers\Admin\CampaignApprovalController::class, 'index'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.campaign-approvals');
Route::post('admin/campaigns/{campaign}/approve', [App\Http\Controllers\Admin\CampaignApprovalController::class, 'approve'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.campaigns.approve');
Route::post('admin/campaigns/{campaign}/reject', [App\Http\Controllers\Admin\CampaignApprovalController::class, 'reject'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.campaigns.reject');

// Admin: SACCO Rules management
Route::get('admin/sacco-rules', [App\Http\Controllers\Admin\SaccoRuleController::class, 'index'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.sacco-rules.index');
Route::post('admin/sacco-rules', [App\Http\Controllers\Admin\SaccoRuleController::class, 'store'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.sacco-rules.store');
Route::delete('admin/sacco-rules/{rule}', [App\Http\Controllers\Admin\SaccoRuleController::class, 'destroy'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.sacco-rules.destroy');

// Admin: SACCO Registrations review
Route::get('admin/sacco-registrations', [App\Http\Controllers\SaccoRegistrationController::class, 'adminIndex'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.sacco-registrations.index');
Route::post('admin/sacco-registrations/{registration}/approve', [App\Http\Controllers\SaccoRegistrationController::class, 'approve'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.sacco-registrations.approve');
Route::post('admin/sacco-registrations/{registration}/reject', [App\Http\Controllers\SaccoRegistrationController::class, 'reject'])
    ->middleware(['auth', App\Http\Middleware\RequireTwoFactor::class])
    ->name('admin.sacco-registrations.reject');

// Notifications
Route::get('notifications', [App\Http\Controllers\NotificationPreferenceController::class, 'index'])
    ->middleware('auth')
    ->name('notifications.index');
Route::put('notifications/preferences', [App\Http\Controllers\NotificationPreferenceController::class, 'update'])
    ->middleware('auth')
    ->name('notifications.preferences.update');
Route::post('notifications/mark-all-read', [App\Http\Controllers\NotificationPreferenceController::class, 'markAllRead'])
    ->middleware('auth')
    ->name('notifications.mark-all-read');

// Meetings CRUD
Route::resource('meetings', App\Http\Controllers\MeetingController::class)
    ->middleware('auth');
Route::post('meetings/{meeting}/password', [App\Http\Controllers\MeetingPasswordController::class, 'verify'])
    ->middleware('auth')
    ->name('meetings.password.verify');
Route::get('meetings-calendar', [App\Http\Controllers\MeetingController::class, 'calendar'])
    ->middleware('auth')
    ->name('meetings.calendar');
Route::get('meetings-feed', [App\Http\Controllers\MeetingController::class, 'feed'])
    ->middleware('auth')
    ->name('meetings.feed');
// User iCal feed (secret token)
Route::get('calendar/ical/{token}', [App\Http\Controllers\MeetingCalendarFeedController::class, 'feed'])
    ->name('calendar.ical');
Route::get('meetings/{meeting}.ics', [App\Http\Controllers\MeetingController::class, 'ics'])
    ->middleware('auth')
    ->name('meetings.ics');

// Meeting room and signaling
Route::get('meetings/{meeting}/room', [App\Http\Controllers\MeetingController::class, 'show'])
    ->middleware('auth')
    ->name('meetings.room');
Route::post('meetings/{meeting}/signal', [App\Http\Controllers\MeetingSignalController::class, 'send'])
    ->middleware('auth')
    ->name('meetings.signal');

// Janus SFU endpoints
Route::get('meetings/{meeting}/janus-session', [App\Http\Controllers\JanusController::class, 'session'])
    ->middleware('auth')
    ->name('meetings.janus.session');

// Breakout rooms (beta)
Route::get('meetings/{meeting}/breakout-rooms', [App\Http\Controllers\BreakoutRoomController::class, 'index'])
    ->middleware('auth')
    ->name('meetings.breakouts.index');
Route::post('meetings/{meeting}/breakout-rooms', [App\Http\Controllers\BreakoutRoomController::class, 'store'])
    ->middleware('auth')
    ->name('meetings.breakouts.store');
Route::delete('meetings/{meeting}/breakout-rooms/{room}', [App\Http\Controllers\BreakoutRoomController::class, 'destroy'])
    ->middleware('auth')
    ->name('meetings.breakouts.destroy');

// Meeting controls (host only)
Route::post('meetings/{meeting}/control', [App\Http\Controllers\MeetingControlController::class, 'control'])
    ->middleware('auth')
    ->name('meetings.control');

// Meeting recordings
Route::post('meetings/{meeting}/recordings', [App\Http\Controllers\MeetingRecordingController::class, 'store'])
    ->middleware('auth')
    ->name('meetings.recordings.store');
Route::post('meetings/{meeting}/transcripts', [App\Http\Controllers\MeetingTranscriptController::class, 'store'])
    ->middleware('auth')
    ->name('meetings.transcripts.store');
Route::get('meetings/{meeting}/transcripts/{transcript}.txt', [App\Http\Controllers\MeetingTranscriptController::class, 'downloadTxt'])
    ->middleware('auth')
    ->name('meetings.transcripts.download.txt');
Route::get('meetings/{meeting}/transcripts/{transcript}/print', [App\Http\Controllers\MeetingTranscriptController::class, 'printView'])
    ->middleware('auth')
    ->name('meetings.transcripts.print');

// Meeting join/leave
Route::post('meetings/{meeting}/join', [App\Http\Controllers\MeetingParticipationController::class, 'join'])
    ->middleware('auth')
    ->name('meetings.join');
Route::post('meetings/{meeting}/leave', [App\Http\Controllers\MeetingParticipationController::class, 'leave'])
    ->middleware('auth')
    ->name('meetings.leave');
// Simple health endpoint used by Docker healthchecks
Route::get('/healthz', function () {
    $ok = true;
    $dbMsg = 'ok';
    try {
        // attempt a lightweight DB connection check
        if (app()->bound('db')) {
            \DB::connection()->getPdo();
        }
    } catch (\Exception $e) {
        $ok = false;
        $dbMsg = $e->getMessage();
    }

    return response()->json([
        'status' => $ok ? 'ok' : 'error',
        'db' => $dbMsg,
    ], $ok ? 200 : 503);
});

// Two-Factor Authentication
Route::prefix('2fa')->middleware('auth')->group(function () {
    Route::get('/', [App\Http\Controllers\TwoFactorAuthController::class, 'show'])->name('2fa.show');
    Route::get('/verify', [App\Http\Controllers\TwoFactorAuthController::class, 'showVerify'])->name('2fa.verify-page');
    Route::post('/enable', [App\Http\Controllers\TwoFactorAuthController::class, 'enable'])->name('2fa.enable');
    Route::post('/disable', [App\Http\Controllers\TwoFactorAuthController::class, 'disable'])->name('2fa.disable');
    Route::post('/send-code', [App\Http\Controllers\TwoFactorAuthController::class, 'sendCode'])->name('2fa.send-code');
    Route::post('/verify', [App\Http\Controllers\TwoFactorAuthController::class, 'verify'])->name('2fa.verify');
    Route::post('/regenerate-backup-codes', [App\Http\Controllers\TwoFactorAuthController::class, 'regenerateBackupCodes'])->name('2fa.regenerate-backup-codes');
});

// Phase 1: Auth & Verification routes
Route::prefix('auth')->group(function () {
    // For JSON API-style auth endpoints we disable the CSRF middleware to
    // simplify automated testing and client integrations. These endpoints
    // remain protected by auth or token logic as appropriate.
    Route::post('invite', [App\Http\Controllers\Auth\InviteController::class, 'invite'])
        ->middleware('auth')
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

    Route::get('register/{token}', [App\Http\Controllers\Auth\RegistrationController::class, 'show'])
        ->middleware('guest')
        ->name('register.show');
    
    Route::get('register/available-saccos', [App\Http\Controllers\Auth\RegistrationController::class, 'getAvailableSaccos'])
        ->name('register.available-saccos');
    
    Route::post('register/{token}', [App\Http\Controllers\Auth\RegistrationController::class, 'register'])
        ->middleware('guest')
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class)
        ->name('register.store');

    Route::post('admin/approve/{user}', [App\Http\Controllers\AdminApprovalController::class, 'approve'])
        ->middleware('auth')
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

    Route::post('admin/reject/{user}', [App\Http\Controllers\AdminApprovalController::class, 'reject'])
        ->middleware('auth')
        ->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
});
