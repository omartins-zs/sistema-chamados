<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chamados', function (Blueprint $table) {
            $table->id();
            $table->string('protocolo')->unique();
            $table->string('nome_solicitante');
            $table->string('email_solicitante');
            $table->string('telefone_solicitante');
            $table->string('titulo');
            $table->text('descricao');
            $table->string('complexidade');
            $table->foreignId('setor_id')->constrained('setores')->cascadeOnDelete();
            $table->foreignId('tecnico_responsavel_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('status');
            $table->string('token_avaliacao')->nullable()->unique();
            $table->timestamp('expira_token_avaliacao_em')->nullable();
            $table->timestamp('finalizado_em')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chamados');
    }
};
