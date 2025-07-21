<?php

declare(strict_types=1);

use App\Application\Middleware\SessionMiddleware;
use App\Middleware\CorsMiddleware;
use Slim\App;

return function (App $app) {
    // Add CORS middleware first
    $app->add(new CorsMiddleware());
    
    // Then other middleware
    $app->add(SessionMiddleware::class);
};
