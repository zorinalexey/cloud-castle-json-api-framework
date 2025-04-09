<?php

use CloudCastle\Core\App;
use CloudCastle\Core\Config;
use CloudCastle\Core\Console\TerminalColor;
use CloudCastle\Core\Controllers\ErrorController;
use CloudCastle\Core\Env;
use CloudCastle\Core\Request\Request;
use CloudCastle\Core\Router\Router;

define('START_TIME', microtime(true));
define('APP_ROOT', dirname(__FILE__, 2));

$data = [];

try {
    require_once APP_ROOT . '/vendor/autoload.php';
    $env = Env::getInstance();
    $config = Config::getInstance();
    $app = App::getInstance();
    $app->set('config', $config)->set('env', $env)->set('APP_ROOT', APP_ROOT);
    $env->init(APP_ROOT . DIRECTORY_SEPARATOR . '.env');
    date_default_timezone_set($env->get('APP_TIMEZONE'));
    $config->init(APP_ROOT . DIRECTORY_SEPARATOR . 'config');
    define('APP_LANG', session('lang', cookies('lang', config('app.default_lang', env('APP_LANG', 'ru')))));
    Request::getInstance();
    
    if (!str_contains(mb_strtolower($env->get('APP_ENV', 'dev')), 'prod')) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }
    
    foreach (scan_dir(APP_ROOT . DIRECTORY_SEPARATOR . 'routes') as $file) {
        require_once $file;
    }
    
    if (APP === 'WEB') {
        session_start();
        
        $data = Router::run();
        var_dump($data);
    }elseif(APP === 'CLI'){
        echo TerminalColor::blue('Start CLI-Mode: '.date('Y-m-d H:i:s')).PHP_EOL;
    }else{
        throw new Exception ("Application not configured");
    }
} catch (Throwable $t) {
    $controller = Router::checkController(config('app.fatal_error_controller', ErrorController::class));
    $action = Router::checkAction($controller, config('app.fatal_error_action', 'page500'));
    $data = $controller->{$action}($t);
}

define('END_TIME', microtime(true));
