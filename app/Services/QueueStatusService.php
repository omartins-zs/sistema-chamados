<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class QueueStatusService
{
    private const string CACHE_CHAVE = 'queue.worker.heartbeat';

    public function registrarHeartbeat(): void
    {
        Cache::put(self::CACHE_CHAVE, now()->toIso8601String(), now()->addMinutes(3));
    }

    /**
     * @return array{
     *     rodando: bool,
     *     rotulo: string,
     *     cor: string,
     *     mensagem: string,
     *     jobs_pendentes: int,
     *     jobs_falhos: int,
     *     ultimo_heartbeat: ?Carbon,
     *     precisa_worker: bool,
     *     driver: string
     * }
     */
    public function obterStatus(): array
    {
        $driver = (string) config('queue.default');
        $jobsPendentes = $this->contarJobsPendentes();
        $jobsFalhos = $this->contarJobsFalhos();
        $ultimoHeartbeat = $this->obterUltimoHeartbeat();

        if ($driver === 'sync') {
            return [
                'rodando' => true,
                'rotulo' => 'Processamento imediato',
                'cor' => 'info',
                'mensagem' => 'A fila está em modo síncrono — os jobs são executados na hora, sem worker.',
                'jobs_pendentes' => 0,
                'jobs_falhos' => $jobsFalhos,
                'ultimo_heartbeat' => null,
                'precisa_worker' => false,
                'driver' => $driver,
            ];
        }

        $rodando = $this->workerEstaAtivo($ultimoHeartbeat) || $this->processoWorkerDetectado();

        return [
            'rodando' => $rodando,
            'rotulo' => $rodando ? 'Worker ativo' : 'Worker parado',
            'cor' => $rodando ? 'success' : 'danger',
            'mensagem' => $rodando
                ? 'O processamento de fila está em execução. E-mails enfileirados serão enviados automaticamente.'
                : 'Nenhum worker detectado. Execute php artisan queue:work em um terminal separado.',
            'jobs_pendentes' => $jobsPendentes,
            'jobs_falhos' => $jobsFalhos,
            'ultimo_heartbeat' => $ultimoHeartbeat,
            'precisa_worker' => true,
            'driver' => $driver,
        ];
    }

    private function obterUltimoHeartbeat(): ?Carbon
    {
        $valor = Cache::get(self::CACHE_CHAVE);

        if (! is_string($valor) || $valor === '') {
            return null;
        }

        return Carbon::parse($valor);
    }

    private function workerEstaAtivo(?Carbon $ultimoHeartbeat): bool
    {
        if ($ultimoHeartbeat === null) {
            return false;
        }

        return $ultimoHeartbeat->greaterThan(now()->subMinutes(2));
    }

    private function processoWorkerDetectado(): bool
    {
        if (! function_exists('shell_exec')) {
            return false;
        }

        if (PHP_OS_FAMILY === 'Windows') {
            $saida = shell_exec('wmic process where "name=\'php.exe\'" get commandline 2>NUL');

            if (! is_string($saida)) {
                return false;
            }

            return str_contains($saida, 'queue:work')
                || str_contains($saida, 'queue:listen');
        }

        $saida = shell_exec('pgrep -af "artisan queue:(work|listen)" 2>/dev/null');

        return is_string($saida) && trim($saida) !== '';
    }

    private function contarJobsPendentes(): int
    {
        if (! Schema::hasTable('jobs')) {
            return 0;
        }

        return (int) DB::table('jobs')->count();
    }

    private function contarJobsFalhos(): int
    {
        if (! Schema::hasTable('failed_jobs')) {
            return 0;
        }

        return (int) DB::table('failed_jobs')->count();
    }
}
