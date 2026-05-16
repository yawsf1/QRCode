<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'nom',
        'prenom',
        'email',
        'password',
        'departement',
        'telephone',
        'role',
        'est_actif',
        'admin_id',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'est_actif'         => 'boolean',
        ];
    }


    public function isAdmin() : bool {
        return $this->role === 'admin';
    }
    public function isEmploye() : bool {
        return $this->role === 'employe';
    }
    public function isSuperAdmin() : bool {
        return $this->role === 'super_admin';
    }

    public function horaire()
    {
        return $this->hasOne(Horaire::class);
    }
    public function presences()
    {
        return $this->hasMany(Presence::class);
    }



    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function employes()
    {
        return $this->hasMany(User::class, 'admin_id');
    }



    public function scopeFilter($query, array $filters)
        {
            return $query
                ->when($filters['search'] ?? null, function ($query, $search) {
                    $query->where(function ($q) use ($search) {
                        $q->where('nom', 'ilike', "{$search}%")
                        ->orWhere('prenom', 'ilike', "{$search}%")
                        ->orWhere('email', 'like', "{$search}%");
                    });
                })
                ->when(isset($filters['status']), function ($query) use ($filters) {
                    $query->where('est_actif', $filters['status']);
                });
        }
        public function scopeSortBy($query, $sort = 'recent')
    {
        return match ($sort) {
            'employees' => $query->orderBy('employes_count', 'desc'),
            'oldest' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };
    }
}
