<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PetController;
use App\Http\Controllers\BattleController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Home route - redirect to dashboard or pet creation
Route::get('/', function () {
    $pet = \App\Models\Pet::first();
    
    if ($pet) {
        return redirect('/pet/dashboard');
    }
    
    return redirect('/pet/create');
});

// Pet routes
Route::get('/pet/create', function () {
    return view('pet.create');
})->name('pet.create');

Route::post('/pet', [PetController::class, 'store'])->name('pet.store');

Route::get('/pet/dashboard', [PetController::class, 'show'])->name('pet.dashboard');

Route::post('/pet/feed', [PetController::class, 'feed'])->name('pet.feed');

Route::post('/pet/train', [PetController::class, 'train'])->name('pet.train');

// Battle routes
Route::get('/battle', [BattleController::class, 'create'])->name('battle.create');

Route::post('/battle', [BattleController::class, 'store'])->name('battle.store');

Route::get('/battle/history', [BattleController::class, 'history'])->name('battle.history');

