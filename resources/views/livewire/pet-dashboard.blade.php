<div class="pet-dashboard p-6 max-w-2xl mx-auto">
    {{-- Messages --}}
    @if($message)
        <div class="mb-4 p-4 rounded-lg {{ $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $message }}
        </div>
    @endif

    {{-- Pet Info --}}
    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
        <h2 class="text-2xl font-bold mb-4">{{ $pet->name }}</h2>
        
        {{-- Alert Indicators --}}
        <div class="mb-4 space-y-2">
            @if($pet->health < 30)
                <div class="health-alert alert bg-red-100 border-l-4 border-red-500 text-red-700 p-3 rounded">
                    <p class="font-bold">⚠️ Saúde Crítica!</p>
                    <p class="text-sm">Seu pet está muito fraco. Alimente-o urgentemente!</p>
                </div>
            @endif
            
            @if($pet->hunger > 70)
                <div class="hunger-alert alert bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-3 rounded">
                    <p class="font-bold">🍖 Muita Fome!</p>
                    <p class="text-sm">Seu pet está faminto. Alimente-o logo!</p>
                </div>
            @endif
        </div>

        {{-- Stats Bars --}}
        <div class="space-y-4">
            {{-- Health Bar --}}
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">Saúde</span>
                    <span class="text-sm font-medium">{{ $pet->health }}/100</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full transition-all duration-300 {{ $pet->health < 30 ? 'bg-red-500' : 'bg-green-500' }}" 
                         style="width: {{ $pet->health }}%"></div>
                </div>
            </div>

            {{-- Hunger Bar --}}
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">Fome</span>
                    <span class="text-sm font-medium">{{ $pet->hunger }}/100</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="h-4 rounded-full transition-all duration-300 {{ $pet->hunger > 70 ? 'bg-orange-500' : 'bg-blue-500' }}" 
                         style="width: {{ $pet->hunger }}%"></div>
                </div>
            </div>

            {{-- Training Level Bar --}}
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">Nível de Treinamento</span>
                    <span class="text-sm font-medium">{{ $pet->training_level }}/100</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-purple-500 h-4 rounded-full transition-all duration-300" 
                         style="width: {{ $pet->training_level }}%"></div>
                </div>
            </div>

            {{-- Battle Strength --}}
            <div>
                <div class="flex justify-between mb-1">
                    <span class="text-sm font-medium">Força de Batalha</span>
                    <span class="text-sm font-medium">{{ number_format($pet->battle_strength, 1) }}/100</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div class="bg-indigo-500 h-4 rounded-full transition-all duration-300" 
                         style="width: {{ $pet->battle_strength }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="grid grid-cols-3 gap-4">
        <button wire:click="feed" 
                class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 {{ $showFeedAnimation ? 'animate-pulse' : '' }}">
            🍖 Alimentar
        </button>
        
        <button wire:click="train" 
                class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200 {{ $showTrainAnimation ? 'animate-pulse' : '' }}"
                {{ $pet->health < 30 ? 'disabled' : '' }}>
            💪 Treinar
        </button>
        
        <a href="/battle" 
           class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-4 rounded-lg text-center transition-colors duration-200">
            ⚔️ Batalhar
        </a>
    </div>

    {{-- Animations --}}
    @if($showFeedAnimation)
        <div class="fixed inset-0 pointer-events-none flex items-center justify-center">
            <div class="text-6xl animate-bounce">🍖</div>
        </div>
    @endif

    @if($showTrainAnimation)
        <div class="fixed inset-0 pointer-events-none flex items-center justify-center">
            <div class="text-6xl animate-spin">💪</div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('resetAnimation', (event) => {
            setTimeout(() => {
                if (event.animation === 'feed') {
                    @this.showFeedAnimation = false;
                } else if (event.animation === 'train') {
                    @this.showTrainAnimation = false;
                }
            }, 2000);
        });
    });
</script>
