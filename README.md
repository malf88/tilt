# Virtual Pet Battle Game 🐾

Um jogo de batalha de pets virtuais com design limpo e minimalista, construído com NativePHP, Laravel, Livewire e TailwindCSS.

## ✨ Características

- 🎨 **Design Limpo**: Interface minimalista e profissional
- 🐾 **Cuide do Seu Pet**: Alimente e mantenha seu pet saudável
- 💪 **Sistema de Treinamento**: Treine seu pet para aumentar sua força
- ⚔️ **Batalhas**: Enfrente oponentes em 3 níveis de dificuldade
- 📊 **Estatísticas**: Acompanhe o progresso do seu pet
- 📜 **Histórico**: Reveja todas as suas batalhas
- ⏰ **Degradação Temporal**: Seu pet fica com fome com o tempo
- 🎯 **Property-Based Testing**: 104 testes garantindo correção

## 🎮 Como Jogar

1. **Crie seu Pet**: Escolha um nome único
2. **Cuide dele**: Alimente para manter saúde alta e fome baixa
3. **Treine**: Aumente o nível de treinamento (requer saúde ≥ 30)
4. **Batalhe**: Escolha a dificuldade e enfrente oponentes
5. **Vença**: Ganhe bônus de treinamento com vitórias

## 📋 Requisitos

- PHP 8.2+
- Composer
- Node.js 22+ & npm (para TailwindCSS e NativePHP desktop app)
- SQLite

**Nota**: A versão web funciona com qualquer versão do Node.js. Node.js 22+ é necessário apenas para o aplicativo desktop NativePHP.

## Installation

1. Install PHP dependencies:
```bash
composer install
```

2. Install Node.js dependencies (when Node.js is available):
```bash
npm install
```

3. Configure environment:
```bash
cp .env.example .env
php artisan key:generate
```

4. Create SQLite database:
```bash
touch database/database.sqlite
```

5. Run migrations:
```bash
php artisan migrate
```

6. Build assets (when Node.js is available):
```bash
npm run build
```

## Development

### Running Tests

```bash
php artisan test
# or
./vendor/bin/pest
```

### Running the Application

For web development (recommended):
```bash
php artisan serve
```

Then visit http://localhost:8000 in your browser.

For native desktop application:
```bash
# Requires Node.js 22+ 
# Check your version: node --version
# If you have Node.js 18.x, upgrade to 22+ first
php artisan native:serve
```

**Troubleshooting NativePHP**:
- If you see "Unsupported engine" errors, you need Node.js 22+
- Current Node.js version detected: v18.19.1
- Upgrade Node.js: https://nodejs.org/ or use nvm/n version managers

## Project Structure

```
app/
├── Contracts/          # Service interfaces
├── Services/           # Business logic services
├── Repositories/       # Data access layer
├── Models/            # Eloquent models
├── Http/
│   ├── Controllers/   # HTTP controllers
│   └── Livewire/      # Livewire components
└── Providers/         # Service providers

tests/
├── Unit/              # Unit tests
├── Property/          # Property-based tests
└── Feature/           # Integration tests
```

## Technology Stack

- **NativePHP**: Desktop application wrapper
- **Laravel 10+**: Backend framework
- **Livewire**: Reactive UI components
- **TailwindCSS**: Styling
- **PestPHP**: Testing framework
- **SQLite**: Local database

## License

MIT
