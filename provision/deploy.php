<?php
namespace Deployer;
require 'recipe/magento2.php';

// Configuration
// ---------------------------------------------------------------------------------------------------------------------

set('repository', 'git@bitbucket.org:magma/corsom2-magento21.git');
set('branch','master');

host('staging')
    ->hostname('stage.magento21.magma.consulting')
    ->user('deploy')
    ->identityFile('~/.ssh/id_rsa')
    ->set('deploy_path', '/home/deploy/stage-magento21');

host('production')
    ->hostname('magento21.magma.consulting')
    ->user('deploy')
    ->identityFile('~/.ssh/id_rsa')
    ->set('deploy_path', '/home/deploy/stage-magento21');

set('default_stage', 'staging');

set('shared_files', [
    'app/etc/env.php',
    'app/etc/config.php',
    'nginx.conf',
//    'auth.json'
]);
set('shared_dirs', [
    'var',
    'pub/media',
    'sitemap'
]);
set('writable_dirs',[]);
set('clear_paths', [
    'var/generation/*',
    'var/cache/*',
    'var/di/*',
    'pub/static/*',
    'var/view_preprocessed/*'
]);

// Custom Tasks
// ---------------------------------------------------------------------------------------------------------------------


desc('Backup deployment machine database (in /var/backups)');
task('magento:backup:db',function() {
    run("{{bin/php}} {{release_path}}/bin/magento setup:backup --db");
});
before('deploy:magento','magento:backup:db');


desc('Deploy assets');
task('magento:deploy:assets', function () {
    $cmd ="{{bin/php}} {{release_path}}/bin/magento setup:static-content:deploy ";
    run("$cmd -s standard -t Magento/luma en_US");
    run("$cmd -s standard -t Magento/luma it_IT");
    run("$cmd -s standard --area=adminhtml en_US");
    run("$cmd -s standard --area=adminhtml it_IT");
    run("$cmd --area=adminhtml en_US ");
    run("$cmd --area=adminhtml it_IT ");
});


desc('Restart PHP-FPM service');
task('php-fpm:restart', function () {
    run('sudo /bin/systemctl restart php7.0-fpm');
});
after('deploy:magento','php-fpm:restart');

task('dev:sync:db',function(){
    run("{{bin/php}} {{release_path}}/bin/magento  setup:backup --db");
    run("cd {{deploy_path}}/shared/var/backups && cp $(ls -t| head -1) latest.sql");
    download("{{deploy_path}}/shared/var/backups/latest.sql","var/backups/latest.sql");
    runLocally("m2 setup:backup --db");
    runLocally("mr2 db:import var/backups/latest.sql");
    runLocally("mr2 script < var/development.script");
    runLocally("m2 setup:upgrade");
})->desc('Sync db from stage to local dev');

task('dev:sync:media',function() {
    $host = host('staging');
    $cmd = "rsync -avz $host:{{deploy_path}}/shared/pub/media/ pub/media/ ";
    write($cmd);
    runLocally($cmd);
})->desc('Sync media from stage to local dev');

// default Tasks
/*
desc('Magento2 deployment operations');
task('deploy:magento', [
    'magento:enable',
    'magento:compile',
    'magento:deploy:assets',
    'magento:maintenance:enable',
    'magento:upgrade:db',
    'magento:cache:flush',
    'magento:maintenance:disable'
]);
desc('Deploy your project');
task('deploy', [
    'deploy:info',
    'deploy:prepare',
    'deploy:lock',
    'deploy:release',
    'deploy:update_code',
    'deploy:shared',
    'deploy:writable',
    'deploy:vendors',
    'deploy:clear_paths',
    'deploy:magento',
    'deploy:symlink',
    'deploy:unlock',
    'cleanup',
    'success'
]);
*/
