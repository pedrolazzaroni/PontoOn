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
        Schema::create('relatorios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');  // Relacionamento com a tabela users
            $table->date('data_inicio');  // Data de início do período do relatório
            $table->date('data_fim');  // Data de fim do período do relatório
            $table->integer('total_horas');  // Total de horas trabalhadas no período
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('relatorios');
    }
};
