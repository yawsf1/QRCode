<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Presence extends Model
{
    protected $table = 'presences';
    protected $fillable = [
        'date_heure_scan',
        'statut',
        'ecart_minutes',
        'adresse_ip',
        'user_id',
        'session_id',
        'admin_id',
    ];
    protected $casts = [
        'date_heure_scan' => 'datetime',
        'ecart_minutes' => 'integer',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function sessionQr()
    {
        return $this->belongsTo(SessionQR::class, 'session_id');
    }
}
