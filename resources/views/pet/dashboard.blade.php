@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="text-center mb-8">
        <h2 class="text-4xl font-bold text-gray-800 mb-2">Dashboard do Pet</h2>
        <p class="text-gray-600">Cuide bem do seu companheiro virtual!</p>
    </div>

    @livewire('pet-dashboard', ['pet' => $pet])

    {{-- Quick Tips --}}
    <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
        <h3 class="text-xl font-semibold mb-4">💡 Dicas Rápidas</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 p-4 rounded-lg">
                <h4 class="font-bold text-green-800 mb-2">🍖 Alimentar</h4>
                <p class="text-sm text-green-700">Reduz fome em 20 e aumenta saúde em 10</p>
            </div>
            <div class="bg-blue-50 p-4 rounded-lg">
                <h4 class="font-bold text-blue-800 mb-2">💪 Treinar</h4>
                <p class="text-sm text-blue-700">Aumenta treinamento em 10, mas requer saúde ≥ 30</p>
            </div>
            <div class="bg-red-50 p-4 rounded-lg">
                <h4 class="font-bold text-red-800 mb-2">⚔️ Batalhar</h4>
                <p class="text-sm text-red-700">Teste suas habilidades contra oponentes!</p>
            </div>
        </div>
    </div>

    {{-- Time Degradation Info --}}
    <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <p class="text-sm text-yellow-800">
            <strong>⏰ Atenção:</strong> A fome do seu pet aumenta 5 pontos a cada 30 minutos. 
            Se a fome ficar acima de 50, a saúde diminui 2 pontos a cada 30 minutos.
        </p>
    </div>
</div>
@endsection
