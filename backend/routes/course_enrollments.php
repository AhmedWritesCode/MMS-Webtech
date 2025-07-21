<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\EnrollmentController;

return function (RouteCollectorProxy $group) {
    $group->get('/course_enrollments', [EnrollmentController::class, 'index']);
    $group->get('/course_enrollments/course/{course_id}', [EnrollmentController::class, 'index']);
    $group->post('/course_enrollments', [EnrollmentController::class, 'store']);
    $group->delete('/course_enrollments/{id}', [EnrollmentController::class, 'delete']);
};
