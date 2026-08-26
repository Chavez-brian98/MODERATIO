<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Permission;
use App\Models\Role;
use App\Models\Sale;
use App\Models\User;
use App\Services\CashRegisterService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CashRegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_show_renders_detail_page(): void
    {
        $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: auth()->id(),
            openingAmount: 100.0,
            shift: 'MORNING',
        );

        $this->get(route('cash-register.show', $register))
            ->assertOk()
            ->assertSee('Caja #'.$register->id)
            ->assertSee('Información de la caja')
            ->assertSee('Fecha de creación');
    }

    public function test_store_persists_optional_responsible_and_creation_date(): void
    {
        $responsible = User::factory()->create();
        $this->signIn();

        $this->post(route('cash-register.store'), [
            'opening_amount' => '50.00',
            'shift' => 'NIGHT',
            'responsible_id' => $responsible->id,
        ])->assertRedirect(route('cash-register.index'));

        $register = CashRegister::query()->latest('id')->first();

        $this->assertSame($responsible->id, $register->responsible_id);
        $this->assertNotNull($register->created_at);
        $this->assertTrue($register->created_at->diffInSeconds(now()) < 5);
    }

    public function test_store_allows_empty_responsible(): void
    {
        $user = $this->signIn();

        $this->post(route('cash-register.store'), [
            'opening_amount' => '50.00',
        ])->assertRedirect(route('cash-register.index'));

        $register = CashRegister::query()->latest('id')->first();

        $this->assertNull($register->responsible_id);
        $this->assertSame($user->id, $register->user_id);
    }

    public function test_close_theoretical_uses_cash_sales_only_and_stores_difference(): void
    {
        $user = $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: $user->id,
            openingAmount: 100.0,
            shift: null,
        );

        Sale::create([
            'cash_register_id' => $register->id,
            'user_id' => $user->id,
            'ticket_number' => 'V-0001',
            'total' => 100.0,
            'amount_received' => 100.0,
            'change_due' => 0.0,
            'payment_method' => 'CASH',
            'status' => 'COMPLETED',
        ]);

        Sale::create([
            'cash_register_id' => $register->id,
            'user_id' => $user->id,
            'ticket_number' => 'V-0002',
            'total' => 50.0,
            'amount_received' => 50.0,
            'change_due' => 0.0,
            'payment_method' => 'CARD',
            'status' => 'COMPLETED',
        ]);

        $this->patch(route('cash-register.close', $register), [
            'actual_closing_amount' => '200.00',
        ])->assertRedirect(route('cash-register.show', $register));

        $register->refresh();

        $this->assertSame('CLOSED', $register->status);
        $this->assertEqualsWithDelta(200.0, $register->theoretical_closing_amount, 0.001);
        $this->assertEqualsWithDelta(0.0, $register->difference, 0.001);
        $this->assertNotNull($register->closing_date);
    }

    public function test_show_marks_difference_for_unbalanced_registers(): void
    {
        $user = $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: $user->id,
            openingAmount: 100.0,
            shift: null,
        );

        app(CashRegisterService::class)->close($register, 90.0);

        $this->get(route('cash-register.show', $register->fresh()))
            ->assertOk()
            ->assertSee('Faltante')
            ->assertSee('$10.00');
    }

    public function test_close_stores_closing_notes(): void
    {
        $user = $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: $user->id,
            openingAmount: 100.0,
            shift: null,
        );

        $this->patch(route('cash-register.close', $register), [
            'actual_closing_amount' => '95.00',
            'closing_notes' => 'Faltante por gasto de caja no registrado.',
        ])->assertRedirect(route('cash-register.show', $register));

        $register->refresh();

        $this->assertSame('Faltante por gasto de caja no registrado.', $register->closing_notes);
        $this->assertDatabaseHas('audit_log', [
            'action' => 'CLOSED',
            'affected_table' => 'cash_registers',
        ]);
    }

    public function test_close_rejects_notes_longer_than_500_chars(): void
    {
        $user = $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: $user->id,
            openingAmount: 100.0,
            shift: null,
        );

        $this->from(route('cash-register.show', $register))
            ->patch(route('cash-register.close', $register), [
                'actual_closing_amount' => '100.00',
                'closing_notes' => str_repeat('x', 501),
            ])
            ->assertSessionHasErrors(['closing_notes']);
    }

    public function test_edit_page_renders_register_data(): void
    {
        $responsible = User::factory()->create();
        $user = $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: $user->id,
            openingAmount: 100.0,
            shift: 'MORNING',
            responsibleId: $responsible->id,
        );

        $this->get(route('cash-register.edit', $register))
            ->assertOk()
            ->assertSee('Editar Caja #'.$register->id)
            ->assertSee('Abierta');
    }

    public function test_update_fixes_typo_on_open_register(): void
    {
        $responsible = User::factory()->create();
        $user = $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: $user->id,
            openingAmount: 100.0,
            shift: null,
        );

        $this->put(route('cash-register.update', $register), [
            'opening_amount' => '150.00',
            'shift' => 'AFTERNOON',
            'responsible_id' => (string) $responsible->id,
            'closing_notes' => 'Corrección de monto de apertura.',
        ])->assertRedirect(route('cash-register.show', $register))
            ->assertSessionHas('success');

        $register->refresh();

        $this->assertEqualsWithDelta(150.0, $register->opening_amount, 0.001);
        $this->assertSame('AFTERNOON', $register->shift);
        $this->assertSame($responsible->id, $register->responsible_id);
        $this->assertSame('Corrección de monto de apertura.', $register->closing_notes);

        $this->assertDatabaseHas('audit_log', [
            'action' => 'UPDATED',
            'affected_table' => 'cash_registers',
        ]);
    }

    public function test_update_on_closed_register_recalculates_theoretical_and_difference(): void
    {
        $user = $this->signIn();
        $service = app(CashRegisterService::class);

        $register = $service->open(userId: $user->id, openingAmount: 100.0, shift: null);

        Sale::create([
            'cash_register_id' => $register->id,
            'user_id' => $user->id,
            'ticket_number' => 'V-0001',
            'total' => 50.0,
            'amount_received' => 50.0,
            'change_due' => 0.0,
            'payment_method' => 'CASH',
            'status' => 'COMPLETED',
        ]);

        $service->close($register, 140.0);
        $register->refresh();
        $this->assertEqualsWithDelta(-10.0, $register->difference, 0.001);

        // Typo fix: opening was actually 120 and the counted cash was 160.
        $this->put(route('cash-register.update', $register), [
            'opening_amount' => '120.00',
            'actual_closing_amount' => '160.00',
        ])->assertRedirect(route('cash-register.show', $register));

        $register->refresh();

        $this->assertEqualsWithDelta(170.0, $register->theoretical_closing_amount, 0.001);
        $this->assertEqualsWithDelta(160.0, $register->actual_closing_amount, 0.001);
        $this->assertEqualsWithDelta(-10.0, $register->difference, 0.001);
        $this->assertSame('CLOSED', $register->status);
    }

    public function test_update_requires_actual_amount_when_closed(): void
    {
        $user = $this->signIn();
        $service = app(CashRegisterService::class);

        $register = $service->open(userId: $user->id, openingAmount: 100.0, shift: null);
        $service->close($register, 100.0);

        $this->put(route('cash-register.update', $register->fresh()), [
            'opening_amount' => '120.00',
        ])->assertSessionHasErrors(['actual_closing_amount']);
    }

    public function test_update_rejects_invalid_shift(): void
    {
        $user = $this->signIn();

        $register = app(CashRegisterService::class)->open(
            userId: $user->id,
            openingAmount: 100.0,
            shift: null,
        );

        $this->put(route('cash-register.update', $register), [
            'opening_amount' => '100.00',
            'shift' => 'MIDNIGHT',
        ])->assertSessionHasErrors(['shift']);
    }

    public function test_responsible_select_only_lists_users_with_pos_access(): void
    {
        $this->signIn();

        $cashierRole = Role::firstOrCreate(
            ['name' => 'CAJERO'],
            ['is_active' => true],
        );
        $salesView = Permission::where('name', 'sales_view')->first();
        $cashierRole->permissions()->sync([$salesView->id]);

        $withAccess = User::factory()->create(['full_name' => 'Ana Cajera']);
        $withAccess->roles()->sync([$cashierRole->id]);

        $withoutAccess = User::factory()->create(['full_name' => 'Beto Bodega']);

        $response = $this->get(route('cash-register.create'))->assertOk();

        $content = $response->getContent();
        $this->assertStringContainsString('Ana Cajera', $content);
        $this->assertStringNotContainsString('Beto Bodega', $content);

        // Same filter on the edit page.
        $register = app(CashRegisterService::class)->open(
            userId: auth()->id(),
            openingAmount: 100.0,
            shift: null,
        );

        $editResponse = $this->get(route('cash-register.edit', $register))->assertOk();

        $editContent = $editResponse->getContent();
        $this->assertStringContainsString('Ana Cajera', $editContent);
        $this->assertStringNotContainsString('Beto Bodega', $editContent);
    }

    public function test_responsible_select_excludes_denied_and_lists_direct_grants(): void
    {
        $this->signIn();

        $salesView = Permission::where('name', 'sales_view')->first();

        $denied = User::factory()->create(['full_name' => 'Carla Denegada']);
        $denied->permissions()->attach($salesView->id, ['type' => 'deny']);

        $granted = User::factory()->create(['full_name' => 'Diego Autorizado']);
        $granted->permissions()->attach($salesView->id, ['type' => 'grant']);

        $content = $this->get(route('cash-register.create'))->getContent();

        $this->assertStringContainsString('Diego Autorizado', $content);
        $this->assertStringNotContainsString('Carla Denegada', $content);
    }

    public function test_inactive_user_with_pos_access_is_not_listed_as_responsible(): void
    {
        $this->signIn();

        $cashierRole = Role::firstOrCreate(
            ['name' => 'CAJERO'],
            ['is_active' => true],
        );
        $salesView = Permission::where('name', 'sales_view')->first();
        $cashierRole->permissions()->sync([$salesView->id]);

        $inactive = User::factory()->create(['full_name' => 'Elena Inactiva', 'is_active' => false]);
        $inactive->roles()->sync([$cashierRole->id]);

        $this->get(route('cash-register.create'))
            ->assertOk()
            ->assertDontSee('Elena Inactiva');
    }
}
