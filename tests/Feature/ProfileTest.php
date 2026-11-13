<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_name_and_email(): void
    {
        $this->seed();
        $user = User::first();

        $response = $this->actingAs($user)->put('/profile', [
            'name' => 'Richad Baru',
            'email' => 'richad.baru@test.com',
            'phone' => '081234567890',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertEquals('Richad Baru', $user->name);
        $this->assertEquals('richad.baru@test.com', $user->email);
    }

    public function test_user_can_change_password(): void
    {
        $this->seed();
        $user = User::first();

        $response = $this->actingAs($user)->put('/profile/password', [
            'current_password' => 'richad ganteng',
            'password' => 'passwordBaru123',
            'password_confirmation' => 'passwordBaru123',
        ]);

        $response->assertSessionHas('success');
        $user->refresh();
        $this->assertTrue(Hash::check('passwordBaru123', $user->password));
    }
}
