<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\UserController;

return function (RouteCollectorProxy $group) {
    $group->get('/users', [UserController::class, 'index']);
    $group->post('/users', [UserController::class, 'store']);
    $group->put('/users/{id}', [UserController::class, 'update']);
    $group->delete('/users/{id}', [UserController::class, 'destroy']);
};
