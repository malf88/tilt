<?php

namespace App\Observers;

use App\Models\Pet;

class PetObserver
{
    /**
     * Handle the Pet "saving" event.
     * 
     * This is triggered before a pet is saved (both create and update).
     * We update the last_updated_at timestamp on every save of existing pets.
     */
    public function saving(Pet $pet): void
    {
        // For existing pets, always update last_updated_at
        // For new pets, only update if not explicitly set
        if ($pet->exists) {
            $pet->last_updated_at = now();
        }
    }
}
