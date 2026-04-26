<?php

namespace Tests\Feature\Store;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class StoreDamageReportAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_keeper_can_view_damage_report(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $storeKeeper = $this->makeUser('store_keeper');

        $this->actingAs($storeKeeper)
            ->get(route('store.reports.damage'))
            ->assertOk();
    }

    private function makeUser(string $roleName): User
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }
}
