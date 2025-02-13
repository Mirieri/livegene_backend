<?php
namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'www');

// Other settings
set('env', ['APP_ENV'=>'prod']);
set('http_user', 'liveged');
set('writable_mode', 'chmod');

// Project repository
set('repository', 'git@github.com:cezar77/livegene_backend.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true); 

// Shared files/dirs between deploys 
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server 
add('writable_dirs', []);


// Hosts

host('ovh')
    ->set('deploy_path', '~/{{application}}');    
    
// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'database:migrate');

