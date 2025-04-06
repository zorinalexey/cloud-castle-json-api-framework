<?php

use CloudCastle\Core\Controllers\ErrorController;

return [
    'error_controller' => ErrorController::class,
    'error_action' => 'page404',
    'fatal_error_controller' => ErrorController::class,
    'fatal_error_page' => 'page500',
];