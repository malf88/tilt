<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('battles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pet_id')->constrained()->onDelete('cascade');
            $table->string('opponent_name', 50);
            $table->float('opponent_strength');
            $table->float('pet_strength');
            $table->string('result', 10); // 'win', 'loss', 'draw'
            $table->string('difficulty', 10); // 'easy', 'medium', 'hard'
            $table->timestamp('fought_at');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('battles');
    }
};
