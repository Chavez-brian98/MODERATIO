<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PageTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_page_renders(): void
    {
        $this->get('/')->assertOk()->assertViewIs('auth.login');
    }

    public function test_login_submission_redirects_to_dashboard(): void
    {
        $this->post('/login', [
            'email' => 'demo@glenda.test',
            'password' => 'demo',
        ])->assertRedirect('/dashboard');
    }

    public function test_dashboard_page_renders(): void
    {
        $this->get('/dashboard')->assertOk()->assertViewIs('dashboard');
    }

    public function test_pos_page_renders(): void
    {
        $this->get('/pos')
            ->assertOk()
            ->assertViewIs('modules.pos.index')
            ->assertSee('Punto de Venta');
    }

    public function test_roles_index_page_renders(): void
    {
        $this->get('/roles')
            ->assertOk()
            ->assertViewIs('modules.roles.index')
            ->assertSee('Roles y Permisos');
    }

    public function test_bitacora_index_page_renders(): void
    {
        $this->get('/bitacora')
            ->assertOk()
            ->assertViewIs('modules.audit.index')
            ->assertSee('Bitácora');
    }

    public function test_categories_index_page_renders(): void
    {
        $this->get('/categorias')
            ->assertOk()
            ->assertViewIs('modules.categories.index')
            ->assertSee('Categorías');
    }

    public function test_employees_index_page_renders(): void
    {
        $this->get('/empleados')
            ->assertOk()
            ->assertViewIs('modules.employees.index')
            ->assertSee('Empleados');
    }

    public function test_cash_register_index_page_renders(): void
    {
        $this->get('/caja')
            ->assertOk()
            ->assertViewIs('modules.cash-registers.index')
            ->assertSee('Caja / Arqueo');
    }

    public function test_cash_register_create_page_renders(): void
    {
        $this->get('/caja/abrir')
            ->assertOk()
            ->assertViewIs('modules.cash-registers.create')
            ->assertSee('Abrir Caja');
    }

    public function test_inventory_index_page_renders(): void
    {
        $this->get('/inventario')
            ->assertOk()
            ->assertViewIs('modules.inventory.index')
            ->assertSee('Inventario');
    }

    public function test_inventory_create_page_renders(): void
    {
        $this->get('/inventario/crear')
            ->assertOk()
            ->assertViewIs('modules.inventory.create')
            ->assertSee('Nuevo Producto');
    }

    public function test_returns_index_page_renders(): void
    {
        $this->get('/devoluciones')
            ->assertOk()
            ->assertViewIs('modules.returns.index')
            ->assertSee('Devoluciones');
    }

    public function test_returns_create_page_renders(): void
    {
        $this->get('/devoluciones/crear')
            ->assertOk()
            ->assertViewIs('modules.returns.create')
            ->assertSee('Nueva Devolución');
    }

    public function test_reports_index_page_renders(): void
    {
        $this->get('/reportes')
            ->assertOk()
            ->assertViewIs('modules.reports.index')
            ->assertSee('Reportes');
    }

    public function test_settings_index_page_renders(): void
    {
        $this->get('/configuracion')
            ->assertOk()
            ->assertViewIs('modules.settings.index')
            ->assertSee('Configuración');
    }
}
