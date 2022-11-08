<?php

declare(strict_types=1);

use App\Application\Middleware\AdminRoutesMiddleware;
use App\Application\Middleware\SessionMiddleware;
use App\Application\Middleware\SessionsMiddleware;
use RKA\Middleware\IpAddress;
use Slim\App;

return function (App $app) {
//    $app->add(SessionMiddleware::class);
    $app->add(AdminRoutesMiddleware::class);
    $app->add(SessionsMiddleware::class);
    $app->add(IpAddress::class);

};
