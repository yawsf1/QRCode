<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AlertNotification extends Model
{
    protected $table = 'notifications';

    protected $fillable = [
        'content',
        'type',
        'lu',
        'user_id',
        'admin_id',
    ];

    protected function casts(): array
    {
        return [
            'lu' => 'boolean',
        ];
    }

    public function employe(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
