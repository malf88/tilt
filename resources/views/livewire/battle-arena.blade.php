<div class="battle-arena p-6 max-w-4xl mx-auto">
    <h2 class="text-3xl font-bold text-center mb-6">⚔️ Arena de Batalha</h2>

    @if(!$currentBattle)
        {{-- Difficulty Selection --}}
        <div class="mb-8">
            <h3 class="text-xl font-semibold mb-4 text-center">Escolha a Dificuldade</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                {{-- Easy --}}
                <button wire:click="selectDifficulty('easy')" 
                        class="p-6 rounded-lg border-2 transition-all duration-200 {{ $difficulty === 'easy' ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-300' }}">
                    <div class="text-4xl mb-2">🟢</div>
                    <h4 class="font-bold text-lg mb-2">Fácil</h4>
                    <p class="text-sm text-gray-600">Força: 20-40</p>
                    <p class="text-xs text-gray-500 mt-2">Ideal para iniciantes</p>
                </button>

                {{-- Medium --}}
                <button wire:click="selectDifficulty('medium')" 
                        class="p-6 rounded-lg border-2 transition-all duration-200 {{ $difficulty === 'medium' ? 'border-yellow-500 bg-yellow-50' : 'border-gray-300 hover:border-yellow-300' }}">
                    <div class="text-4xl mb-2">🟡</div>
                    <h4 class="font-bold text-lg mb-2">Médio</h4>
                    <p class="text-sm text-gray-600">Força: 40-70</p>
                    <p class="text-xs text-gray-500 mt-2">Desafio equilibrado</p>
                </button>

                {{-- Hard --}}
                <button wire:click="selectDifficulty('hard')" 
                        class="p-6 rounded-lg border-2 transition-all duration-200 {{ $difficulty === 'hard' ? 'border-red-500 bg-red-50' : 'border-gray-300 hover:border-red-300' }}">
                    <div class="text-4xl mb-2">🔴</div>
                    <h4 class="font-bold text-lg mb-2">Difícil</h4>
                    <p class="text-sm text-gray-600">Força: 70-95</p>
                    <p class="text-xs text-gray-500 mt-2">Para mestres</p>
                </button>
            </div>
        </div>

        {{-- Pet Stats --}}
        <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
            <h3 class="text-lg font-semibold mb-4">Seu Pet: {{ $pet->name }}</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <p class="text-sm text-gray-600">Saúde</p>
                    <p class="text-2xl font-bold">{{ $pet->health }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Treinamento</p>
                    <p class="text-2xl font-bold">{{ $pet->training_level }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Fome</p>
                    <p class="text-2xl font-bold">{{ $pet->hunger }}</p>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Força de Batalha</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ number_format($pet->battle_strength, 1) }}</p>
                </div>
            </div>
        </div>

        {{-- Start Battle Button --}}
        <div class="text-center">
            <button wire:click="startBattle" 
                    class="bg-red-500 hover:bg-red-600 text-white font-bold py-4 px-8 rounded-lg text-xl transition-colors duration-200">
                ⚔️ Iniciar Batalha!
            </button>
        </div>
    @else
        {{-- Battle Result --}}
        <div class="bg-white rounded-lg shadow-lg p-8">
            {{-- Battle Animation --}}
            @if($showBattleAnimation)
                <div class="text-center mb-6">
                    <div class="text-6xl animate-pulse">⚔️</div>
                    <p class="text-xl font-semibold mt-4">Batalha em andamento...</p>
                </div>
            @endif

            {{-- Combatants --}}
            <div class="grid grid-cols-2 gap-8 mb-8">
                {{-- Your Pet --}}
                <div class="text-center">
                    <div class="text-5xl mb-2">🐾</div>
                    <h4 class="font-bold text-lg">{{ $pet->name }}</h4>
                    <p class="text-sm text-gray-600">Força: {{ number_format($currentBattle->pet_strength, 1) }}</p>
                </div>

                {{-- VS --}}
                <div class="flex items-center justify-center">
                    <span class="text-4xl font-bold text-gray-400">VS</span>
                </div>

                {{-- Opponent --}}
                <div class="text-center">
                    <div class="text-5xl mb-2">👾</div>
                    <h4 class="font-bold text-lg">{{ $opponent['name'] }}</h4>
                    <p class="text-sm text-gray-600">Força: {{ number_format($opponent['strength'], 1) }}</p>
                </div>
            </div>

            {{-- Result --}}
            <div class="text-center mb-6">
                @if($currentBattle->result === 'win')
                    <div class="bg-green-100 border-2 border-green-500 rounded-lg p-6">
                        <div class="text-6xl mb-2">🏆</div>
                        <h3 class="text-2xl font-bold text-green-700 mb-2">Vitória!</h3>
                        <p class="text-gray-700">Seu pet venceu a batalha!</p>
                        <p class="text-sm text-gray-600 mt-2">+5 Nível de Treinamento</p>
                    </div>
                @elseif($currentBattle->result === 'loss')
                    <div class="bg-red-100 border-2 border-red-500 rounded-lg p-6">
                        <div class="text-6xl mb-2">😢</div>
                        <h3 class="text-2xl font-bold text-red-700 mb-2">Derrota</h3>
                        <p class="text-gray-700">Seu pet foi derrotado.</p>
                        <p class="text-sm text-gray-600 mt-2">Continue treinando!</p>
                    </div>
                @else
                    <div class="bg-gray-100 border-2 border-gray-500 rounded-lg p-6">
                        <div class="text-6xl mb-2">🤝</div>
                        <h3 class="text-2xl font-bold text-gray-700 mb-2">Empate!</h3>
                        <p class="text-gray-700">A batalha terminou empatada.</p>
                        <p class="text-sm text-gray-600 mt-2">Forças iguais!</p>
                    </div>
                @endif
            </div>

            {{-- Battle Effects --}}
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
                <p class="text-sm font-semibold mb-2">Efeitos da Batalha:</p>
                <ul class="text-sm text-gray-700 space-y-1">
                    <li>• Fome aumentou em 10 pontos</li>
                    <li>• Saúde reduziu em 10 pontos</li>
                    @if($currentBattle->result === 'win')
                        <li class="text-green-600 font-semibold">• Nível de Treinamento aumentou em 5 pontos!</li>
                    @endif
                </ul>
            </div>

            {{-- Action Buttons --}}
            <div class="grid grid-cols-2 gap-4">
                <button wire:click="resetBattle" 
                        class="bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                    🔄 Nova Batalha
                </button>
                <a href="/pet/dashboard" 
                   class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg text-center transition-colors duration-200">
                    🏠 Voltar ao Dashboard
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
