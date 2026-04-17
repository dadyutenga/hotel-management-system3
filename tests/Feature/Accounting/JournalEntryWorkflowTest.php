<?php

namespace Tests\Feature\Accounting;

use App\Models\JournalEntry;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class JournalEntryWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_can_create_edit_and_post_draft_with_balanced_lines(): void
    {
        [$accountant] = $this->bootstrapUsers();

        $this->actingAs($accountant)
            ->post(route('accounting.journal.store'), [
                'entry_date' => now()->toDateString(),
                'description' => 'Manual adjustment',
                'reference' => 'MAN-001',
                'lines' => [
                    ['account_id' => $this->accountId('1100'), 'type' => 'debit', 'amount' => 1000, 'notes' => 'cash'],
                    ['account_id' => $this->accountId('6600'), 'type' => 'credit', 'amount' => 900, 'notes' => 'expense'],
                ],
            ])
            ->assertSessionHasErrors('lines');

        $this->actingAs($accountant)
            ->post(route('accounting.journal.store'), [
                'entry_date' => now()->toDateString(),
                'description' => 'Manual adjustment',
                'reference' => 'MAN-001',
                'lines' => [
                    ['account_id' => $this->accountId('1100'), 'type' => 'debit', 'amount' => 1000, 'notes' => 'cash'],
                    ['account_id' => $this->accountId('6600'), 'type' => 'credit', 'amount' => 1000, 'notes' => 'expense'],
                ],
            ])
            ->assertRedirect(route('accounting.journal.index'));

        $entry = JournalEntry::where('reference', 'MAN-001')->firstOrFail();
        $this->assertSame('draft', $entry->status);

        $this->actingAs($accountant)
            ->put(route('accounting.journal.update', $entry), [
                'entry_date' => now()->toDateString(),
                'description' => 'Manual adjustment updated',
                'reference' => 'MAN-001',
                'lines' => [
                    ['account_id' => $this->accountId('1100'), 'type' => 'debit', 'amount' => 1500, 'notes' => 'cash'],
                    ['account_id' => $this->accountId('6600'), 'type' => 'credit', 'amount' => 1500, 'notes' => 'expense'],
                ],
            ])
            ->assertRedirect(route('accounting.journal.show', $entry));

        $entry->refresh();
        $this->assertSame('draft', $entry->status);
        $this->assertEquals(1500.0, (float) $entry->total_debit);

        $this->actingAs($accountant)
            ->post(route('accounting.journal.post', $entry))
            ->assertRedirect(route('accounting.journal.show', $entry));

        $entry->refresh();
        $this->assertSame('posted', $entry->status);
        $this->assertNotNull($entry->posted_at);
        $this->assertSame($accountant->id, $entry->posted_by);
    }

    public function test_posted_entries_are_locked_and_manager_can_reverse(): void
    {
        [$accountant, $manager] = $this->bootstrapUsers();

        $entry = JournalEntry::create([
            'entry_date' => now()->toDateString(),
            'description' => 'Posted manual entry',
            'reference' => 'MAN-LOCK-1',
            'source' => 'manual',
            'total_debit' => 2000,
            'total_credit' => 2000,
            'status' => 'posted',
            'created_by' => $accountant->id,
            'posted_by' => $accountant->id,
            'posted_at' => now(),
        ]);

        $entry->lines()->createMany([
            ['account_id' => $this->accountId('1100'), 'type' => 'debit', 'amount' => 2000, 'notes' => 'd'],
            ['account_id' => $this->accountId('6600'), 'type' => 'credit', 'amount' => 2000, 'notes' => 'c'],
        ]);

        $this->actingAs($accountant)
            ->get(route('accounting.journal.edit', $entry))
            ->assertStatus(403);

        $this->actingAs($manager)
            ->post(route('manager.accounting.journal.reverse', $entry), ['reason' => 'Wrong allocation'])
            ->assertRedirect(route('manager.accounting.journal.show', $entry));

        $entry->refresh();
        $this->assertSame('reversed', $entry->status);

        $reversal = JournalEntry::where('reference', 'REV-' . $entry->entry_no)->first();
        $this->assertNotNull($reversal);
        $this->assertSame('posted', $reversal->status);
        $this->assertEquals((float) $entry->total_debit, (float) $reversal->total_credit);
        $this->assertEquals((float) $entry->total_credit, (float) $reversal->total_debit);
    }

    public function test_non_accountant_cannot_create_or_post_journal_entries(): void
    {
        [$accountant, , $supervisor] = $this->bootstrapUsers();

        $this->actingAs($supervisor)
            ->post(route('accounting.journal.store'), [
                'entry_date' => now()->toDateString(),
                'description' => 'Forbidden draft',
                'lines' => [
                    ['account_id' => $this->accountId('1100'), 'type' => 'debit', 'amount' => 1000],
                    ['account_id' => $this->accountId('6600'), 'type' => 'credit', 'amount' => 1000],
                ],
            ])
            ->assertRedirect(route('dashboard'));

        $entry = JournalEntry::create([
            'entry_date' => now()->toDateString(),
            'description' => 'Draft to post',
            'reference' => 'MAN-POST-1',
            'source' => 'manual',
            'total_debit' => 1000,
            'total_credit' => 1000,
            'status' => 'draft',
            'created_by' => $accountant->id,
        ]);

        $entry->lines()->createMany([
            ['account_id' => $this->accountId('1100'), 'type' => 'debit', 'amount' => 1000],
            ['account_id' => $this->accountId('6600'), 'type' => 'credit', 'amount' => 1000],
        ]);

        $this->actingAs($supervisor)
            ->post(route('accounting.journal.post', $entry))
            ->assertRedirect(route('dashboard'));

        $entry->refresh();
        $this->assertSame('draft', $entry->status);
    }

    private function bootstrapUsers(): array
    {
        Artisan::call('db:seed', ['class' => 'RoleSeeder']);
        Artisan::call('db:seed', ['class' => 'ChartOfAccountsSeeder']);

        $accountantRole = Role::where('name', 'ACCOUNTANT')->firstOrFail();
        $managerRole = Role::where('name', 'manager')->firstOrFail();
        $supervisorRole = Role::where('name', 'supervisor')->firstOrFail();

        $accountant = User::factory()->create(['role_id' => $accountantRole->id, 'is_active' => true]);
        $manager = User::factory()->create(['role_id' => $managerRole->id, 'is_active' => true]);
        $supervisor = User::factory()->create(['role_id' => $supervisorRole->id, 'is_active' => true]);

        return [$accountant, $manager, $supervisor];
    }

    private function accountId(string $code): string
    {
        return \App\Models\Account::where('code', $code)->value('id');
    }
}

