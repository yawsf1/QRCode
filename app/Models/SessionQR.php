<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class SessionQR extends Model
{
    protected $table = 'sessions_qr';
    protected $fillable = [
        'token',
        'est_actif',
        'expires_at',
        'admin_id',
    ];
    protected $casts = [
        'est_actif' => 'boolean',
        'expires_at' => 'datetime',
    ];
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }
}
