<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Pet Battle</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    <div class="min-h-screen flex flex-col items-center justify-center px-4 py-12">
        {{-- Hero Section --}}
        <div class="text-center mb-12 max-w-3xl">
            <div class="text-8xl mb-6">🐾</div>
            
            <h1 class="text-5xl md:text-6xl font-bold text-gray-800 mb-4">
                Virtual Pet Battle
            </h1>
            
            <p class="text-xl text-gray-600 mb-8">
                Cuide, treine e batalhe com seu pet virtual
            </p>
            
            <a href="/pet/create" 
               class="inline-block bg-gradient-to-r from-blue-400 to-purple-400 hover:from-blue-500 hover:to-purple-500 text-white font-bold py-4 px-10 rounded-full text-lg transition-all shadow-lg hover:shadow-xl">
                Começar Agora
            </a>
        </div>

        {{-- Features --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <div class="bg-white rounded-3xl shadow-lg p-8 text-center">
                <div class="text-6xl mb-4">🍖</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Cuide do Seu Pet</h3>
                <p class="text-sm text-gray-600">
                    Alimente para manter a saúde alta e a fome baixa
                </p>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 text-center">
                <div class="text-6xl mb-4">💪</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Treine e Evolua</h3>
                <p class="text-sm text-gray-600">
                    Aumente o nível e a força de batalha do seu pet
                </p>
            </div>

            <div class="bg-white rounded-3xl shadow-lg p-8 text-center">
                <div class="text-6xl mb-4">⚔️</div>
                <h3 class="text-lg font-bold text-gray-800 mb-2">Batalhe e Vença</h3>
                <p class="text-sm text-gray-600">
                    Enfrente oponentes e ganhe recompensas
                </p>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-16 text-center">
            <p class="text-gray-500 text-sm">
                &copy; {{ date('Y') }} Virtual Pet Battle Game
            </p>
        </div>
    </div>
</body>
</html>
