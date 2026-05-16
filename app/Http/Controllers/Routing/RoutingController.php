<?php

namespace App\Http\Controllers\Routing;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class RoutingController extends Controller
{
    public function home(){
        return Inertia::render('Home');
    }

    public function login(){
        return Inertia::render('Auth/Login');
    }
    public function Admindashboard(){
        return Inertia::render('Admin/Dashboard');
    }
    public function EmployeRegister(){
        return Inertia::render('Admin/EmployeRegister');
    }
    public function AdminRegister(){
        return Inertia::render('SuperAdmin/AdminRegister');
    }
    public function editEmploye($id){
        $employe = User::find($id)->load('horaire');
        return Inertia::render('Admin/EmployeUpdate', 
            ['employe' => $employe]
        );
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
    public function SuperAdmindashboard()
    {
        $adminsByMonth = User::where('role', 'admin')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('COUNT(id) as total'),
                DB::raw("TRIM(TO_CHAR(created_at, 'Month')) as month") 
            )
            ->groupBy(DB::raw("TRIM(TO_CHAR(created_at, 'Month'))"))
            ->pluck('total', 'month');

        $months = [
            'January', 'February', 'March', 'April', 'May', 'June', 
            'July', 'August', 'September', 'October', 'November', 'December'
        ];

        $employesByMonth = User::where('role', 'employe')
            ->whereYear('created_at', date('Y'))
            ->select(
                DB::raw('COUNT(id) as total'),
                DB::raw("TRIM(TO_CHAR(created_at, 'Month')) as month")
            )
            ->groupBy(DB::raw("TRIM(TO_CHAR(created_at, 'Month'))"))
            ->pluck('total', 'month');

        $salesData = [];
        foreach ($months as $month) {
            $salesData[] = $adminsByMonth->get($month, 0);
        }

        $salesData2 = [];
        foreach ($months as $month) {
            $salesData2[] = $employesByMonth->get($month, 0);
        }

        $entreprises = User::where('role', 'admin')
            ->whereNotNull('departement')
            ->select('id', 'departement')
            ->withCount('employes')
            ->orderBy('employes_count', 'desc')
            ->limit(5)
            ->get();
                
        $entrepriseNames = $entreprises->pluck('departement')->toArray();
        $employeeCounts = $entreprises->pluck('employes_count')->map(fn($val) => (int)$val)->toArray();

        return Inertia::render('SuperAdmin/Dashboard', [
            'months' => $months,
            'salesData' => $salesData,
            'salesData2' => $salesData2,
            'entrepriseNames' => $entrepriseNames,
            'employeeCounts' => $employeeCounts
        ]);
    }



    public function listAdmin(Request $request)
    {
        $admins = User::query()
            ->where('role', 'admin')
            ->withCount('employes')
            ->filter($request->only(['search', 'status']))
            ->sortBy($request->sortBy)
            ->paginate(4)
            ->withQueryString();

        return Inertia::render('SuperAdmin/ListAdmin', [
            'admins' => $admins,
        ]);
    }
}