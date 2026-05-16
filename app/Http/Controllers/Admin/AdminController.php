<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest as AdminRegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class AdminController extends Controller
{
    public function deleteOwnAccount(Request $request)
    {
        $user = Auth::user();

        Auth::logout();

        $user->delete();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()
            ->route('login')
            ->with('success', 'Compte supprimé');
    }

    
    public function delete(User $user)
    {
        if ($user->isSuperAdmin()) {
            return back()->with(
                'error',
                'Vous ne pouvez pas supprimer un super admin'
            );
        }

        if (!$user->isAdmin()) {
            return back()->with(
                'error',
                'Cet utilisateur n’est pas un admin'
            );
        }

        $user->delete();

        return back()->with(
            'success',
            'Admin supprimé'
        );
    }

    public function register(AdminRegisterRequest $request)
    {
        $data = $request->validated();
        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'departement' => $data['departement'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'est_actif' => true,
            'admin_id' => Auth::id(),
        ]);
        return redirect()
            ->route('super-admin.dashboard')
            ->with('success', 'Admin ajouté');
    }

}
