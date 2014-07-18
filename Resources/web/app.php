<?php

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';

require_once __DIR__.'/../app/AppKernel.php';

$maintenanceMode = file_exists(__DIR__ . '/../app/config/.update');
$reinstallMode = file_exists(__DIR__ . '/../app/config/.refresh_demo');

if ($maintenanceMode) {
    $url = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/../maintenance.html.php';
    header("Location: http://{$url}");
    return;
}

if ($reinstallMode) {
    $url = $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'] . '/../reinstall.html.php';
    header("Location: http://{$url}");
    return;
}

$request = \Symfony\Component\HttpFoundation\Request::createFromGlobals();
$kernel = new AppKernel('prod', false);
$kernel->loadClassCache();
$kernel->handle($request)->send();
//$kernel->terminate($request, $response);
