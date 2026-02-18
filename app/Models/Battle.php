<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Battle extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'opponent_name',
        'opponent_strength',
        'pet_strength',
        'result',
        'difficulty',
        'fought_at',
    ];

    protected $casts = [
        'pet_strength' => 'float',
        'opponent_strength' => 'float',
        'fought_at' => 'datetime',
    ];

    /**
     * Get the pet that fought this battle.
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
