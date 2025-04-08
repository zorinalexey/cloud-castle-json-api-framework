<?php

use CloudCastle\Core\App;
use CloudCastle\Core\Config;
use CloudCastle\Core\Controllers\ErrorController;
use CloudCastle\Core\Env;
use CloudCastle\Core\Request\Request;
use CloudCastle\Core\Router\Router;

define('APP_ROOT', dirname(__FILE__, 2));

$data = [];

try {
    require_once APP_ROOT . '/vendor/autoload.php';
    Request::getInstance();
    $env = Env::getInstance();
    $config = Config::getInstance();
    $app = App::getInstance();
    $app->set('config', $config)->set('env', $env)->set('APP_ROOT', APP_ROOT);
    $env->init(APP_ROOT . DIRECTORY_SEPARATOR . '.env');
    date_default_timezone_set($env->get('APP_TIMEZONE'));
    $config->init(APP_ROOT . DIRECTORY_SEPARATOR . 'config');
    
    if (!str_contains(mb_strtolower($env->get('APP_ENV', 'dev')), 'prod')) {
        ini_set('display_errors', 1);
        ini_set('display_startup_errors', 1);
        error_reporting(E_ALL);
    }

    if (APP === 'WEB') {
        session_start();
        
        foreach (scan_dir(APP_ROOT . DIRECTORY_SEPARATOR . 'routes') as $file) {
            require_once $file;
        }
        
        $data = Router::run();
    }elseif(APP === 'CLI'){
        echo 'Start CLI-Mode:'.date('Y-m-d H:i:s').PHP_EOL;
    }else{
        throw new Exception ("Application not configured");
    }
} catch (Throwable $t) {
    $controller = Router::checkController(config('app.fatal_error_controller', ErrorController::class));
    $action = Router::checkAction($controller, config('app.fatal_error_action', 'page500'));
    $data = $controller->{$action}($t);
}

var_dump($data);

define('END_TIME', microtime(true));

#var_dump(END_TIME - START_TIME);