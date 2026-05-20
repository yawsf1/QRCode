<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        $credentials = $request->only(['email', 'password']);
        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()->withErrors([
                'email' => 'Email ou mot de passe incorrect',
            ]);
        }

        $request->session()->regenerate();
        $user = Auth::user();

        if ($user->isSuperAdmin()) {
            return redirect()
                ->route('super-admin.dashboard')
                ->with('success', 'Bienvenue ' . $user->prenom);
        }

        if ($user->isAdmin()) {
            return redirect()
                ->route('admin.dashboard')
                ->with('success', 'Bienvenue ' . $user->prenom);
        }

        return redirect()
            ->route('employe.dashboard')
            ->with('success', 'Bienvenue ' . $user->prenom);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Vous avez été déconnecté');
    }
}
