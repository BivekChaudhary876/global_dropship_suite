<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** A guest is redirected to login when trying to reach a protected route. */
    public function test_guest_is_redirected_from_protected_route(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }

    /** A guest cannot reach the admin-only suppliers area either. */
    public function test_guest_is_redirected_from_admin_route(): void
    {
        $response = $this->get('/suppliers');

        $response->assertRedirect('/login');
    }
}