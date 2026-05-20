<?php
namespace App\Http\Controllers\Admin;
use App\Events\PlatformStatsUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegisterRequest as AdminRegisterRequest;
use App\Models\User;
use App\Support\AppCache;
use App\Support\CacheKeys;
use Illuminate\Http\Request;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function __construct(protected EmailVerificationService $verification) {}

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
            return back()->with('error', 'Vous ne pouvez pas supprimer un super admin');
        }
        if (!$user->isAdmin()) {
            return back()->with('error', 'Cet utilisateur n’est pas un admin');
        }
        $user->delete();
        AppCache::forget(CacheKeys::superAdminDashboard());
        broadcast(new PlatformStatsUpdated('admin_deleted'))->toOthers();
        return back()->with('success', 'Admin supprimé');
    }
    public function register(AdminRegisterRequest $request)
    {
        $data = $request->validated();
        $admin = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'admin',
            'departement' => $data['departement'] ?? null,
            'telephone' => $data['telephone'] ?? null,
            'est_actif' => true,
            'admin_id' => Auth::id(),
            'email_verified_at' => now(),
        ]);

        $this->verification->clearRegistrationVerification($data['email']);
        AppCache::forget(CacheKeys::superAdminDashboard());
        broadcast(new PlatformStatsUpdated('admin_created', [
            'monthIndex' => (int) date('n') - 1,
        ]))->toOthers();
        return redirect()
            ->route('super-admin.dashboard')
            ->with('success', 'Admin ajouté. L\'adresse e-mail a été vérifiée.');
    }
}
