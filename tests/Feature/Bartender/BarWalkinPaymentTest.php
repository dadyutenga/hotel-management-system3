<?php

namespace Tests\Feature\Bartender;

use App\Models\FinancePayment;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\MenuItemIngredient;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Receipt;
use App\Models\StockLevel;
use App\Models\StockLocation;
use App\Models\StockMovement;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class BarWalkinPaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_bar_walkin_cash_payment_settles_order_deducts_stock_once_and_creates_receipt(): void
    {
        $bartender = $this->createUserWithRole('bar_tender');
        $this->actingAs($bartender);

        $order = $this->createPreparedBarWalkinOrder($bartender, ingredientQtyPerItem: 2, itemQty: 2, availableQty: 10);

        $response = $this->postJson(route('finance.walkin-payment.process'), [
            'order_id' => $order->id,
            'module' => 'bar',
            'amount' => 1,
            'customer_name' => 'Walkin One',
            'customer_phone' => '+255700000001',
            'payment_method' => 'cash',
            'payment_reference' => 'CASH-001',
        ]);

        $response->assertOk()->assertJson(['success' => true]);

        $order->refresh();
        $this->assertSame('settled', $order->status);
        $this->assertSame('served', $order->bartender_status);
        $this->assertSame('cash', $order->payment_method);
        $this->assertSame('CASH-001', $order->payment_reference);
        $this->assertNotNull($order->stock_deducted_at);

        $this->assertDatabaseHas('finance_payments', [
            'order_id' => $order->id,
            'payment_type' => 'walkin',
            'method' => 'cash',
            'status' => 'completed',
            'reference' => 'CASH-001',
        ]);

        $this->assertSame(1, Receipt::where('receiptable_type', Order::class)
            ->where('receiptable_id', $order->id)
            ->count());

        $recipeUseCount = StockMovement::where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'recipe_use')
            ->count();
        $this->assertSame(1, $recipeUseCount);

        $secondAttempt = $this->postJson(route('finance.walkin-payment.process'), [
            'order_id' => $order->id,
            'module' => 'bar',
            'amount' => 1,
            'customer_name' => 'Walkin One',
            'customer_phone' => '+255700000001',
            'payment_method' => 'cash',
            'payment_reference' => 'CASH-RETRY',
        ]);

        $secondAttempt->assertStatus(422);

        $this->assertSame(1, StockMovement::where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'recipe_use')
            ->count());
    }

    public function test_bar_walkin_payment_is_blocked_when_stock_is_insufficient(): void
    {
        $bartender = $this->createUserWithRole('bar_tender');
        $this->actingAs($bartender);

        $order = $this->createPreparedBarWalkinOrder($bartender, ingredientQtyPerItem: 3, itemQty: 2, availableQty: 4);

        $response = $this->postJson(route('finance.walkin-payment.process'), [
            'order_id' => $order->id,
            'module' => 'bar',
            'amount' => 1,
            'customer_name' => 'Walkin Two',
            'customer_phone' => '+255700000002',
            'payment_method' => 'cash',
        ]);

        $response->assertStatus(422);
        $this->assertStringContainsString('Insufficient stock', $response->json('message'));

        $order->refresh();
        $this->assertSame('open', $order->status);
        $this->assertNull($order->stock_deducted_at);

        $this->assertDatabaseMissing('finance_payments', ['order_id' => $order->id]);
        $this->assertSame(0, StockMovement::where('reference_type', 'order')
            ->where('reference_id', $order->id)
            ->where('type', 'recipe_use')
            ->count());
    }

    public function test_bar_receipt_reprint_uses_same_receipt_number_and_non_bar_role_is_blocked(): void
    {
        $bartender = $this->createUserWithRole('bar_tender');
        $this->actingAs($bartender);

        $order = $this->createPreparedBarWalkinOrder($bartender, ingredientQtyPerItem: 1, itemQty: 1, availableQty: 10);

        $this->postJson(route('finance.walkin-payment.process'), [
            'order_id' => $order->id,
            'module' => 'bar',
            'amount' => 1,
            'customer_name' => 'Walkin Three',
            'customer_phone' => '+255700000003',
            'payment_method' => 'cash',
        ])->assertOk();

        $receipt = Receipt::where('receiptable_type', Order::class)
            ->where('receiptable_id', $order->id)
            ->firstOrFail();

        $this->get(route('receipts.order', $order))->assertOk();
        $this->get(route('receipts.reprint', $receipt->receipt_number))->assertOk();

        $this->assertSame(1, Receipt::where('receiptable_type', Order::class)
            ->where('receiptable_id', $order->id)
            ->count());

        $receipt->refresh();
        $this->assertSame($receipt->receipt_number, Receipt::where('receiptable_type', Order::class)
            ->where('receiptable_id', $order->id)
            ->value('receipt_number'));

        $frontDesk = $this->createUserWithRole('front_desk');
        $this->actingAs($frontDesk);

        $blocked = $this->postJson(route('finance.walkin-payment.process'), [
            'order_id' => $order->id,
            'module' => 'bar',
            'amount' => 1,
            'customer_name' => 'Blocked User',
            'payment_method' => 'cash',
        ]);

        $blocked->assertStatus(403);
    }

    protected function createPreparedBarWalkinOrder(User $actor, float $ingredientQtyPerItem, int $itemQty, float $availableQty): Order
    {
        $bar = StockLocation::create([
            'name' => 'Bar',
            'code' => 'bar',
            'description' => 'Bar location',
            'is_active' => true,
        ]);

        $product = Product::create([
            'name' => 'Gin',
            'sku' => 'GIN-' . Str::upper(Str::random(6)),
            'description' => 'Gin bottle',
            'category' => 'drinks',
            'unit' => 'bottle',
            'cost_price' => 10000,
            'selling_price' => 15000,
            'reorder_level' => 1,
            'is_active' => true,
            'created_by' => $actor->id,
        ]);

        StockLevel::where('product_id', $product->id)
            ->where('location_id', $bar->id)
            ->update(['quantity' => $availableQty, 'reserved_qty' => 0]);

        $category = MenuCategory::create([
            'name' => 'Cocktails',
            'location_id' => $bar->id,
            'description' => 'Cocktails',
            'is_active' => true,
        ]);

        $menuItem = MenuItem::create([
            'category_id' => $category->id,
            'name' => 'Gin Tonic',
            'description' => 'Classic',
            'selling_price' => 15000,
            'is_available' => true,
            'is_active' => true,
            'created_by' => $actor->id,
        ]);

        MenuItemIngredient::create([
            'menu_item_id' => $menuItem->id,
            'product_id' => $product->id,
            'quantity' => $ingredientQtyPerItem,
            'unit' => 'bottle',
        ]);

        $order = Order::create([
            'location_id' => $bar->id,
            'order_type' => 'walkin',
            'order_source' => 'walkin',
            'bartender_status' => 'prepared',
            'bartender_status_updated_at' => now(),
            'status' => 'open',
            'customer_name' => 'Walkin Guest',
            'created_by' => $actor->id,
        ]);

        OrderItem::create([
            'order_id' => $order->id,
            'menu_item_id' => $menuItem->id,
            'quantity' => $itemQty,
            'unit_price' => 15000,
            'subtotal' => 15000 * $itemQty,
            'status' => 'pending',
        ]);

        $order->recalculate();

        return $order->fresh();
    }

    protected function createUserWithRole(string $roleName): User
    {
        $roleId = (string) Str::uuid();

        DB::table('roles')->insert([
            'id' => $roleId,
            'name' => $roleName,
            'description' => Str::title(str_replace('_', ' ', $roleName)),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $user = new User();
        $user->id = (string) Str::uuid();
        $user->name = Str::title(str_replace('_', ' ', $roleName)) . ' User';
        $user->email = $roleName . '-' . Str::lower(Str::random(6)) . '@example.test';
        $user->role_id = $roleId;
        $user->is_active = true;
        $user->password = Hash::make('password');
        $user->save();

        return $user;
    }
}
