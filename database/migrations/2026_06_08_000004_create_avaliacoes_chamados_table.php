<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('avaliacoes_chamados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chamado_id')->unique()->constrained('chamados')->cascadeOnDelete();
            $table->unsignedTinyInteger('nota_satisfacao');
            $table->unsignedTinyInteger('nota_tempo_resolucao');
            $table->text('comentario')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('avaliacoes_chamados');
    }
};
