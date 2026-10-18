<?php
// ============================================
// PIUL CELL — Front Controller
// ============================================
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
define('APP_PATH', BASE_PATH . '/app');

// Sessions
ini_set('session.save_path', BASE_PATH . '/storage/sessions');
ini_set('session.gc_maxlifetime', '86400');
session_start();

// Error reporting
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/logs/php-error.log');

// Autoload core classes
spl_autoload_register(function ($class) {
    $paths = [
        APP_PATH . '/Core/' . $class . '.php',
        APP_PATH . '/Controllers/' . str_replace('\\', '/', $class) . '.php',
        APP_PATH . '/Models/' . $class . '.php',
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) { require $p; return; }
    }
});

require APP_PATH . '/Core/helpers.php';
require APP_PATH . '/Core/Database.php';
require APP_PATH . '/Core/Controller.php';
require APP_PATH . '/Core/Router.php';

// Bring all admin/owner controllers in one shot - load Core first (defines AdminBaseController)
foreach (glob(APP_PATH . '/Controllers/*.php') as $f) require_once $f;
require_once APP_PATH . '/Controllers/Admin/AdminCore.php';
foreach (glob(APP_PATH . '/Controllers/Admin/*.php') as $f) require_once $f;
foreach (glob(APP_PATH . '/Controllers/Owner/*.php') as $f) require_once $f;

require APP_PATH . '/routes.php';

$router->dispatch();
