<?php

namespace App\Livewire;

use App\Models\Pet;
use App\Services\PetService;
use Livewire\Component;
use Illuminate\Support\Facades\Log;

class PetDashboard extends Component
{
    public Pet $pet;
    public bool $showFeedAnimation = false;
    public bool $showTrainAnimation = false;
    public ?string $message = null;
    public ?string $messageType = null;

    protected $listeners = ['petUpdated' => '$refresh'];

    public function mount(): void
    {
        $petService = app(PetService::class);
        $this->pet = $petService->applyTimeDegradation($this->pet);
    }

    public function feed(): void
    {
        try {
            $petService = app(PetService::class);
            $this->pet = $petService->feedPet($this->pet);
            
            $this->showFeedAnimation = true;
            $this->message = 'Pet alimentado com sucesso!';
            $this->messageType = 'success';
            
            $this->dispatch('petUpdated');
            
            // Reset animation after delay
            $this->dispatch('resetAnimation', animation: 'feed');
        } catch (\Exception $e) {
            Log::error('Erro ao alimentar pet: ' . $e->getMessage());
            $this->message = 'Erro ao alimentar pet.';
            $this->messageType = 'error';
        }
    }

    public function train(): void
    {
        try {
            $petService = app(PetService::class);
            $this->pet = $petService->trainPet($this->pet);
            
            $this->showTrainAnimation = true;
            $this->message = 'Pet treinado com sucesso!';
            $this->messageType = 'success';
            
            $this->dispatch('petUpdated');
            
            // Reset animation after delay
            $this->dispatch('resetAnimation', animation: 'train');
        } catch (\Exception $e) {
            $this->message = $e->getMessage();
            $this->messageType = 'error';
        }
    }

    public function render()
    {
        return view('livewire.pet-dashboard');
    }
}
