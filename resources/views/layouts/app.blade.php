<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ config('app.name', 'Virtual Pet Battle') }}</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body class="bg-gradient-to-br from-purple-100 to-blue-100 min-h-screen">
    {{-- Header --}}
    <header class="bg-white shadow-md">
        <div class="container mx-auto px-4 py-4">
            <div class="flex items-center justify-between">
                <a href="/" class="flex items-center space-x-2">
                    <span class="text-3xl">🐾</span>
                    <h1 class="text-2xl font-bold text-gray-800">Virtual Pet Battle</h1>
                </a>
                
                @if(isset($pet))
                    <div class="flex items-center space-x-4">
                        <span class="text-sm text-gray-600">{{ $pet->name }}</span>
                        <a href="/pet/dashboard" class="text-blue-500 hover:text-blue-700">Dashboard</a>
                        <a href="/battle" class="text-red-500 hover:text-red-700">Batalhar</a>
                        <a href="/battle/history" class="text-purple-500 hover:text-purple-700">Histórico</a>
                    </div>
                @endif
            </div>
        </div>
    </header>

    {{-- Main Content --}}
    <main class="container mx-auto px-4 py-8">
        @yield('content')
    </main>

    {{-- Footer --}}
    <footer class="bg-white mt-12 py-6">
        <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
            <p>&copy; {{ date('Y') }} Virtual Pet Battle Game. Cuide bem do seu pet! 🐾</p>
        </div>
    </footer>

    @livewireScripts
</body>
</html>
