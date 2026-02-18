<?php

namespace App\Http\Controllers;

use App\Contracts\PetServiceInterface;
use App\Http\Requests\CreatePetRequest;
use App\Models\Pet;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PetController extends Controller
{
    public function __construct(
        private PetServiceInterface $petService
    ) {}

    /**
     * Display the pet dashboard.
     * Loads the user's pet and applies time degradation.
     */
    public function show(): View
    {
        // Load the first pet (assuming single pet per user for now)
        $pet = Pet::first();
        
        // If no pet exists, redirect to create page
        if (!$pet) {
            return view('pet.create');
        }
        
        // Apply time degradation
        $pet = $this->petService->applyTimeDegradation($pet);
        
        return view('pet.dashboard', compact('pet'));
    }

    /**
     * Store a newly created pet.
     */
    public function store(CreatePetRequest $request): RedirectResponse
    {
        // Create the pet using validated data
        $pet = $this->petService->createPet($request->validated('name'));
        
        return redirect()
            ->route('pet.dashboard')
            ->with('success', "Pet {$pet->name} criado com sucesso!");
    }

    /**
     * Feed the pet.
     */
    public function feed(): RedirectResponse
    {
        try {
            $pet = Pet::first();
            
            if (!$pet) {
                return redirect()->route('pet.create');
            }
            
            $this->petService->feedPet($pet);
            
            return redirect()
                ->back()
                ->with('success', "{$pet->name} foi alimentado!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Train the pet.
     */
    public function train(): RedirectResponse
    {
        try {
            $pet = Pet::first();
            
            if (!$pet) {
                return redirect()->route('pet.create');
            }
            
            $this->petService->trainPet($pet);
            
            return redirect()
                ->back()
                ->with('success', "{$pet->name} treinou com sucesso!");
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', $e->getMessage());
        }
    }
}
