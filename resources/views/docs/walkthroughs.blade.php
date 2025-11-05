@extends('layouts.app')

@section('content')
<div class="container py-4">
  <h1 class="h4 mb-4">Video Walkthroughs</h1>

  <div class="row g-4">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">For Organizers</div>
        <div class="card-body">
          <h5 class="card-title">Getting Started</h5>
          <p class="card-text">Learn how to create and manage campaigns, track donations, and engage with donors.</p>
          <div class="ratio ratio-16x9 mb-3">
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Organizer Walkthrough" allowfullscreen></iframe>
          </div>
          <h6 class="mt-3">Topics Covered:</h6>
          <ul>
            <li>Creating your first campaign</li>
            <li>Setting goals and milestones</li>
            <li>Managing donations and receipts</li>
            <li>Donor wall and moderation</li>
            <li>Campaign updates and engagement</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">For Treasurers</div>
        <div class="card-body">
          <h5 class="card-title">Financial Management</h5>
          <p class="card-text">Master contribution tracking, recurring rules, expense management, and reporting.</p>
          <div class="ratio ratio-16x9 mb-3">
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Treasurer Walkthrough" allowfullscreen></iframe>
          </div>
          <h6 class="mt-3">Topics Covered:</h6>
          <ul>
            <li>Setting up recurring contributions</li>
            <li>Tracking payments and expenses</li>
            <li>Generating monthly statements</li>
            <li>Exporting ledger data</li>
            <li>Managing member contributions</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">For Group Admins</div>
        <div class="card-body">
          <h5 class="card-title">SACCO Management</h5>
          <p class="card-text">Learn how to manage your SACCO or group, invite members, assign roles, and set quotas.</p>
          <div class="ratio ratio-16x9 mb-3">
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Group Admin Walkthrough" allowfullscreen></iframe>
          </div>
          <h6 class="mt-3">Topics Covered:</h6>
          <ul>
            <li>Member invitations and management</li>
            <li>Role templates and permissions</li>
            <li>Member quota configuration</li>
            <li>Group financial tracking</li>
          </ul>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <div class="card-header">Meeting Management</div>
        <div class="card-body">
          <h5 class="card-title">Video Conferencing</h5>
          <p class="card-text">Host and manage meetings, use waiting rooms, recordings, and transcripts.</p>
          <div class="ratio ratio-16x9 mb-3">
            <iframe src="https://www.youtube.com/embed/dQw4w9WgXcQ" title="Meeting Walkthrough" allowfullscreen></iframe>
          </div>
          <h6 class="mt-3">Topics Covered:</h6>
          <ul>
            <li>Scheduling and calendar integration</li>
            <li>Host controls and waiting rooms</li>
            <li>Recording and transcript management</li>
            <li>Meeting settings and security</li>
          </ul>
        </div>
      </div>
    </div>
  </div>

  <div class="mt-4 alert alert-info">
    <strong>Note:</strong> Replace the placeholder YouTube embed URLs with your actual walkthrough videos.
  </div>
</div>
@endsection

