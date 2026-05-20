<?php
namespace App\Http\Controllers\Employe;
use App\Events\PlatformStatsUpdated;
use App\Http\Controllers\Controller;
use App\Http\Requests\Employe\RegisterRequest as EmployeRegisterRequest;
use App\Http\Requests\Employe\UpdateRequest;
use App\Models\User;
use App\Support\AppCache;
use App\Support\CacheKeys;
use App\Services\EmailVerificationService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class EmployeController extends Controller
{
    public function __construct(protected EmailVerificationService $verification) {}

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
                'email_verified_at' => now(),
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

        $this->verification->clearRegistrationVerification($user->email);

        AppCache::forget(CacheKeys::adminDashboard(Auth::id()));
        AppCache::forget(CacheKeys::superAdminDashboard());
        broadcast(new PlatformStatsUpdated('employe_created', [
            'monthIndex' => (int) date('n') - 1,
            'departement' => $user->departement,
            'adminId' => Auth::id(),
        ]))->toOthers();
        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Employé ' . $user->prenom . ' ajouté. L\'adresse e-mail a été vérifiée.');
    }
    public function delete(User $user)
    {
        if (!$user->isEmploye() || $user->admin_id !== Auth::id()) {
            return back()->with('error', "Cet utilisateur n’est pas un employé de votre équipe");
        }
        $departement = $user->departement;
        $user->horaire()->delete();
        $user->delete();
        AppCache::forget(CacheKeys::adminDashboard(Auth::id()));
        AppCache::forget(CacheKeys::superAdminDashboard());
        broadcast(new PlatformStatsUpdated('employe_deleted', [
            'departement' => $departement,
            'adminId' => Auth::id(),
        ]))->toOthers();
        return back()->with('success', 'Employé supprimé');
    }
    public function update(UpdateRequest $request, User $user)
    {
        if (!$user->isEmploye() || $user->admin_id !== Auth::id()) {
            return back()->with('error', "Cet utilisateur n’est pas un employé de votre équipe");
        }
        $user = DB::transaction(function () use ($request, $user) {
            $data = $request->validated();
            $user->update([
                'nom' => $data['nom'],
                'prenom' => $data['prenom'],
                'email' => $data['email'],
                'telephone' => $data['telephone'] ?? null,
                'departement' => $data['departement'],
                'est_actif' => $data['est_actif'] ?? true,
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
        AppCache::forget(CacheKeys::adminDashboard(Auth::id()));
        AppCache::forget(CacheKeys::employeDashboard($user->id));
        return redirect()
            ->route('employe.list')
            ->with('success', 'Employé ' . $user->prenom . ' mis à jour');
    }
}
