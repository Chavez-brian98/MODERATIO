<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CashRegisterController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReturnController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\SettingsController;
use Illuminate\Support\Facades\Route;

Route::get('/', [AuthController::class, 'create'])->name('login');
Route::get('/login', [AuthController::class, 'create'])->name('login.form');
Route::post('/login', [AuthController::class, 'store'])->name('login.store')->middleware('throttle:5,1');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'destroy'])->name('logout');

    Route::get('/perfil', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/perfil/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/perfil/foto', [ProfileController::class, 'updatePhoto'])->name('profile.photo');
    Route::delete('/perfil/foto', [ProfileController::class, 'destroyPhoto'])->name('profile.photo.destroy');

    Route::get('/dashboard', DashboardController::class)->name('dashboard')->middleware('permission:dashboard_view');

    // POS
    Route::middleware('permission:sales_view')->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos');
        Route::get('/pos/productos', [PosController::class, 'searchProducts'])->name('pos.products');
        Route::get('/pos/clientes', [PosController::class, 'searchCustomers'])->name('pos.customers');
    });
    Route::post('/pos/caja/abrir', [PosController::class, 'openCashRegister'])->name('pos.cash-register.open')->middleware('permission:cash_registers_create');
    Route::post('/pos/caja/cerrar', [PosController::class, 'closeCashRegister'])->name('pos.cash-register.close')->middleware('permission:cash_registers_edit');
    Route::post('/pos/venta', [PosController::class, 'completeSale'])->name('pos.sale.complete')->middleware('permission:sales_create');

    // Caja / Arqueo
    Route::get('/caja', [CashRegisterController::class, 'index'])->name('cash-register.index')->middleware('permission:cash_registers_view');
    Route::get('/caja/abrir', [CashRegisterController::class, 'create'])->name('cash-register.create')->middleware('permission:cash_registers_create');
    Route::post('/caja', [CashRegisterController::class, 'store'])->name('cash-register.store')->middleware('permission:cash_registers_create');
    Route::get('/caja/{cashRegister}', [CashRegisterController::class, 'show'])->name('cash-register.show')->middleware('permission:cash_registers_view');
    Route::get('/caja/{cashRegister}/editar', [CashRegisterController::class, 'edit'])->name('cash-register.edit')->middleware('permission:cash_registers_edit');
    Route::patch('/caja/{cashRegister}/cerrar', [CashRegisterController::class, 'close'])->name('cash-register.close')->middleware('permission:cash_registers_edit');
    Route::patch('/caja/{cashRegister}/estado', [CashRegisterController::class, 'toggleStatus'])->name('cash-register.toggle-status')->middleware('permission:cash_registers_edit');
    Route::match(['put', 'patch'], '/caja/{cashRegister}', [CashRegisterController::class, 'update'])->name('cash-register.update')->middleware('permission:cash_registers_edit');

    // Inventario
    Route::get('/inventario', [InventoryController::class, 'index'])->name('inventory.index')->middleware('permission:products_view');
    Route::get('/inventario/crear', [InventoryController::class, 'create'])->name('inventory.create')->middleware('permission:products_create');
    Route::post('/inventario', [InventoryController::class, 'store'])->name('inventory.store')->middleware('permission:products_create');
    Route::get('/inventario/{product}', [InventoryController::class, 'show'])->name('inventory.show')->middleware('permission:products_view');
    Route::get('/inventario/{product}/editar', [InventoryController::class, 'edit'])->name('inventory.edit')->middleware('permission:products_edit');
    Route::patch('/inventario/{product}/estado', [InventoryController::class, 'toggleActive'])->name('inventory.toggle')->middleware('permission:products_edit');
    Route::match(['put', 'patch'], '/inventario/{product}', [InventoryController::class, 'update'])->name('inventory.update')->middleware('permission:products_edit');
    Route::delete('/inventario/{product}', [InventoryController::class, 'destroy'])->name('inventory.destroy')->middleware('permission:products_delete');

    // Categorías
    Route::get('/categorias', [CategoryController::class, 'index'])->name('categories.index')->middleware('permission:categories_view');
    Route::get('/categorias/crear', [CategoryController::class, 'create'])->name('categories.create')->middleware('permission:categories_create');
    Route::post('/categorias', [CategoryController::class, 'store'])->name('categories.store')->middleware('permission:categories_create');
    Route::get('/categorias/{category}', [CategoryController::class, 'show'])->name('categories.show')->middleware('permission:categories_view');
    Route::get('/categorias/{category}/editar', [CategoryController::class, 'edit'])->name('categories.edit')->middleware('permission:categories_edit');
    Route::patch('/categorias/{category}/estado', [CategoryController::class, 'toggleActive'])->name('categories.toggle')->middleware('permission:categories_edit');
    Route::match(['put', 'patch'], '/categorias/{category}', [CategoryController::class, 'update'])->name('categories.update')->middleware('permission:categories_edit');
    Route::delete('/categorias/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy')->middleware('permission:categories_delete');

    // Clientes
    Route::get('/clientes', [CustomerController::class, 'index'])->name('customers.index')->middleware('permission:customers_view');
    Route::get('/clientes/crear', [CustomerController::class, 'create'])->name('customers.create')->middleware('permission:customers_create');
    Route::post('/clientes', [CustomerController::class, 'store'])->name('customers.store')->middleware('permission:customers_create');
    Route::get('/clientes/{customer}', [CustomerController::class, 'show'])->name('customers.show')->middleware('permission:customers_view');
    Route::get('/clientes/{customer}/editar', [CustomerController::class, 'edit'])->name('customers.edit')->middleware('permission:customers_edit');
    Route::patch('/clientes/{customer}/estado', [CustomerController::class, 'toggleActive'])->name('customers.toggle')->middleware('permission:customers_edit');
    Route::match(['put', 'patch'], '/clientes/{customer}', [CustomerController::class, 'update'])->name('customers.update')->middleware('permission:customers_edit');
    Route::delete('/clientes/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy')->middleware('permission:customers_delete');

    // Empleados
    Route::get('/empleados', [EmployeeController::class, 'index'])->name('employees.index')->middleware('permission:users_view');
    Route::get('/empleados/crear', [EmployeeController::class, 'create'])->name('employees.create')->middleware('permission:users_create');
    Route::post('/empleados', [EmployeeController::class, 'store'])->name('employees.store')->middleware('permission:users_create');
    Route::get('/empleados/{employee}/editar', [EmployeeController::class, 'edit'])->name('employees.edit')->middleware('permission:users_edit');
    Route::get('/empleados/{employee}/permisos', [EmployeeController::class, 'permissions'])->name('employees.permissions')->middleware('permission:users_edit');
    Route::post('/empleados/{employee}/permisos', [EmployeeController::class, 'syncPermissions'])->name('employees.permissions.sync')->middleware('permission:users_edit');
    Route::patch('/empleados/{employee}/estado', [EmployeeController::class, 'toggleActive'])->name('employees.toggle')->middleware('permission:users_edit');
    Route::match(['put', 'patch'], '/empleados/{employee}', [EmployeeController::class, 'update'])->name('employees.update')->middleware('permission:users_edit');
    Route::delete('/empleados/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy')->middleware('permission:users_delete');
    Route::get('/empleados/{employee}', [EmployeeController::class, 'show'])->name('employees.show')->middleware('permission:users_view');

    // Roles
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles_view');
    Route::get('/roles/crear', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles_create');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles_create');
    Route::get('/roles/{role}/permisos', [RoleController::class, 'permissions'])->name('roles.permissions')->middleware('permission:roles_edit');
    Route::post('/roles/{role}/permisos', [RoleController::class, 'syncPermissions'])->name('roles.permissions.sync')->middleware('permission:roles_edit');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:roles_view');
    Route::get('/roles/{role}/editar', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles_edit');
    Route::patch('/roles/{role}/estado', [RoleController::class, 'toggleActive'])->name('roles.toggle')->middleware('permission:roles_edit');
    Route::match(['put', 'patch'], '/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles_edit');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles_delete');

    // Devoluciones
    Route::get('/devoluciones', [ReturnController::class, 'index'])->name('returns.index')->middleware('permission:returns_view');
    Route::get('/devoluciones/crear', [ReturnController::class, 'create'])->name('returns.create')->middleware('permission:returns_create');
    Route::post('/devoluciones', [ReturnController::class, 'store'])->name('returns.store')->middleware('permission:returns_create');
    Route::get('/devoluciones/{return}', [ReturnController::class, 'show'])->name('returns.show')->middleware('permission:returns_view');

    // Reportes
    Route::get('/reportes', [ReportController::class, 'index'])->name('reports.index')->middleware('permission:reports_view');

    // Bitácora
    Route::get('/bitacora', [AuditController::class, 'index'])->name('audit.index')->middleware('permission:audit_log_view');
    Route::get('/bitacora/{log}', [AuditController::class, 'show'])->name('audit.show')->middleware('permission:audit_log_view');

    // Configuración
    Route::get('/configuracion', [SettingsController::class, 'index'])->name('settings.index')->middleware('permission:settings_view');
    Route::put('/configuracion', [SettingsController::class, 'update'])->name('settings.update')->middleware('permission:settings_edit');
});
