<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AuthController;

return function (RouteCollectorProxy $group) {
    $group->group('/auth', function (RouteCollectorProxy $subgroup) {
        $subgroup->post('/login', [AuthController::class, 'login']);
        $subgroup->post('/logout', [AuthController::class, 'logout']);
        $subgroup->post('/refresh', [AuthController::class, 'refreshToken']);
    });
}; 