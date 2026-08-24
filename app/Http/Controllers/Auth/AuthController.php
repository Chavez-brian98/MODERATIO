<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View|RedirectResponse
    {
        if (Auth::check()) {
            return $this->redirectToDefaultModule();
        }

        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($validated + ['is_active' => true], $remember)) {
            return back()
                ->withErrors(['email' => 'Credenciales incorrectas o cuenta inactiva.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        AuditService::log('LOGIN', 'users', Auth::id());

        return $this->redirectToDefaultModule();
    }

    public function destroy(Request $request): RedirectResponse
    {
        AuditService::log('LOGOUT', 'users', Auth::id());

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirectToDefaultModule(): RedirectResponse
    {
        $user = Auth::user();

        // Check if user has a role with a configured default route
        $role = $user->role();
        if ($role && $role->default_route) {
            // Verify the user actually has permission to access that route
            $routePermissionMap = [
                'dashboard' => 'dashboard_view',
                'pos' => 'sales_view',
                'inventory.index' => 'products_view',
                'categories.index' => 'categories_view',
                'employees.index' => 'users_view',
                'roles.index' => 'roles_view',
                'cash-register.index' => 'cash_registers_view',
                'returns.index' => 'returns_view',
                'reports.index' => 'reports_view',
                'audit.index' => 'audit_log_view',
                'settings.index' => 'settings_view',
            ];

            if (isset($routePermissionMap[$role->default_route])) {
                $permission = $routePermissionMap[$role->default_route];
                if ($user->hasEffectivePermission($permission)) {
                    return redirect()->route($role->default_route);
                }
            } else {
                // If the route is not in the map, try to redirect anyway
                return redirect()->route($role->default_route);
            }
        }

        // Fallback to permission-based redirection
        if ($user->hasEffectivePermission('dashboard_view')) {
            return redirect()->route('dashboard');
        }

        $moduleRoutes = [
            'sales_view' => 'pos',
            'products_view' => 'inventory.index',
            'categories_view' => 'categories.index',
            'users_view' => 'employees.index',
            'roles_view' => 'roles.index',
            'cash_registers_view' => 'cash-register.index',
            'returns_view' => 'returns.index',
            'reports_view' => 'reports.index',
            'audit_log_view' => 'audit.index',
            'settings_view' => 'settings.index',
        ];

        foreach ($moduleRoutes as $permission => $route) {
            if ($user->hasEffectivePermission($permission)) {
                return redirect()->route($route);
            }
        }

        return redirect()->route('dashboard');
    }
}
