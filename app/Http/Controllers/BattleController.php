<?php

namespace App\Http\Controllers;

use App\Contracts\BattleServiceInterface;
use App\Contracts\PetServiceInterface;
use App\Http\Requests\BattleRequest;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BattleController extends Controller
{
    public function __construct(
        private BattleServiceInterface $battleService,
        private PetServiceInterface $petService
    ) {}

    /**
     * Show the battle difficulty selection screen.
     */
    public function create(): View
    {
        $pet = Pet::first();
        
        if (!$pet) {
            return view('pet.create');
        }
        
        // Apply time degradation before battle
        $pet = $this->petService->applyTimeDegradation($pet);
        
        return view('battle.arena', compact('pet'));
    }

    /**
     * Execute a battle.
     */
    public function store(BattleRequest $request): RedirectResponse
    {
        try {
            $pet = Pet::first();
            
            if (!$pet) {
                return redirect()->route('pet.create');
            }
            
            // Execute the battle
            $battle = $this->battleService->executeBattle(
                $pet,
                $request->validated('difficulty')
            );
            
            // Prepare result message
            $resultMessage = match ($battle->result) {
                'win' => "Vitória! {$pet->name} derrotou {$battle->opponent_name}!",
                'loss' => "Derrota! {$battle->opponent_name} venceu a batalha.",
                'draw' => "Empate! Foi uma batalha equilibrada contra {$battle->opponent_name}.",
            };
            
            return redirect()
                ->route('pet.dashboard')
                ->with('success', $resultMessage)
                ->with('battle', $battle);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Show the battle history.
     */
    public function history(): View
    {
        $pet = Pet::first();
        
        if (!$pet) {
            return view('pet.create');
        }
        
        // Load battles with most recent first
        $battles = $pet->battles()
            ->orderBy('fought_at', 'desc')
            ->paginate(10);
        
        return view('battle.history', compact('pet', 'battles'));
    }
}
