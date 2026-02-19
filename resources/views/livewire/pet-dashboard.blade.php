<div class="max-w-3xl mx-auto">
    {{-- Messages --}}
    @if($message)
        <div class="mb-6 p-4 rounded-2xl {{ $messageType === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
            {{ $message }}
        </div>
    @endif

    {{-- Pet Card --}}
    <div class="bg-white rounded-3xl shadow-lg p-8 mb-6">
        {{-- Pet Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center space-x-3">
                <div class="text-5xl">🐾</div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">{{ $pet->name }}</h2>
                    <p class="text-sm text-gray-500">Seu companheiro virtual</p>
                </div>
            </div>
            <div class="text-right bg-blue-50 rounded-2xl px-4 py-3">
                <div class="text-xs text-blue-600 font-medium">Força de Batalha</div>
                <div class="text-3xl font-bold text-blue-600">{{ number_format($pet->battle_strength, 1) }}</div>
            </div>
        </div>
        
        {{-- Alert Indicators --}}
        @if($pet->health < 30 || $pet->hunger > 70)
            <div class="mb-6 space-y-3">
                @if($pet->health < 30)
                    <div class="bg-red-100 border-l-4 border-red-400 p-4 rounded-2xl">
                        <p class="font-semibold text-red-800 text-sm">⚠️ Saúde Crítica!</p>
                        <p class="text-xs text-red-700">Alimente seu pet urgentemente</p>
                    </div>
                @endif
                
                @if($pet->hunger > 70)
                    <div class="bg-orange-100 border-l-4 border-orange-400 p-4 rounded-2xl">
                        <p class="font-semibold text-orange-800 text-sm">🍖 Muita Fome!</p>
                        <p class="text-xs text-orange-700">Seu pet está faminto</p>
                    </div>
                @endif
            </div>
        @endif

        {{-- Stats Bars --}}
        <div class="space-y-5">
            {{-- Health --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">❤️ Saúde</span>
                    <span class="text-sm font-bold {{ $pet->health < 30 ? 'text-red-500' : 'text-gray-800' }}">{{ $pet->health }}/100</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-4">
                    <div class="h-4 rounded-full transition-all duration-300 {{ $pet->health < 30 ? 'bg-gradient-to-r from-red-400 to-red-500' : 'bg-gradient-to-r from-green-400 to-green-500' }}" 
                         style="width: {{ $pet->health }}%"></div>
                </div>
            </div>

            {{-- Hunger --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">🍖 Fome</span>
                    <span class="text-sm font-bold {{ $pet->hunger > 70 ? 'text-orange-500' : 'text-gray-800' }}">{{ $pet->hunger }}/100</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-4">
                    <div class="h-4 rounded-full transition-all duration-300 {{ $pet->hunger > 70 ? 'bg-gradient-to-r from-orange-400 to-orange-500' : 'bg-gradient-to-r from-blue-400 to-blue-500' }}" 
                         style="width: {{ $pet->hunger }}%"></div>
                </div>
            </div>

            {{-- Training --}}
            <div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-sm font-medium text-gray-700">💪 Treinamento</span>
                    <span class="text-sm font-bold text-gray-800">{{ $pet->training_level }}/100</span>
                </div>
                <div class="w-full bg-gray-100 rounded-full h-4">
                    <div class="bg-gradient-to-r from-purple-400 to-purple-500 h-4 rounded-full transition-all duration-300" 
                         style="width: {{ $pet->training_level }}%"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- Action Buttons --}}
    <div class="grid grid-cols-3 gap-4 mb-6">
        <button wire:click="feed" 
                class="bg-gradient-to-br from-green-400 to-emerald-400 hover:from-green-500 hover:to-emerald-500 text-white font-semibold py-6 px-4 rounded-3xl transition-all shadow-lg hover:shadow-xl">
            <div class="text-3xl mb-2">🍖</div>
            <div class="text-sm">Alimentar</div>
        </button>
        
        <button wire:click="train" 
                class="bg-gradient-to-br from-blue-400 to-indigo-400 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold py-6 px-4 rounded-3xl transition-all shadow-lg hover:shadow-xl {{ $pet->health < 30 ? 'opacity-50 cursor-not-allowed' : '' }}"
                {{ $pet->health < 30 ? 'disabled' : '' }}>
            <div class="text-3xl mb-2">💪</div>
            <div class="text-sm">Treinar</div>
        </button>
        
        <a href="/battle" 
           class="bg-gradient-to-br from-red-400 to-rose-400 hover:from-red-500 hover:to-rose-500 text-white font-semibold py-6 px-4 rounded-3xl transition-all shadow-lg hover:shadow-xl text-center">
            <div class="text-3xl mb-2">⚔️</div>
            <div class="text-sm">Batalhar</div>
        </a>
    </div>

    {{-- Quick Stats --}}
    <div class="grid grid-cols-4 gap-3">
        <div class="bg-white rounded-2xl p-4 text-center shadow-md">
            <div class="text-2xl mb-1">⚡</div>
            <div class="text-xs text-gray-600">Força</div>
            <div class="text-lg font-bold text-blue-600">{{ number_format($pet->battle_strength, 1) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center shadow-md">
            <div class="text-2xl mb-1">🏆</div>
            <div class="text-xs text-gray-600">Nível</div>
            <div class="text-lg font-bold text-purple-600">{{ floor($pet->training_level / 10) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center shadow-md">
            <div class="text-2xl mb-1">⏰</div>
            <div class="text-xs text-gray-600">Atualizado</div>
            <div class="text-xs font-medium text-gray-800">{{ $pet->last_updated_at->diffForHumans(null, true) }}</div>
        </div>
        <div class="bg-white rounded-2xl p-4 text-center shadow-md">
            <div class="text-2xl mb-1">📊</div>
            <div class="text-xs text-gray-600">Status</div>
            <div class="text-xs font-bold {{ $pet->health > 70 ? 'text-green-500' : ($pet->health > 30 ? 'text-yellow-500' : 'text-red-500') }}">
                {{ $pet->health > 70 ? 'Ótimo' : ($pet->health > 30 ? 'Bom' : 'Crítico') }}
            </div>
        </div>
    </div>

    {{-- Animations --}}
    @if($showFeedAnimation)
        <div class="fixed inset-0 pointer-events-none flex items-center justify-center z-50">
            <div class="text-8xl animate-bounce">🍖</div>
        </div>
    @endif

    @if($showTrainAnimation)
        <div class="fixed inset-0 pointer-events-none flex items-center justify-center z-50">
            <div class="text-8xl animate-spin">💪</div>
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
