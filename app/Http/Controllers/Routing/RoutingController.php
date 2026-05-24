<?php
namespace App\Http\Controllers\Routing;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\AdminDashboard;
use App\Services\EmployeDashboard;
use App\Services\SuperAdminDashboard;
use Illuminate\Http\Request;
use Inertia\Inertia;
class RoutingController extends Controller
{
    public function __construct(
        protected AdminDashboard $adminService,
        protected SuperAdminDashboard $superAdminService,
        protected EmployeDashboard $employeService,
    ) {}
    public function home()
    {
        $employees = User::where('role', 'employe')->count();
        $admins = User::where('role', 'admin')->count();
        return Inertia::render('Home', [
            'employees' => $employees,
            'admins' => $admins,
        ]);
    }
    public function login()
    {
        return Inertia::render('Auth/Login');
    }
    public function employeRegister()
    {
        return Inertia::render('Admin/EmployeRegister');
    }
    public function adminRegister()
    {
        return Inertia::render('SuperAdmin/AdminRegister');
    }
    public function superAdminDashboard()
    {
        return $this->superAdminService->superAdminDashboard();
    }
    public function listAdmin(Request $request)
    {
        return $this->superAdminService->listAdmin($request);
    }
    public function adminDashboard()
    {
        return $this->adminService->adminDashboard();
    }
    public function showEmploye($id)
    {
        return $this->adminService->showEmploye($id);
    }
    public function editEmploye($id)
    {
        return $this->adminService->editEmploye($id);
    }
    public function listEmploye(Request $request)
    {
        return $this->adminService->listEmploye($request);
    }
    public function employeDashboard()
    {
        return $this->employeService->employeDashboard();
    }

    public function employeScanForm()
    {
        $user = auth()->user();

        if ($user->hasCheckedInToday()) {
            return redirect()
                ->route('employe.dashboard')
                ->with('success', 'Votre présence est déjà enregistrée pour aujourd\'hui.');
        }

        return Inertia::render('Employe/Scanner');
    }
}
