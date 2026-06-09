<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historicos_chamados', function (Blueprint $table) {
            $table->id();
            $table->foreignId('chamado_id')->constrained('chamados')->cascadeOnDelete();
            $table->foreignId('tecnico_id')->constrained('usuarios')->cascadeOnDelete();
            $table->string('status');
            $table->text('descricao');
            $table->boolean('visivel_solicitante')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historicos_chamados');
    }
};
