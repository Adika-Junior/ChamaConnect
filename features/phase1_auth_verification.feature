Feature: Phase 1 - Authentication and Admin Verification
  In order to allow only verified institutional users access
  As an Administrator
  I want to invite users by institutional email, allow them to register, and approve or reject them

  Background:
    Given the system is running
    And an admin user exists with email "admin@example.com"

  Scenario: Admin invites a new institutional user
    Given I am logged in as an admin
    When I create an invitation for "alice@institution.ac.ke"
    Then the system should create an invitation token valid for 48 hours
    And the system should send an email to "alice@institution.ac.ke" with a registration link

  Scenario: Invitee completes registration within validity and awaits approval
    Given an invitation exists for "alice@institution.ac.ke"
    When the invitee visits the registration link and completes the profile with password "Str0ngP@ssw0rd!"
    Then the user's account status should be "pending"
    And an admin should see the pending verification in the admin dashboard

  Scenario: Admin approves a pending user
    Given a pending user exists with email "alice@institution.ac.ke"
    When the admin approves the user
    Then the user's status should become "active"
    And the user should receive a confirmation email

  Scenario: Invitation token expiry
    Given an invitation was created more than 48 hours ago
    When the invitee attempts to use the registration link
    Then the system should reject the registration and display "Invitation expired"

  Scenario: Admin-only user creation enforcement
    Given I am logged in as a non-admin user
    When I attempt to create a user via the UI or API
    Then the operation must be rejected with HTTP 403
