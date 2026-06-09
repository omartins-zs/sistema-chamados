<?php

/**
 * Executa a suíte de qualidade do projeto.
 * Usa cobertura mínima de 90% quando PCOV ou Xdebug estiver disponível.
 */
$root = dirname(__DIR__);

$commands = [
    [PHP_BINARY, $root.'/vendor/bin/pint', '--test'],
    [PHP_BINARY, $root.'/vendor/bin/phpstan', 'analyse'],
];

foreach ($commands as $command) {
    echo "\n>>> ".implode(' ', $command)."\n";

    $process = proc_open(
        $command,
        [
            0 => ['pipe', 'r'],
            1 => ['pipe', 'w'],
            2 => ['pipe', 'w'],
        ],
        $pipes,
        $root,
    );

    if (! is_resource($process)) {
        echo 'Falha ao executar: '.implode(' ', $command)."\n";
        exit(1);
    }

    fclose($pipes[0]);
    echo stream_get_contents($pipes[1]);
    echo stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $exitCode = proc_close($process);

    if ($exitCode !== 0) {
        exit($exitCode);
    }
}

$hasCoverage = extension_loaded('pcov') || extension_loaded('xdebug');

if ($hasCoverage) {
    echo "\n>>> ".PHP_BINARY." artisan test --coverage --min=90\n";
    passthru(PHP_BINARY.' artisan test --coverage --min=90', $exitCode);
} else {
    echo "\n>>> Aviso: PCOV/Xdebug não encontrado. Executando testes sem cobertura.\n";
    echo ">>> Instale PCOV para validar cobertura mínima de 90% localmente.\n";
    echo ">>> O CI (GitHub Actions) valida cobertura com PCOV.\n\n";
    passthru(PHP_BINARY.' artisan test', $exitCode);
}

exit($exitCode);
