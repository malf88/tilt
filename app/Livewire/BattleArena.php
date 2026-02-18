<?php

namespace App\Livewire;

use App\Models\Pet;
use App\Models\Battle;
use App\Services\BattleService;
use Livewire\Component;

class BattleArena extends Component
{
    public Pet $pet;
    public string $difficulty = 'easy';
    public ?Battle $currentBattle = null;
    public bool $showBattleAnimation = false;
    public ?array $opponent = null;

    public function selectDifficulty(string $difficulty): void
    {
        $this->difficulty = $difficulty;
        $this->currentBattle = null;
        $this->opponent = null;
    }

    public function startBattle(): void
    {
        $battleService = app(BattleService::class);
        
        // Execute the battle
        $this->currentBattle = $battleService->executeBattle($this->pet, $this->difficulty);
        
        // Get opponent info for display
        $this->opponent = [
            'name' => $this->currentBattle->opponent_name,
            'strength' => $this->currentBattle->opponent_strength,
        ];
        
        // Show battle animation
        $this->showBattleAnimation = true;
        
        // Refresh pet data
        $this->pet->refresh();
        
        // Dispatch event to update other components
        $this->dispatch('petUpdated');
        
        // Reset animation after delay
        $this->dispatch('resetBattleAnimation');
    }

    public function resetBattle(): void
    {
        $this->currentBattle = null;
        $this->opponent = null;
        $this->showBattleAnimation = false;
    }

    public function render()
    {
        return view('livewire.battle-arena');
    }
}
