@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-2">Histórico de Batalhas</h2>
        <p class="text-gray-600">Todas as batalhas de {{ $pet->name }}</p>
    </div>

    @if($battles->isEmpty())
        <div class="bg-white rounded-3xl shadow-lg p-12 text-center">
            <div class="text-7xl mb-4">⚔️</div>
            <h3 class="text-2xl font-bold text-gray-700 mb-2">Nenhuma Batalha Ainda</h3>
            <p class="text-gray-600 mb-6">Seu pet ainda não participou de nenhuma batalha</p>
            <a href="/battle" class="inline-block bg-gradient-to-r from-red-400 to-rose-400 hover:from-red-500 hover:to-rose-500 text-white font-semibold py-3 px-8 rounded-full transition-all shadow-lg">
                ⚔️ Iniciar Primeira Batalha
            </a>
        </div>
    @else
        {{-- Statistics --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-3xl shadow-lg p-6">
                <p class="text-sm text-gray-600 mb-1">Total</p>
                <p class="text-4xl font-bold text-gray-800">{{ $battles->count() }}</p>
            </div>
            <div class="bg-green-100 rounded-3xl shadow-lg p-6">
                <p class="text-sm text-green-700 mb-1">Vitórias</p>
                <p class="text-4xl font-bold text-green-600">{{ $battles->where('result', 'win')->count() }}</p>
            </div>
            <div class="bg-red-100 rounded-3xl shadow-lg p-6">
                <p class="text-sm text-red-700 mb-1">Derrotas</p>
                <p class="text-4xl font-bold text-red-600">{{ $battles->where('result', 'loss')->count() }}</p>
            </div>
            <div class="bg-gray-100 rounded-3xl shadow-lg p-6">
                <p class="text-sm text-gray-700 mb-1">Empates</p>
                <p class="text-4xl font-bold text-gray-600">{{ $battles->where('result', 'draw')->count() }}</p>
            </div>
        </div>

        {{-- Battle List --}}
        <div class="bg-white rounded-3xl shadow-lg overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-purple-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Data</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Oponente</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Dificuldade</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Sua Força</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Força Oponente</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">Resultado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach($battles as $battle)
                            <tr class="hover:bg-purple-50 transition-colors">
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-800">
                                    {{ $battle->fought_at->format('d/m/Y H:i') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-800">
                                    {{ $battle->opponent_name }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($battle->difficulty === 'easy')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                            Fácil
                                        </span>
                                    @elseif($battle->difficulty === 'medium')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-700">
                                            Médio
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                            Difícil
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-blue-600">
                                    {{ number_format($battle->pet_strength, 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-red-600">
                                    {{ number_format($battle->opponent_strength, 1) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($battle->result === 'win')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-700">
                                            Vitória
                                        </span>
                                    @elseif($battle->result === 'loss')
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-700">
                                            Derrota
                                        </span>
                                    @else
                                        <span class="px-3 py-1 text-xs font-semibold rounded-full bg-gray-100 text-gray-700">
                                            Empate
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
            <a href="/battle" class="bg-gradient-to-r from-red-400 to-rose-400 hover:from-red-500 hover:to-rose-500 text-white font-semibold py-3 px-8 rounded-full transition-all shadow-lg">
                ⚔️ Nova Batalha
            </a>
            <a href="/pet/dashboard" class="bg-gradient-to-r from-gray-400 to-gray-500 hover:from-gray-500 hover:to-gray-600 text-white font-semibold py-3 px-8 rounded-full transition-all shadow-lg">
                🏠 Dashboard
            </a>
        </div>
    @endif
</div>
@endsection
