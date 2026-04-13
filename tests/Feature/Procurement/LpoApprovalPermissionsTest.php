<?php

namespace Tests\Feature\Procurement;

use App\Models\LocalPurchaseOrder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class LpoApprovalPermissionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_manager_cannot_approve_or_reject_lpos(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $storeManager = $this->makeUser('store_manager');
        $creator = $this->makeUser('store_keeper');
        $lpo = $this->makePendingLpo($creator->id);

        $this->actingAs($storeManager)
            ->post(route('procurement.lpo.approve', $lpo))
            ->assertRedirect(route('dashboard'));

        $this->actingAs($storeManager)
            ->post(route('procurement.lpo.reject', $lpo), ['rejection_reason' => 'Missing pricing support'])
            ->assertRedirect(route('dashboard'));

        $this->assertDatabaseHas('local_purchase_orders', [
            'id' => $lpo->id,
            'status' => 'pending_approval',
        ]);
    }

    public function test_manager_can_approve_and_reject_lpos(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $manager = $this->makeUser('manager');
        $creator = $this->makeUser('store_manager');
        $approvable = $this->makePendingLpo($creator->id);
        $rejectable = $this->makePendingLpo($creator->id, now()->subDay()->toDateString());

        $this->actingAs($manager)
            ->post(route('procurement.lpo.approve', $approvable))
            ->assertRedirect();

        $this->actingAs($manager)
            ->post(route('procurement.lpo.reject', $rejectable), ['rejection_reason' => 'Budget not approved'])
            ->assertRedirect();

        $this->assertDatabaseHas('local_purchase_orders', [
            'id' => $approvable->id,
            'status' => 'approved',
            'approved_by' => $manager->id,
        ]);

        $this->assertDatabaseHas('local_purchase_orders', [
            'id' => $rejectable->id,
            'status' => 'rejected',
            'rejected_by' => $manager->id,
            'rejection_reason' => 'Budget not approved',
        ]);
    }

    public function test_manager_oversight_views_are_restricted_and_render(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $manager = $this->makeUser('manager');
        $storeManager = $this->makeUser('store_manager');

        $this->actingAs($manager)->get(route('manager.procurement.approvals'))->assertOk();
        $this->actingAs($manager)->get(route('manager.stock.overview'))->assertOk();
        $this->actingAs($manager)->get(route('manager.stock.movements'))->assertOk();

        $this->actingAs($storeManager)->get(route('manager.procurement.approvals'))->assertRedirect(route('dashboard'));
    }

    public function test_store_manager_lpo_view_hides_approval_actions_but_manager_sees_them(): void
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);

        $storeManager = $this->makeUser('store_manager');
        $manager = $this->makeUser('manager');
        $creator = $this->makeUser('store_keeper');
        $lpo = $this->makePendingLpo($creator->id);

        $this->actingAs($storeManager)
            ->get(route('procurement.lpo.show', $lpo))
            ->assertOk()
            ->assertDontSee('Approve')
            ->assertDontSee('Reject');

        $this->actingAs($manager)
            ->get(route('procurement.lpo.show', $lpo))
            ->assertOk()
            ->assertSee('Approve')
            ->assertSee('Reject');
    }

    private function makeUser(string $roleName): User
    {
        $role = Role::where('name', $roleName)->firstOrFail();

        return User::factory()->create([
            'role_id' => $role->id,
            'is_active' => true,
        ]);
    }

    private function makePendingLpo(string $creatorId, ?string $orderDate = null): LocalPurchaseOrder
    {
        return LocalPurchaseOrder::create([
            'order_date' => $orderDate ?? now()->toDateString(),
            'status' => 'pending_approval',
            'created_by' => $creatorId,
        ]);
    }
}
