<?php

use CloudCastle\Core\App;
use CloudCastle\Core\Config;
use CloudCastle\Core\Console\TerminalColor;
use CloudCastle\Core\Controllers\ErrorController;
use CloudCastle\Core\Env;
use CloudCastle\Core\Request\Request;
use CloudCastle\Core\Router\Router;

if (!defined('START_TIME')) {
    define('START_TIME', microtime(true));
}

try {
    define('APP_ROOT', dirname(__FILE__, 2));
    
    $data = '';
    $defaultLang = 'ru-RU';
    require_once APP_ROOT . '/vendor/autoload.php';
    $env = Env::getInstance();
    $config = Config::getInstance();
    $app = App::getInstance();
    $app->set('config', $config)->set('env', $env)->set('APP_ROOT', APP_ROOT);
    $env->init(APP_ROOT . DIRECTORY_SEPARATOR . '.env');
    date_default_timezone_set(session('time_zone', cookies('time_zone', config('app.time_zone', $env->get('APP_TIMEZONE')))));
    $config->init(APP_ROOT . DIRECTORY_SEPARATOR . 'config');
    define('APP_LANG', session('lang', cookies('lang', config('app.default_lang', env('APP_LANG', $defaultLang)))));
    Request::getInstance();
    
    try {
        if (!str_contains(mb_strtolower($env->get('APP_ENV', 'dev')), 'prod')) {
            ini_set('display_errors', 1);
            ini_set('display_startup_errors', 1);
            error_reporting(E_ALL);
        }
        
        Router::init();
        
        if (APP === 'WEB') {
            session_start();
            
            $data = Router::run();
        } elseif (APP === 'CLI') {
            echo TerminalColor::blue('Start CLI-Mode: ' . date('Y-m-d H:i:s')) . PHP_EOL;
        } else {
            throw new Exception ("Application not configured");
        }
    } catch (Throwable $t) {
        $controller = Router::checkController(config('app.error_controller', ErrorController::class));
        $action = Router::checkAction($controller, errorAction(500, 'page500'));
        $data = $controller->{$action}($t);
    }
    
    if (APP === 'WEB') {
        $charset = mb_internal_encoding();
        $contentType = headers('Content-Type') ?? 'text/html';
        $lang = session('lang', cookies('lang', config('app.default_lang', env('APP_LANG', $defaultLang))));
        
        header('Content-type: ' . $contentType . '; charset=' . $charset);
        header('Accept: ' . $contentType);
        header('Content-Length: ' . strlen($data));
        header('Accept-Language: ' . $lang);
        header('Server: CloudCastle ' . APP . ' ' . mb_strtoupper(env('APP_ENV')) . ' Server');
        header('Allow: DELETE, GET, PATCH, POST, PUT, OPTIONS, VIEW');
        header('Server-Timing: ' . (microtime(true) - START_TIME));
        header('Request-Method: ' . (Request::getInstance()->{'_method'} ? $_SERVER['REQUEST_METHOD'] : 'GET'));
    }
    
    echo $data;
} catch (Throwable $e) {
    var_dump($e);
}

