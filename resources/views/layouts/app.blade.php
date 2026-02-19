<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Virtual Pet Battle') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 min-h-screen">
    {{-- Header --}}
    <header class="bg-white/80 backdrop-blur-sm shadow-sm">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-3 group">
                    <span class="text-3xl transition-transform group-hover:scale-110">🐾</span>
                    <div>
                        <h1 class="text-xl font-bold text-gray-800">Virtual Pet Battle</h1>
                        <p class="text-xs text-gray-500">Cuide e batalhe</p>
                    </div>
                </a>
                
                @if(isset($pet))
                    <nav class="flex items-center space-x-2">
                        <div class="hidden md:flex items-center px-4 py-2 bg-purple-100 rounded-full">
                            <span class="text-sm font-medium text-purple-700">{{ $pet->name }}</span>
                        </div>
                        <a href="/pet/dashboard" class="px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-blue-100 transition-colors">
                            Dashboard
                        </a>
                        <a href="/battle" class="px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-red-100 transition-colors">
                            Batalhar
                        </a>
                        <a href="/battle/history" class="px-4 py-2 rounded-full text-sm font-medium text-gray-700 hover:bg-purple-100 transition-colors">
                            Histórico
                        </a>
                    </nav>
                @endif
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white/80 backdrop-blur-sm mt-12 py-6">
        <div class="container mx-auto px-4 text-center">
            <p class="text-gray-600 text-sm">
                &copy; {{ date('Y') }} Virtual Pet Battle Game
            </p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
