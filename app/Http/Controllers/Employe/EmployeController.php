<?php

namespace App\Http\Controllers\Employe;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateEmployeRequest;
use App\Http\Requests\Employe\RegisterRequest as EmployeRegisterRequest;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeController extends Controller
{
    public function register(EmployeRegisterRequest $request)
    {
        $user = DB::transaction(function () use ($request) {
            $data = $request->validated();
            $user = User::create([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'employe',
                'departement' => $data['departement'],
                'telephone' => $data['telephone'] ?? null,
                'est_actif' => true,
                'admin_id' => Auth::id(),
            ]);

            $user->horaire()->create([
                'heure_debut' => $data['heure_debut'],
                'heure_fin' => $data['heure_fin'],
                'jours_ouvres' => $data['jours_ouvres'],
                'tolerance_retard' => $data['tolerance_retard'],
                'admin_id' => Auth::id(),
            ]);
            return $user;

        });
        
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Bienvenue ' . $user->prenom);
    }
    
    public function delete(User $user)
    {
        if (!$user->isEmploye()) {
            return back()->with(
                'error',
                "Cet utilisateur n’est pas un employé"
            );
        }
        $user->horaire()->delete();
        $user->delete();

        return back()->with(
            'success',
            'Employé supprimé'
        );
    }

    public function update(UpdateEmployeRequest $request, User $user)
    {
        if(!$user->isEmploye()) {
            return back()->with(
                'error',
                "Cet utilisateur n’est pas un employé"
            );
        }
        $user = DB::transaction(function () use ($request, $user) {
            $data = $request->validated();
            $user->update([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'password' => $data['password'],
                'role' => 'employe',
                'departement' => $data['departement'],
                'telephone' => $data['telephone'] ?? null,
                'est_actif' => $data['est_actif'] ?? true,
                'admin_id' => Auth::id(),
            ]);

            $user->horaire()->update([
                'heure_debut' => $data['heure_debut'],
                'heure_fin' => $data['heure_fin'],
                'jours_ouvres' => $data['jours_ouvres'],
                'tolerance_retard' => $data['tolerance_retard'],
                'admin_id' => Auth::id(),
            ]);
            return $user;
        });
        
        return back()->with(
            'success',
            'Employé'. $user->prenom .'mis à jour'
        );
    }
}
