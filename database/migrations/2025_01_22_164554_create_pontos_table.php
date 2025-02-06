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
        Schema::create('pontos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');  // Relacionamento com a tabela users
            $table->dateTime('entrada');  // Hora de entrada
            $table->dateTime('saida')->nullable();  // Hora de saída
            $table->time('horas_trabalhadas')->nullable();
            $table->time('horas_extras')->default('00:00:00');  // Horas extras trabalhadas
            $table->time('atraso')->default('00:00:00');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pontos');
    }
};
