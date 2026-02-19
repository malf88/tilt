@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-3xl shadow-lg p-8">
        <div class="text-center mb-8">
            <div class="text-6xl mb-4">🐾</div>
            <h2 class="text-2xl font-bold text-gray-800 mb-2">Crie Seu Pet</h2>
            <p class="text-gray-600">Escolha um nome único para seu companheiro</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border-l-4 border-red-400 text-red-700 px-4 py-3 rounded-2xl mb-6">
                <ul class="text-sm space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/pet" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">
                    Escolha seu Bichinho
                </label>
                <div class="grid grid-cols-3 gap-3">
                    @foreach(\App\Models\Pet::PET_TYPES as $type => $emoji)
                        <label class="cursor-pointer">
                            <input type="radio" name="pet_type" value="{{ $type }}" class="peer hidden" {{ old('pet_type', 'dog') === $type ? 'checked' : '' }} required>
                            <div class="peer-checked:ring-4 peer-checked:ring-blue-300 peer-checked:scale-105 transition-all rounded-2xl p-4 bg-gray-50 hover:bg-gray-100 flex flex-col items-center">
                                <x-pet-avatar :type="$type" size="sm" class="mb-2" />
                                <span class="text-xs font-medium text-gray-700 capitalize">{{ $type }}</span>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome do Pet
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 bg-gray-50 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-300 focus:border-blue-300 transition-all"
                    placeholder="Ex: Fluffy, Shadow, Luna..."
                    required
                    minlength="2"
                    maxlength="50"
                    autofocus
                >
                <p class="mt-2 text-sm text-gray-500">
                    Entre 2 e 50 caracteres
                </p>
            </div>

            <div class="bg-purple-50 rounded-2xl p-5">
                <h3 class="font-semibold text-gray-800 mb-3">Atributos Iniciais</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between items-center bg-white rounded-xl px-3 py-2">
                        <span class="text-gray-700">❤️ Saúde</span>
                        <span class="font-bold text-red-500">100</span>
                    </div>
                    <div class="flex justify-between items-center bg-white rounded-xl px-3 py-2">
                        <span class="text-gray-700">🍖 Fome</span>
                        <span class="font-bold text-orange-500">0</span>
                    </div>
                    <div class="flex justify-between items-center bg-white rounded-xl px-3 py-2">
                        <span class="text-gray-700">💪 Treinamento</span>
                        <span class="font-bold text-purple-500">0</span>
                    </div>
                </div>
            </div>

            <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-blue-400 to-purple-400 hover:from-blue-500 hover:to-purple-500 text-white font-semibold py-4 px-4 rounded-2xl transition-all shadow-lg hover:shadow-xl"
            >
                Criar Pet
            </button>
        </form>
    </div>
</div>
@endsection
