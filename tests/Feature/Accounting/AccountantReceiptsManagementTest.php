<?php

namespace Tests\Feature\Accounting;

use App\Models\Receipt;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class AccountantReceiptsManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_receipts_index_loads_and_filters_by_module_and_receipt_number(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $accountant = $this->makeUser('ACCOUNTANT');

        $restaurant = Receipt::create([
            'module' => 'restaurant',
            'receipt_number' => 'HMS-2026-111111',
            'payment_method' => 'cash',
            'payment_status' => 'paid',
            'total' => 50000,
            'subtotal' => 50000,
            'amount_paid' => 50000,
            'balance' => 0,
            'currency' => 'TZS',
            'issued_at' => now(),
        ]);

        Receipt::create([
            'module' => 'laundry',
            'receipt_number' => 'HMS-2026-222222',
            'payment_method' => 'card',
            'payment_status' => 'unpaid',
            'total' => 25000,
            'subtotal' => 25000,
            'amount_paid' => 0,
            'balance' => 25000,
            'currency' => 'TZS',
            'issued_at' => now()->subDay(),
        ]);

        $this->actingAs($accountant)
            ->get(route('accountant.receipts.index', [
                'module' => 'restaurant',
                'receipt_number' => '111111',
            ]))
            ->assertOk()
            ->assertSee($restaurant->receipt_number)
            ->assertDontSee('HMS-2026-222222');
    }

    public function test_non_accountant_cannot_access_receipts_center(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $frontDesk = $this->makeUser('front_desk');

        $this->actingAs($frontDesk)
            ->get(route('accountant.receipts.index'))
            ->assertRedirect(route('dashboard'));
    }

    public function test_reprint_keeps_same_receipt_number_for_accountant(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $accountant = $this->makeUser('ACCOUNTANT');

        $receipt = Receipt::create([
            'module' => 'checkout',
            'receipt_number' => 'HMS-2026-333333',
            'payment_status' => 'paid',
            'subtotal' => 10000,
            'total' => 10000,
            'amount_paid' => 10000,
            'balance' => 0,
            'currency' => 'TZS',
            'issued_at' => now(),
        ]);

        $this->actingAs($accountant)
            ->get(route('receipts.reprint', $receipt->receipt_number))
            ->assertOk()
            ->assertSee($receipt->receipt_number);

        $this->assertDatabaseHas('receipts', [
            'id' => $receipt->id,
            'receipt_number' => 'HMS-2026-333333',
        ]);
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

