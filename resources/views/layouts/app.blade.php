<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'ChamaConnect'))</title>
    <link rel="icon" type="image/svg+xml" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    @yield('head')
    <link rel="alternate icon" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <link rel="apple-touch-icon" sizes="180x180" href="{{ url('brand/chamaconnect-logo.svg') }}?v=3">
    <meta name="theme-color" content="#0F766E">
    <style>
        /* A11y: Ensure sufficient color contrast */
        .text-muted { color: #6c757d !important; }
        .btn-outline-secondary { border-color: #6c757d; color: #6c757d; }
        .btn-outline-secondary:hover { background-color: #6c757d; color: #fff; }
        .badge { color: #fff !important; }
    </style>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" defer></script>
</head>
<body>
<a href="#main" class="visually-hidden-focusable position-absolute top-0 start-0 m-2 px-3 py-2 bg-light border rounded">Skip to main content</a>
<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center gap-2" href="/">
        <img src="{{ asset('brand/chamaconnect-logo.svg') }}" alt="ChamaConnect" class="d-inline-block" style="width: 40px; height: 40px;"/>
        <span class="fw-bold">ChamaConnect</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav" aria-controls="nav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="nav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('tasks.index') }}">Tasks</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('chats.index') }}">Chats</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('contributions.index') }}">Contributions</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('groups.index') }}">Groups</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('campaigns.index') }}">Campaigns</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('meetings.index') }}">Meetings</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('notifications.index') }}">Notifications @auth<span class="badge bg-danger">{{ auth()->user()->unreadNotifications()->count() }}</span>@endauth</a></li>
        @can('admin')
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">Admin</a>
          <ul class="dropdown-menu">
            <li><a class="dropdown-item" href="{{ route('admin.health.index') }}">Health Dashboard</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.metrics') }}">Metrics</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.deploy.verify') }}">Deploy Verify</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.activities.index') }}">Activity Log</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.payments.webhooks.index') }}">Webhooks</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.donations.export') }}">Donations Export</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.recurring_rules.index') }}">Recurring Rules</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.sacco_role_templates.index') }}">SACCO Role Templates</a></li>
            <li><a class="dropdown-item" href="{{ route('admin.permissions.matrix') }}">Permission Matrix</a></li>
            <li><hr class="dropdown-divider"></li>
            <li><a class="dropdown-item" href="{{ route('departments.index') }}">Departments</a></li>
            <li><a class="dropdown-item" href="{{ route('roles.index') }}">Roles</a></li>
          </ul>
        </li>
        @endcan
      </ul>
      <ul class="navbar-nav ms-auto">
        @auth
        <li class="nav-item"><a class="nav-link" href="{{ route('profile.show') }}">Profile</a></li>
        <li class="nav-item"><a class="nav-link" href="{{ route('settings.index') }}">Settings</a></li>
        <li class="nav-item">
          <form action="{{ route('logout') }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-link nav-link">Logout</button>
          </form>
        </li>
        @else
        <li class="nav-item"><a class="nav-link" href="{{ route('login') }}">Login</a></li>
        @endauth
      </ul>
    </div>
  </div>
</nav>

<main id="main" tabindex="-1">
  @yield('content')
  @yield('scripts')
</main>
</body>
</html>


