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
