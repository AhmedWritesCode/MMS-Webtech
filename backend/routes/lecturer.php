<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\LecturerController;

return function (RouteCollectorProxy $group) {
    $authMiddleware = \App\Middleware\JWTAuthMiddleware::class;

    $group->get('/lecturer/{lecturerId}/dashboard', [LecturerController::class, 'getDashboard'])->add($authMiddleware);
    $group->get('/lecturer/{lecturerId}/course/{courseId}/students', [LecturerController::class, 'getCourseStudents'])->add($authMiddleware);
    $group->get('/lecturer/{lecturerId}/course/{courseId}/components', [LecturerController::class, 'getCourseComponents'])->add($authMiddleware);
    $group->post('/lecturer/{lecturerId}/course/{courseId}/components', [LecturerController::class, 'createUpdateComponent'])->add($authMiddleware);
    $group->put('/lecturer/{lecturerId}/course/{courseId}/student/{studentId}/marks', [LecturerController::class, 'updateStudentMarks'])->add($authMiddleware);
    $group->post('/lecturer/{lecturerId}/course/{courseId}/marks/bulk-upload', [LecturerController::class, 'bulkUploadMarks'])->add($authMiddleware);
    $group->get('/lecturer/{lecturerId}/course/{courseId}/marks/export', [LecturerController::class, 'exportMarks'])->add($authMiddleware);
    $group->get('/lecturer/{lecturerId}/course/{courseId}/analytics', [LecturerController::class, 'getCourseAnalytics'])->add($authMiddleware);
    $group->post('/lecturer/{lecturerId}/course/{courseId}/notifications', [LecturerController::class, 'sendNotification'])->add($authMiddleware);
    $group->get('/lecturer/{lecturerId}/courses', [LecturerController::class, 'getCourses'])->add($authMiddleware);
}; 