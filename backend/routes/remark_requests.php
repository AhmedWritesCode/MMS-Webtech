<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\RemarkRequestController;

return function (RouteCollectorProxy $group) {
    $controller = new RemarkRequestController();
    $group->group('/remark-requests', function (RouteCollectorProxy $subgroup) use ($controller) {
        $subgroup->post('/submit', [$controller, 'submitRequest']);
        $subgroup->get('/student/{student_id}', [$controller, 'getStudentRequests']);
        $subgroup->get('/course/{course_id}', [$controller, 'getRequestsForLecturer']);
        $subgroup->post('/review/{id}', [$controller, 'reviewRequest']);
    });
};
