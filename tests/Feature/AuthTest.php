<?php

namespace Tests\Feature;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_succeeds_with_valid_credentials(): void
    {
        $user = User::factory()->create([
            'email' => 'glenda@test.com',
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => 'glenda@test.com',
            'password' => 'password123',
        ])->assertRedirect('/dashboard');

        $this->assertAuthenticatedAs($user);
    }

    public function test_login_fails_with_wrong_password(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->from('/login')->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ])->assertRedirect('/login')
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_fails_with_unknown_email(): void
    {
        $this->post('/login', [
            'email' => 'nobody@test.com',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_inactive_users_cannot_log_in(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
            'is_active' => false,
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_login_requires_email_and_password(): void
    {
        $this->post('/login', [])
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertGuest();
    }

    public function test_login_writes_audit_entry(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'password123',
        ]);

        $log = AuditLog::query()
            ->where('action', 'LOGIN')
            ->where('affected_table', 'users')
            ->where('record_id', $user->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logout_redirects_to_login_and_audits(): void
    {
        $user = $this->signIn();

        $this->post('/logout')->assertRedirect('/');

        $this->assertGuest();

        $log = AuditLog::query()
            ->where('action', 'LOGOUT')
            ->where('affected_table', 'users')
            ->where('record_id', $user->id)
            ->first();

        $this->assertNotNull($log);
    }

    public function test_logout_requires_authentication(): void
    {
        $this->post('/logout')->assertRedirect('/');
    }
}
