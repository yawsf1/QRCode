<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{

    public function login(LoginRequest $request)
    {
        $credentials = $request->validated();

        if (Auth::attempt($credentials)) {

            $request->session()->regenerate();

            $user = Auth::user();

            if($user->isSuperAdmin()){
                return redirect()
                    ->route('super-admin.dashboard')
                    ->with('success', 'Bienvenue ' . $user->prenom);
            }
            if($user->isAdmin()){
                return redirect()
                    ->route('admin.dashboard')
                    ->with('success', 'Bienvenue ' . $user->prenom);
            }
            return redirect()
                ->route('home')
                ->with('success', 'Bienvenue ' . $user->prenom);
        }

        return back()
            ->withErrors([
                'email' => 'Email ou mot de passe incorrect'
            ]);
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