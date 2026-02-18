<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'pet_id',
        'training_level_before',
        'training_level_after',
        'trained_at',
    ];

    protected $casts = [
        'training_level_before' => 'integer',
        'training_level_after' => 'integer',
        'trained_at' => 'datetime',
    ];

    /**
     * Get the pet that owns the training log.
     */
    public function pet(): BelongsTo
    {
        return $this->belongsTo(Pet::class);
    }
}
