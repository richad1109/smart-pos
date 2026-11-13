<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_login_with_richadganteng_and_richad_ganteng(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'richadganteng',
            'password' => 'richad ganteng',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }

    public function test_admin_can_login_with_richadganteng_without_spaces(): void
    {
        $this->seed();

        $response = $this->post('/login', [
            'email' => 'richadganteng',
            'password' => 'richadganteng',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticated();
    }
}
