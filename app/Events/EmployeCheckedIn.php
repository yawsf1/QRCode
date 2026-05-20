<?php
namespace App\Events;
use App\Models\Presence;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
class EmployeCheckedIn implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public function __construct(public Presence $presence)
    {
        $this->presence = $presence->load('user:id,nom,prenom,departement,admin_id');
    }
    public function broadcastOn(): array
    {
        return [new Channel('attendance-channel')];
    }
    public function broadcastAs(): string
    {
        return 'checked-in';
    }
    public function broadcastWith(): array
    {
        return [
            'presence' => [
                'id' => $this->presence->id,
                'user_id' => $this->presence->user_id,
                'admin_id' => $this->presence->admin_id,
                'statut' => $this->presence->statut,
                'ecart_minutes' => $this->presence->ecart_minutes,
                'date_heure_scan' => $this->presence->date_heure_scan?->toIso8601String(),
                'user' => $this->presence->user ? [
                    'id' => $this->presence->user->id,
                    'nom' => $this->presence->user->nom,
                    'prenom' => $this->presence->user->prenom,
                    'departement' => $this->presence->user->departement,
                ] : null,
            ],
        ];
    }
}
