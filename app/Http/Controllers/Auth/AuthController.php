<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuthController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        AuditService::log('LOGIN', 'users');

        return redirect()->route('dashboard');
    }

    public function destroy(Request $request): RedirectResponse
    {
        AuditService::log('LOGOUT', 'users');

        $request->session()->invalidate();

        return redirect()->route('login');
    }
}
