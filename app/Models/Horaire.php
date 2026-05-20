<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Horaire extends Model
{
    protected $table = 'horaires';
    protected $fillable = [
        'heure_debut',
        'heure_fin',
        'jours_ouvres',
        'tolerance_retard',
        'user_id',
        'admin_id',
    ];
    protected $casts = [
        'jours_ouvres' => 'array',
        'tolerance_retard' => 'integer',
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
