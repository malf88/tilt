@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">📜 Histórico de Batalhas</h2>
        <p class="text-gray-600">Reveja todas as batalhas do {{ $pet->name }}</p>
    </div>

    @if($battles->isEmpty())
        <div class="bg-white rounded-lg shadow-lg p-12 text-center">
            <div class="text-6xl mb-4">⚔️</div>
            <h3 class="text-2xl font-bold text-gray-700 mb-2">Nenhuma Batalha Ainda</h3>
            <p class="text-gray-600 mb-6">Seu pet ainda não participou de nenhuma batalha.</p>
            <a href="/battle" class="inline-block bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                ⚔️ Iniciar Primeira Batalha
            </a>
        </div>
    @else
        {{-- Statistics --}}
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Total de Batalhas</p>
                <p class="text-3xl font-bold text-gray-800">{{ $battles->count() }}</p>
            </div>
            <div class="bg-green-50 rounded-lg shadow p-4">
                <p class="text-sm text-green-600">Vitórias</p>
                <p class="text-3xl font-bold text-green-700">{{ $battles->where('result', 'win')->count() }}</p>
            </div>
            <div class="bg-red-50 rounded-lg shadow p-4">
                <p class="text-sm text-red-600">Derrotas</p>
                <p class="text-3xl font-bold text-red-700">{{ $battles->where('result', 'loss')->count() }}</p>
            </div>
            <div class="bg-gray-50 rounded-lg shadow p-4">
                <p class="text-sm text-gray-600">Empates</p>
                <p class="text-3xl font-bold text-gray-700">{{ $battles->where('result', 'draw')->count() }}</p>
            </div>
        </div>

        {{-- Battle List --}}
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Data
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Oponente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Dificuldade
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Sua Força
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Força Oponente
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Resultado
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($battles as $battle)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ $battle->fought_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $battle->opponent_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($battle->difficulty === 'easy')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            🟢 Fácil
                                        </span>
                                    @elseif($battle->difficulty === 'medium')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                            🟡 Médio
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            🔴 Difícil
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($battle->pet_strength, 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    {{ number_format($battle->opponent_strength, 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($battle->result === 'win')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                            🏆 Vitória
                                        </span>
                                    @elseif($battle->result === 'loss')
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                            😢 Derrota
                                        </span>
                                    @else
                                        <span class="px-2 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-800">
                                            🤝 Empate
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- Pagination --}}
        @if($battles->hasPages())
            <div class="mt-6">
                {{ $battles->links() }}
            </div>
        @endif

        {{-- Actions --}}
        <div class="mt-6 flex justify-center space-x-4">
            <a href="/battle" class="bg-red-500 hover:bg-red-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                ⚔️ Nova Batalha
            </a>
            <a href="/pet/dashboard" class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-3 px-6 rounded-lg transition-colors duration-200">
                🏠 Voltar ao Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
