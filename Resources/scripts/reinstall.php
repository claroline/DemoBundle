<?php

$rootPath = __DIR__ . '/../../../../../../..';
$console = 'php ' . $rootPath . '/app/console ';

system($console . 'claroline:demo:refresh');

//if you want to load some fixtures, do it here.
system($console . 'claroline:demo:load');

//removing the maintenance mode.
@unlink($rootPath . '/app/config/.update');

system('chmod -R 777 ' . $rootPath . '/app/cache/*');