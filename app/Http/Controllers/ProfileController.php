<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private ProfileService $profiles) {}

    public function edit(Request $request): View
    {
        $user = $request->user()->loadMissing('roles');

        return view('modules.profile.edit', ['user' => $user]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (! Hash::check($validated['current_password'], $request->user()->password)) {
            return back()->withErrors(['current_password' => 'La contraseña actual no es correcta.']);
        }

        $this->profiles->updatePassword($request->user(), $validated['password']);

        $request->session()->regenerate();

        return back()->with('success', 'Contraseña actualizada correctamente.');
    }

    public function updatePhoto(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'photo' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $this->profiles->updatePhoto($request->user(), $validated['photo']);

        return back()->with('success', 'Foto de perfil actualizada.');
    }

    public function destroyPhoto(Request $request): RedirectResponse
    {
        $this->profiles->removePhoto($request->user());

        return back()->with('success', 'Foto de perfil eliminada.');
    }
}
