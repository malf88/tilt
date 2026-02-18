@extends('layouts.app')

@section('content')
<div class="max-w-md mx-auto">
    <div class="bg-white rounded-lg shadow-xl p-8">
        <div class="text-center mb-6">
            <div class="text-6xl mb-4">🐾</div>
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Crie Seu Pet!</h2>
            <p class="text-gray-600">Escolha um nome único para seu companheiro virtual</p>
        </div>

        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="/pet" method="POST" class="space-y-6">
            @csrf
            
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                    Nome do Pet
                </label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    value="{{ old('name') }}"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Ex: Fluffy, Shadow, Luna..."
                    required
                    minlength="2"
                    maxlength="50"
                    autofocus
                >
                <p class="mt-2 text-sm text-gray-500">
                    O nome deve ter entre 2 e 50 caracteres
                </p>
            </div>

            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                <h3 class="font-semibold text-blue-900 mb-2">Atributos Iniciais:</h3>
                <ul class="text-sm text-blue-800 space-y-1">
                    <li>❤️ Saúde: 100</li>
                    <li>🍖 Fome: 0</li>
                    <li>💪 Nível de Treinamento: 0</li>
                </ul>
            </div>

            <button 
                type="submit" 
                class="w-full bg-blue-500 hover:bg-blue-600 text-white font-bold py-3 px-4 rounded-lg transition-colors duration-200"
            >
                🎉 Criar Pet
            </button>
        </form>
    </div>

    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Dica: Escolha um nome que você goste, pois não poderá mudá-lo depois!
        </p>
    </div>
</div>

<script>
    // Client-side validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const nameInput = document.getElementById('name');
        const name = nameInput.value.trim();
        
        if (name.length < 2) {
            e.preventDefault();
            alert('O nome deve ter pelo menos 2 caracteres!');
            nameInput.focus();
            return false;
        }
        
        if (name.length > 50) {
            e.preventDefault();
            alert('O nome deve ter no máximo 50 caracteres!');
            nameInput.focus();
            return false;
        }
    });
</script>
@endsection
