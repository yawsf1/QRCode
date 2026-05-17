<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AdminDashboard
{
    /**
     * Create a new class instance.
     */
    public function __construct()
    {
        //
    }
    public function showEmploye($id)
    {
        $employe = User::where('role', 'employe')
        ->with(['horaire', 'presences' => function($query) {
            $query->latest();
        }])
        ->findOrFail($id);

        $stats = [
            'total_scans' => $employe->presences->count(),
            'on_time'     => $employe->presences->where('statut', 'a_lheure')->count(),
            'late'        => $employe->presences->where('statut', 'en_retard')->count(),
            'early'       => $employe->presences->where('statut', 'en_avance')->count(),
            'absent'      => $employe->presences->where('statut', 'absent')->count(),
        ];
        return Inertia::render('Admin/Employe', [
            'employe' => $employe,
            'stats' => $stats
        ]);
    }

    public function listEmploye(Request $request){
        $employes = User::query()
            ->where('role', 'employe')
            ->where('admin_id', Auth::id())
            ->with('horaire')
            ->filter($request->only(['search', 'status']))
            ->sortBy($request->sortBy)
            ->paginate(4)
            ->withQueryString();

        return Inertia::render('Admin/ListEmploye'
            , [
                'employes' => $employes
            ]
            );
    }
    public function editEmploye($id){
        $employe = User::with('horaire')->findOrFail($id);;
        return Inertia::render('Admin/EmployeUpdate', 
            ['employe' => $employe]
        );
    }



    public function whatever(){
        $employes = User::query()
            ->where('role', 'employe')
            ->where('admin_id,', Auth::id())->count();
            
    }
    
}
