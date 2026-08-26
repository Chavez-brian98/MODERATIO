<?php

namespace Tests\Feature;

use App\Models\CashRegister;
use App\Models\Customer;
use App\Models\Sale;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerTest extends TestCase
{
    use RefreshDatabase;

    private function validData(array $overrides = []): array
    {
        return array_merge([
            'first_name' => 'María',
            'last_name' => 'Pérez',
            'tax_id' => '12345678-9',
            'phone' => '7000-0000',
            'email' => 'maria@example.com',
            'address' => 'San Salvador',
            'customer_type' => 'REGULAR',
            'is_active' => '1',
        ], $overrides);
    }

    public function test_index_renders_customers(): void
    {
        Customer::create($this->validData());
        $this->signIn();

        $this->get(route('customers.index'))
            ->assertOk()
            ->assertSee('Clientes')
            ->assertSee('María');
    }

    public function test_store_creates_customer(): void
    {
        $this->signIn();

        $this->post(route('customers.store'), $this->validData())
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'first_name' => 'María',
            'tax_id' => '12345678-9',
            'is_active' => true,
        ]);
    }

    public function test_store_validates_required_fields_and_unique_tax_id(): void
    {
        Customer::create($this->validData());
        $this->signIn();

        $this->post(route('customers.store'), $this->validData(['first_name' => '', 'tax_id' => '12345678-9']))
            ->assertSessionHasErrors(['first_name', 'tax_id']);

        $this->post(route('customers.store'), $this->validData(['customer_type' => 'INVALID']))
            ->assertSessionHasErrors(['customer_type']);
    }

    public function test_show_returns_modal_partial(): void
    {
        $customer = Customer::create($this->validData());
        $this->signIn();

        $this->get(route('customers.show', $customer))
            ->assertOk()
            ->assertSee('data-customer-modal', false)
            ->assertSee('María Pérez')
            ->assertViewIs('modules.customers.show');
    }

    public function test_update_modifies_customer(): void
    {
        $customer = Customer::create($this->validData());
        $this->signIn();

        $this->put(route('customers.update', $customer), $this->validData([
            'first_name' => 'Ana',
            'customer_type' => 'WHOLESALER',
        ]))->assertRedirect(route('customers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('customers', [
            'id' => $customer->id,
            'first_name' => 'Ana',
            'customer_type' => 'WHOLESALER',
        ]);
    }

    public function test_update_ignores_own_tax_id_on_unique_check(): void
    {
        $customer = Customer::create($this->validData());
        $this->signIn();

        $this->put(route('customers.update', $customer), $this->validData())
            ->assertRedirect(route('customers.index'))
            ->assertSessionHasNoErrors();
    }

    public function test_toggle_flips_active_status_and_audits(): void
    {
        $customer = Customer::create($this->validData());
        $this->signIn();

        $this->patch(route('customers.toggle', $customer))
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('success');

        $this->assertFalse($customer->fresh()->is_active);

        $this->patch(route('customers.toggle', $customer->fresh()));
        $this->assertTrue($customer->fresh()->is_active);

        $this->assertDatabaseHas('audit_log', [
            'action' => 'TOGGLED',
            'affected_table' => 'customers',
        ]);
    }

    public function test_destroy_deletes_customer_without_sales(): void
    {
        $customer = Customer::create($this->validData());
        $this->signIn();

        $this->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }

    public function test_destroy_blocks_customer_with_sales(): void
    {
        $user = User::factory()->create();
        $customer = Customer::create($this->validData());

        $register = CashRegister::create([
            'user_id' => $user->id,
            'opening_amount' => 100,
            'status' => 'OPEN',
            'opening_date' => now(),
        ]);

        Sale::create([
            'cash_register_id' => $register->id,
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'ticket_number' => 'T-0001',
            'total' => 100,
            'amount_received' => 100,
            'change_due' => 0,
            'payment_method' => 'CASH',
            'status' => 'COMPLETED',
        ]);

        $this->signIn();

        $this->delete(route('customers.destroy', $customer))
            ->assertRedirect(route('customers.index'))
            ->assertSessionHas('error');

        $this->assertDatabaseHas('customers', ['id' => $customer->id]);
    }

    public function test_delete_action_only_shown_for_disabled_customers(): void
    {
        $active = Customer::create($this->validData());
        $disabled = Customer::create($this->validData(['first_name' => 'Beto', 'tax_id' => '87654321-0', 'is_active' => false]));
        $this->signIn();

        $content = $this->get(route('customers.index'))
            ->assertOk()
            ->getContent();

        $this->assertStringContainsString('action="'.route('customers.destroy', $disabled).'"', $content);
        $this->assertStringNotContainsString('action="'.route('customers.destroy', $active).'"', $content);

        // Once disabled through the toggle, the delete action becomes available.
        $this->patch(route('customers.toggle', $active))->assertRedirect();

        $content = $this->get(route('customers.index'))->getContent();
        $this->assertStringContainsString('action="'.route('customers.destroy', $active).'"', $content);
    }
}
