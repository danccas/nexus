<?php
declare(strict_types=1);
if(isset($_GET['demo'])) {
	print_r($_SERVER);
	exit;
}
if (getenv('OS') == 'Windows_NT') {
    ini_set('display_errors', '1');
    ini_set('display_startup_errors', '1');
    error_reporting(E_ALL & ~E_NOTICE);
}
define('DEBUG_START', microtime(true));

require __DIR__ . '/vendor/autoload.php';


$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->kernel();

$kernel->terminate();
