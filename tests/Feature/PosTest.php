<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PosTest extends TestCase
{
    use RefreshDatabase;

    public function test_pos_warns_when_user_has_no_open_register(): void
    {
        $other = User::factory()->create();

        CashRegister::create([
            'user_id' => $other->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->signIn();

        $this->get('/pos')
            ->assertOk()
            ->assertSee('No puedes realizar ventas hasta tener una caja abierta a tu cargo')
            ->assertSee('Sin caja abierta');
    }

    public function test_pos_recognizes_register_opened_by_user(): void
    {
        $user = $this->signIn();

        CashRegister::create([
            'user_id' => $user->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->get('/pos')
            ->assertOk()
            ->assertSee('Caja abierta');
    }

    public function test_pos_recognizes_register_where_user_is_responsible(): void
    {
        $opener = User::factory()->create();
        $responsible = $this->signIn();

        CashRegister::create([
            'user_id' => $opener->id,
            'responsible_id' => $responsible->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->get('/pos')
            ->assertOk()
            ->assertSee('Caja abierta');
    }

    public function test_opening_register_from_pos_assigns_authenticated_user(): void
    {
        $user = $this->signIn();

        $this->postJson(route('pos.cash-register.open'), [
            'opening_amount' => 50.5,
            'shift' => 'MORNING',
        ])->assertOk();

        $register = CashRegister::query()->latest('id')->first();

        $this->assertSame($user->id, $register->user_id);
        $this->assertSame('MORNING', $register->shift);
        $this->assertEqualsWithDelta(50.5, $register->opening_amount, 0.001);
    }

    public function test_complete_sale_rejects_user_without_open_register(): void
    {
        $category = Category::create(['name' => 'General', 'is_active' => true]);

        $product = Product::create([
            'category_id' => $category->id,
            'barcode' => 'P-0001',
            'name' => 'Producto prueba',
            'purchase_price' => 5,
            'sale_price' => 10,
            'current_stock' => 10,
            'min_stock' => 1,
            'has_tax' => false,
            'tax_percentage' => 0,
            'is_active' => true,
        ]);

        $other = User::factory()->create();

        CashRegister::create([
            'user_id' => $other->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->signIn();

        $this->postJson(route('pos.sale.complete'), [
            'items' => [
                ['product_id' => $product->id, 'quantity' => 1, 'unit_price' => 10],
            ],
            'payment_method' => 'CASH',
            'amount_received' => 10,
        ])->assertStatus(422)
            ->assertJson([
                'error' => 'No tienes una caja abierta a tu cargo. Debes abrir una caja antes de poder vender.',
            ]);

        $this->assertDatabaseCount('sales', 0);
    }

    public function test_pos_shows_close_button_and_theoretical_amount_for_open_register(): void
    {
        $user = $this->signIn();

        CashRegister::create([
            'user_id' => $user->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->get('/pos')
            ->assertOk()
            ->assertSee('Cerrar caja')
            ->assertSee('close-register-modal', false)
            ->assertSee('$100.00');
    }

    public function test_pos_can_close_own_register_with_notes(): void
    {
        $user = $this->signIn();

        $register = CashRegister::create([
            'user_id' => $user->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->postJson(route('pos.cash-register.close'), [
            'actual_closing_amount' => 120.5,
            'closing_notes' => 'Sobrante por propina en efectivo.',
        ])->assertOk()
            ->assertJson([
                'message' => 'Caja cerrada correctamente.',
            ]);

        $register->refresh();

        $this->assertSame('CLOSED', $register->status);
        $this->assertEqualsWithDelta(120.5, $register->actual_closing_amount, 0.001);
        $this->assertEqualsWithDelta(20.5, $register->difference, 0.001);
        $this->assertSame('Sobrante por propina en efectivo.', $register->closing_notes);
        $this->assertNotNull($register->closing_date);

        $this->assertDatabaseHas('audit_log', [
            'action' => 'CLOSED',
            'affected_table' => 'cash_registers',
        ]);
    }

    public function test_pos_close_rejects_user_without_open_register(): void
    {
        $other = User::factory()->create();

        CashRegister::create([
            'user_id' => $other->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->signIn();

        $this->postJson(route('pos.cash-register.close'), [
            'actual_closing_amount' => 100,
        ])->assertStatus(422)
            ->assertJson([
                'error' => 'No tienes una caja abierta a tu cargo para cerrar.',
            ]);
    }

    public function test_pos_close_validates_amount(): void
    {
        $this->signIn();

        $this->postJson(route('pos.cash-register.close'), [
            'actual_closing_amount' => -5,
        ])->assertStatus(422)
            ->assertJsonValidationErrors(['actual_closing_amount']);
    }

    public function test_responsible_user_can_close_register_from_pos(): void
    {
        $opener = User::factory()->create();
        $responsible = $this->signIn();

        CashRegister::create([
            'user_id' => $opener->id,
            'responsible_id' => $responsible->id,
            'opening_amount' => 80,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        $this->postJson(route('pos.cash-register.close'), [
            'actual_closing_amount' => 80,
        ])->assertOk();
    }
}
