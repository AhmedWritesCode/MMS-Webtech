<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\StudentController;

return function (RouteCollectorProxy $group) {
    $group->get('/students', [StudentController::class, 'index']);
    $group->post('/students', [StudentController::class, 'store']);
    $group->put('/students/{id}', [StudentController::class, 'update']);
    $group->delete('/students/{id}', [StudentController::class, 'destroy']);
};
