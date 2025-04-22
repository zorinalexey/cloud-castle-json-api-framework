<?php
/**
 * @var Config $config
 * @var Env $env
 * @var Route|null $route
 * @var RequestInterface|null $request
 */

use CloudCastle\Core\Config;
use CloudCastle\Core\Env;
use CloudCastle\Core\Request\RequestInterface;
use CloudCastle\Core\Router\Route;

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>404 - Page not found !!!</title>
</head>
<body>
<main>
    <div> <?php echo $layout; ?></div>
    <h1>404 - Page not found</h1>
    <p>
        <?php echo trans('errors.404', [':url' => $request->request_uri]); ?>
    </p>
</main>
</body>
</html>