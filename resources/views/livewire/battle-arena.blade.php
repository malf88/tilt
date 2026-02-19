<div class="max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Arena de Batalha</h2>
        <p class="text-gray-600">Teste a força do seu pet</p>
    </div>

    @if(!$currentBattle)
        {{-- Difficulty Selection --}}
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Escolha a Dificuldade</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Easy --}}
                <button wire:click="selectDifficulty('easy')" 
                        class="p-6 rounded-3xl transition-all {{ $difficulty === 'easy' ? 'bg-green-100 ring-4 ring-green-300' : 'bg-white hover:bg-green-50' }} shadow-lg">
                    <div class="text-5xl mb-3">🟢</div>
                    <h4 class="font-bold text-lg mb-1 text-gray-800">Fácil</h4>
                    <p class="text-sm text-gray-600">Força: 20-40</p>
                </button>

                {{-- Medium --}}
                <button wire:click="selectDifficulty('medium')" 
                        class="p-6 rounded-3xl transition-all {{ $difficulty === 'medium' ? 'bg-yellow-100 ring-4 ring-yellow-300' : 'bg-white hover:bg-yellow-50' }} shadow-lg">
                    <div class="text-5xl mb-3">🟡</div>
                    <h4 class="font-bold text-lg mb-1 text-gray-800">Médio</h4>
                    <p class="text-sm text-gray-600">Força: 40-70</p>
                </button>

                {{-- Hard --}}
                <button wire:click="selectDifficulty('hard')" 
                        class="p-6 rounded-3xl transition-all {{ $difficulty === 'hard' ? 'bg-red-100 ring-4 ring-red-300' : 'bg-white hover:bg-red-50' }} shadow-lg">
                    <div class="text-5xl mb-3">🔴</div>
                    <h4 class="font-bold text-lg mb-1 text-gray-800">Difícil</h4>
                    <p class="text-sm text-gray-600">Força: 70-95</p>
                </button>
            </div>
        </div>

        {{-- Pet Stats --}}
        <div class="bg-white rounded-3xl shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4 text-gray-800">Seu Pet: {{ $pet->name }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="text-center bg-red-50 rounded-2xl p-4">
                    <div class="text-3xl mb-1">❤️</div>
                    <p class="text-xs text-gray-600">Saúde</p>
                    <p class="text-xl font-bold text-red-500">{{ $pet->health }}</p>
                </div>
                <div class="text-center bg-purple-50 rounded-2xl p-4">
                    <div class="text-3xl mb-1">💪</div>
                    <p class="text-xs text-gray-600">Treinamento</p>
                    <p class="text-xl font-bold text-purple-500">{{ $pet->training_level }}</p>
                </div>
                <div class="text-center bg-orange-50 rounded-2xl p-4">
                    <div class="text-3xl mb-1">🍖</div>
                    <p class="text-xs text-gray-600">Fome</p>
                    <p class="text-xl font-bold text-orange-500">{{ $pet->hunger }}</p>
                </div>
                <div class="text-center bg-blue-50 rounded-2xl p-4">
                    <div class="text-3xl mb-1">⚡</div>
                    <p class="text-xs text-gray-600">Força</p>
                    <p class="text-xl font-bold text-blue-600">{{ number_format($pet->battle_strength, 1) }}</p>
                </div>
            </div>
        </div>

        {{-- Start Battle Button --}}
        <div class="text-center">
            <button wire:click="startBattle" 
                    class="bg-gradient-to-r from-red-400 to-rose-400 hover:from-red-500 hover:to-rose-500 text-white font-bold py-4 px-10 rounded-full text-lg transition-all shadow-lg hover:shadow-xl">
                ⚔️ Iniciar Batalha
            </button>
        </div>
    @else
        {{-- Battle Result --}}
        <div class="bg-white rounded-3xl shadow-lg p-8">
            {{-- Battle Animation --}}
            @if($showBattleAnimation)
                <div class="text-center mb-8 py-8">
                    <div class="text-6xl animate-pulse mb-4">⚔️</div>
                    <p class="text-xl font-semibold text-gray-700">Batalha em andamento...</p>
                </div>
            @endif

            {{-- Combatants --}}
            <div class="grid grid-cols-3 gap-4 mb-8">
                {{-- Your Pet --}}
                <div class="text-center bg-blue-50 rounded-3xl p-6">
                    <div class="text-6xl mb-3">🐾</div>
                    <h4 class="font-bold text-lg mb-1 text-gray-800">{{ $pet->name }}</h4>
                    <p class="text-sm text-gray-600">Força: {{ number_format($currentBattle->pet_strength, 1) }}</p>
                </div>

                {{-- VS --}}
                <div class="flex items-center justify-center">
                    <span class="text-4xl font-bold text-gray-400">VS</span>
                </div>

                {{-- Opponent --}}
                <div class="text-center bg-red-50 rounded-3xl p-6">
                    <div class="text-6xl mb-3">👾</div>
                    <h4 class="font-bold text-lg mb-1 text-gray-800">{{ $opponent['name'] }}</h4>
                    <p class="text-sm text-gray-600">Força: {{ number_format($opponent['strength'], 1) }}</p>
                </div>
            </div>

            {{-- Result --}}
            <div class="text-center mb-6">
                @if($currentBattle->result === 'win')
                    <div class="bg-green-100 rounded-3xl p-8">
                        <div class="text-7xl mb-3">🏆</div>
                        <h3 class="text-3xl font-bold text-green-700 mb-2">Vitória!</h3>
                        <p class="text-gray-700 mb-3">Seu pet venceu a batalha!</p>
                        <p class="text-sm text-green-600 font-semibold">+5 Nível de Treinamento</p>
                    </div>
                @elseif($currentBattle->result === 'loss')
                    <div class="bg-red-100 rounded-3xl p-8">
                        <div class="text-7xl mb-3">😢</div>
                        <h3 class="text-3xl font-bold text-red-700 mb-2">Derrota</h3>
                        <p class="text-gray-700 mb-3">Seu pet foi derrotado</p>
                        <p class="text-sm text-red-600">Continue treinando!</p>
                    </div>
                @else
                    <div class="bg-gray-100 rounded-3xl p-8">
                        <div class="text-7xl mb-3">🤝</div>
                        <h3 class="text-3xl font-bold text-gray-700 mb-2">Empate!</h3>
                        <p class="text-gray-700">Forças iguais</p>
                    </div>
                @endif
            </div>

            {{-- Battle Effects --}}
            <div class="bg-purple-50 rounded-2xl p-5 mb-6">
                <p class="text-sm font-semibold mb-3 text-gray-800">Efeitos da Batalha:</p>
                <div class="grid grid-cols-3 gap-3">
                    <div class="bg-white rounded-xl p-3 text-center">
                        <div class="text-2xl mb-1">🍖</div>
                        <div class="text-xs text-gray-600">Fome</div>
                        <div class="text-sm font-bold text-orange-500">+10</div>
                    </div>
                    <div class="bg-white rounded-xl p-3 text-center">
                        <div class="text-2xl mb-1">❤️</div>
                        <div class="text-xs text-gray-600">Saúde</div>
                        <div class="text-sm font-bold text-red-500">-10</div>
                    </div>
                    @if($currentBattle->result === 'win')
                        <div class="bg-white rounded-xl p-3 text-center">
                            <div class="text-2xl mb-1">💪</div>
                            <div class="text-xs text-gray-600">Treino</div>
                            <div class="text-sm font-bold text-green-500">+5</div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-2 gap-4">
                <button wire:click="resetBattle" 
                        class="bg-gradient-to-r from-blue-400 to-indigo-400 hover:from-blue-500 hover:to-indigo-500 text-white font-semibold py-3 px-6 rounded-full transition-all shadow-lg">
                    🔄 Nova Batalha
                </button>
                <a href="/pet/dashboard" 
                   class="bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-semibold py-3 px-6 rounded-full text-center transition-all shadow-lg">
                    🏠 Dashboard
                </a>
            </div>
        </div>
    @endif
</div>

<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('resetBattleAnimation', () => {
            setTimeout(() => {
                @this.showBattleAnimation = false;
            }, 2000);
        });
    });
</script>
