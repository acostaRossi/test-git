<?php

namespace Deployer;

require '../vendor/autoload.php';
require 'recipe/common.php';
// Config

set('repository', 'https://github.com/acostaRossi/test-git.git');

// Cartella dove Deployer organizzerà le release (current, releases, shared)
set('deploy_path', '/Users/albertocosta/Dev/Scuola/202526/altro/lamp-docker/test-git-deploy');

localhost()
    ->setLabels(['stage' => 'local']);

// ... configurazione host ...

task('deploy:check_changes', function () {
    // 1. Ottieni l'ultimo commit deployato sul server
    // (Deployer salva l'hash nel file REVISION all'interno della cartella current)
    $lastDeployed = run('cat {{current_path}}/REVISION 2>/dev/null || echo "none"');

    // 2. Ottieni l'ultimo commit locale (o dal repo remoto)
    $currentCommit = runLocally('git rev-parse HEAD');

    if ($lastDeployed === $currentCommit) {
        writeln("<info>Nessun nuovo cambiamento rilevato. Deploy annullato.</info>");
        // Interrompe l'esecuzione di Deployer in modo pulito
        throw new \Deployer\Exception\GracefulShutdownException("Già aggiornato.");
    }
});

// Esegui il controllo come primissima cosa
before('deploy:prepare', 'deploy:check_changes');

// Definizione del flusso di deploy
task('deploy', [
    'deploy:prepare',
    'deploy:clear_paths',
    'deploy:update_code',    // Fa il git clone/pull nella cartella release
    'deploy:shared',         // Gestisce file condivisi come .env
    'deploy:vendors',        // Composer install
    'deploy:publish',        // Crea il link simbolico 'current'
    'deploy:success'
]);

after('deploy:failed', 'deploy:unlock');
