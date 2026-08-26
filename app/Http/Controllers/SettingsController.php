<?php

namespace App\Http\Controllers;

use App\Services\AuditService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    private string $configPath;

    public function __construct()
    {
        $this->configPath = storage_path('app/settings.json');
    }

    public function index(): View
    {
        $settings = $this->load();

        return view('modules.settings.index', ['settings' => $settings]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'business_name' => ['required', 'string', 'max:150'],
            'business_nit' => ['nullable', 'string', 'max:20'],
            'business_address' => ['nullable', 'string', 'max:255'],
            'business_phone' => ['nullable', 'string', 'max:20'],
            'business_email' => ['nullable', 'email', 'max:150'],
            'default_tax_percentage' => ['required', 'numeric', 'min:0', 'max:100'],
            'currency_symbol' => ['required', 'string', 'max:5'],
            'receipt_footer' => ['nullable', 'string', 'max:255'],
        ]);

        $before = $this->load();

        file_put_contents($this->configPath, json_encode($validated, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        AuditService::log('UPDATED', 'settings', null, [
            'before' => $before,
            'after' => $validated,
        ]);

        return redirect()->route('settings.index')
            ->with('success', 'Configuración actualizada correctamente.');
    }

    private function load(): array
    {
        if (file_exists($this->configPath)) {
            $data = json_decode(file_get_contents($this->configPath), true);
            if (is_array($data)) {
                return $data;
            }
        }

        return [
            'business_name' => 'Glenda Store',
            'business_nit' => '',
            'business_address' => '',
            'business_phone' => '',
            'business_email' => '',
            'default_tax_percentage' => 13.00,
            'currency_symbol' => '$',
            'receipt_footer' => '¡Gracias por su compra!',
        ];
    }
}
