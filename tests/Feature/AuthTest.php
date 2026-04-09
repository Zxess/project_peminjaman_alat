<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test login page displays correctly
     */
    public function test_login_page_displays(): void
    {
        $response = $this->get('/login');
        $response->assertStatus(200);
        $response->assertViewIs('auth.login');
    }

    /**
     * Test successful login
     */
    public function test_successful_login(): void
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
            'role' => 'peminjam'
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'password123',
        ]);

        $this->assertAuthenticatedAs($user);
        $response->assertRedirect('/peminjam/dashboard');
    }

    /**
     * Test failed login with wrong password
     */
    public function test_failed_login_wrong_password(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password123'),
        ]);

        $response = $this->post('/login', [
            'email' => 'test@example.com',
            'password' => 'wrongpassword',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test failed login with non-existent email
     */
    public function test_failed_login_non_existent_email(): void
    {
        $response = $this->post('/login', [
            'email' => 'nonexistent@example.com',
            'password' => 'password123',
        ]);

        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }

    /**
     * Test successful registration
     */
    public function test_successful_registration(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123456',
            'password_confirmation' => 'password123456',
        ]);

        $this->assertDatabaseHas('users', [
            'email' => 'john@example.com',
            'name' => 'John Doe',
            'role' => 'peminjam'
        ]);

        $response->assertRedirect('/login');
    }

    /**
     * Test registration with duplicate email
     */
    public function test_registration_duplicate_email(): void
    {
        User::factory()->create([
            'email' => 'duplicate@example.com'
        ]);

        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'duplicate@example.com',
            'password' => 'password123456',
            'password_confirmation' => 'password123456',
        ]);

        $response->assertSessionHasErrors('email');
    }

    /**
     * Test registration with password mismatch
     */
    public function test_registration_password_mismatch(): void
    {
        $response = $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123456',
            'password_confirmation' => 'different123456',
        ]);

        $response->assertSessionHasErrors('password');
    }

    /**
     * Test logout functionality
     */
    public function test_logout(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/login');
    }

    /**
     * Test admin redirect on login
     */
    public function test_admin_redirect_on_login(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => bcrypt('password123'),
            'role' => 'admin'
        ]);

        $response = $this->post('/login', [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/admin/dashboard');
    }

    /**
     * Test petugas redirect on login
     */
    public function test_petugas_redirect_on_login(): void
    {
        $petugas = User::factory()->create([
            'email' => 'petugas@example.com',
            'password' => bcrypt('password123'),
            'role' => 'petugas'
        ]);

        $response = $this->post('/login', [
            'email' => 'petugas@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect('/petugas/dashboard');
    }
}
