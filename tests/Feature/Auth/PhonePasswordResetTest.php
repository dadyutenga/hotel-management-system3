<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PhonePasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_reset_with_phone_and_user_must_change_password(): void
    {
        $user = User::factory()->create([
            'phone' => '+255712345678',
            'must_change_password' => false,
        ]);

        $oldPasswordHash = $user->password;

        $response = $this->post('/forgot-password', [
            'phone' => '0712345678',
        ]);

        $response->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertTrue($user->must_change_password);
        $this->assertNotNull($user->password_reset_requested_at);
        $this->assertNotNull($user->password_reset_completed_at);
        $this->assertSame('+255712345678', $user->password_reset_phone);
        $this->assertNotSame($oldPasswordHash, $user->password);
        $this->assertFalse(Hash::check('password', $user->password));
    }

    public function test_phone_reset_is_throttled_with_safe_response(): void
    {
        $user = User::factory()->create([
            'phone' => '+255700000001',
        ]);

        $this->post('/forgot-password', [
            'phone' => '+255700000001',
        ])->assertSessionHasNoErrors();

        $firstCompletedAt = $user->fresh()->password_reset_completed_at;

        $this->post('/forgot-password', [
            'phone' => '+255700000001',
        ])->assertSessionHasNoErrors();

        $user->refresh();

        $this->assertTrue($user->password_reset_completed_at?->equalTo($firstCompletedAt));
    }

}

