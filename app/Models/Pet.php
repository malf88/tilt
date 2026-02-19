<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pet extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'pet_type',
        'health',
        'hunger',
        'training_level',
        'last_updated_at',
    ];

    /**
     * Available pet types with their emoji representations
     */
    public const PET_TYPES = [
        'dog' => '🐕',
        'cat' => '🐱',
        'dragon' => '🐉',
        'fox' => '🦊',
        'panda' => '🐼',
        'tiger' => '🐯',
    ];

    protected $casts = [
        'health' => 'integer',
        'hunger' => 'integer',
        'training_level' => 'integer',
        'last_updated_at' => 'datetime',
    ];

    /**
     * Get the pet's battle strength.
     * Formula: (health × 0.4) + (training_level × 0.6)
     * With 20% penalty if hunger > 70
     */
    public function getBattleStrengthAttribute(): float
    {
        $baseStrength = ($this->health * 0.4) + ($this->training_level * 0.6);
        
        // Apply hunger penalty if hunger > 70
        if ($this->hunger > 70) {
            $baseStrength *= 0.8; // 20% penalty
        }
        
        // Ensure value is between 0 and 100
        return max(0, min(100, $baseStrength));
    }

    /**
     * Get the pet's battles.
     */
    public function battles(): HasMany
    {
        return $this->hasMany(Battle::class);
    }

    /**
     * Get the pet's training logs.
     */
    public function trainingLogs(): HasMany
    {
        return $this->hasMany(TrainingLog::class);
    }

    /**
     * Check if the pet is healthy (health >= 30).
     */
    public function getIsHealthyAttribute(): bool
    {
        return $this->health >= 30;
    }

    /**
     * Check if the pet is hungry (hunger > 70).
     */
    public function getIsHungryAttribute(): bool
    {
        return $this->hunger > 70;
    }

    /**
     * Get the pet's emoji avatar.
     */
    public function getAvatarAttribute(): string
    {
        return self::PET_TYPES[$this->pet_type] ?? '🐾';
    }

    /**
     * Get the pet's color scheme based on type.
     */
    public function getColorSchemeAttribute(): array
    {
        return match($this->pet_type) {
            'dog' => ['primary' => 'amber', 'secondary' => 'orange'],
            'cat' => ['primary' => 'gray', 'secondary' => 'slate'],
            'dragon' => ['primary' => 'red', 'secondary' => 'rose'],
            'fox' => ['primary' => 'orange', 'secondary' => 'amber'],
            'panda' => ['primary' => 'gray', 'secondary' => 'zinc'],
            'tiger' => ['primary' => 'yellow', 'secondary' => 'orange'],
            default => ['primary' => 'blue', 'secondary' => 'indigo'],
        };
    }
}
