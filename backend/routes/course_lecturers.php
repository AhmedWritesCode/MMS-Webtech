<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\CourseLecturerController;

return function (RouteCollectorProxy $group) {
    $ctrl = new CourseLecturerController();
    $group->get('/course-lecturers', [$ctrl, 'index']);
    $group->post('/course-lecturers', [$ctrl, 'assign']);
    $group->get('/course-lecturers/{id}', [$ctrl, 'get']);
    $group->delete('/course-lecturers/{courseId}/{lecturerId}', [$ctrl, 'delete']);
};
