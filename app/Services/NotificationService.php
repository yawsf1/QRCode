<?php

namespace App\Services;

use App\Models\AlertNotification;
use App\Models\Presence;
use App\Models\User;

class NotificationService
{
    public function notifyAdminOfCheckIn(Presence $presence): void
    {
        $presence->loadMissing('user:id,nom,prenom');

        $employe = $presence->user;
        if (!$employe) {
            return;
        }

        $label = match ($presence->statut) {
            'en_retard' => 'en retard',
            'en_avance' => 'en avance',
            'a_lheure' => 'à l\'heure',
            'absent' => 'absent',
            default => $presence->statut,
        };

        $type = match ($presence->statut) {
            'en_retard' => 'retard',
            'absent' => 'absence',
            default => 'info',
        };

        AlertNotification::create([
            'content' => sprintf(
                '%s %s a pointé (%s).',
                $employe->prenom,
                $employe->nom,
                $label
            ),
            'type' => $type,
            'lu' => false,
            'user_id' => $employe->id,
            'admin_id' => $presence->admin_id,
        ]);
    }

    public function unreadForAdmin(int $adminId, int $limit = 10): array
    {
        return AlertNotification::query()
            ->where('admin_id', $adminId)
            ->where('lu', false)
            ->with('employe:id,nom,prenom')
            ->latest()
            ->limit($limit)
            ->get()
            ->map(fn (AlertNotification $n) => [
                'id' => $n->id,
                'content' => $n->content,
                'type' => $n->type,
                'lu' => $n->lu,
                'created_at' => $n->created_at?->diffForHumans(),
                'employe' => $n->employe ? [
                    'id' => $n->employe->id,
                    'nom' => $n->employe->nom,
                    'prenom' => $n->employe->prenom,
                ] : null,
            ])
            ->toArray();
    }

    public function unreadCountForAdmin(int $adminId): int
    {
        return AlertNotification::query()
            ->where('admin_id', $adminId)
            ->where('lu', false)
            ->count();
    }

    public function markAsRead(int $notificationId, User $admin): bool
    {
        $notification = AlertNotification::query()
            ->where('id', $notificationId)
            ->where('admin_id', $admin->id)
            ->first();

        if (!$notification) {
            return false;
        }

        $notification->update(['lu' => true]);

        return true;
    }

    public function markAllAsRead(int $adminId): void
    {
        AlertNotification::query()
            ->where('admin_id', $adminId)
            ->where('lu', false)
            ->update(['lu' => true]);
    }
}
