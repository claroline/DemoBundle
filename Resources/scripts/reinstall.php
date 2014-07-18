<?php

$rootPath = __DIR__ . '/../../../../../../..';
$console = 'php ' . $rootPath . '/app/console ';

system($console . 'claroline:demo:refresh');

//if you want to load some fixtures, do it here.
system($console . 'claroline:fixture:demo');

system('chmod -R 777 ' . $rootPath . '/app/cache/*');