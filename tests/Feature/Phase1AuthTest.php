<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\EmailVerificationToken;

class Phase1AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_invite_user()
    {
        $admin = User::factory()->create(['status' => 'active']);
        $this->actingAs($admin);

        $response = $this->postJson('/auth/invite', ['email' => 'alice@institution.ac.ke']);
    // TEMP DEBUG: write response to a log file so we can inspect it after the test
    @file_put_contents(storage_path('logs/test-response.log'), "RESPONSE BODY: " . $response->getContent() . "\n", FILE_APPEND);
    $response->assertStatus(201);

        $this->assertDatabaseHas('email_verification_tokens', ['email' => 'alice@institution.ac.ke']);
    }

    public function test_invitee_can_register_with_valid_token()
    {
        $evt = EmailVerificationToken::factory()->create(['expires_at' => now()->addHours(48)]);

        $response = $this->postJson('/auth/register/' . $evt->token, [
            'name' => 'Alice',
            'password' => 'Str0ngP@ssw0rd!',
            'password_confirmation' => 'Str0ngP@ssw0rd!',
            'phone' => '254712345678'
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['email' => $evt->email, 'status' => 'pending']);
    }

    public function test_admin_can_approve_user()
    {
        $admin = User::factory()->create(['status' => 'active']);
        $user = User::factory()->create(['status' => 'pending']);

        $this->actingAs($admin);
        $response = $this->postJson('/auth/admin/approve/' . $user->id);
        $response->assertStatus(200);

        $this->assertDatabaseHas('users', ['id' => $user->id, 'status' => 'active']);
    }
}
