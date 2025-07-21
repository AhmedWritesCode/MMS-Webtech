<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\CourseController;

return function (RouteCollectorProxy $group) {
    $controller = new CourseController();
    $group->get('/courses', [$controller, 'getAll']);
    $group->post('/courses', [$controller, 'create']);
    $group->put('/courses/{id}', [$controller, 'update']);
    $group->delete('/courses/{id}', [$controller, 'delete']);
};
