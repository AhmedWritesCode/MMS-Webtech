<?php

use App\Controllers\StudentPerformanceController;

return function ($group) {
    $authMiddleware = \App\Middleware\JWTAuthMiddleware::class;

    $group->get('/student/{studentId}/course/{courseId}/marks', [StudentPerformanceController::class, 'getStudentCourseMarks'])->add($authMiddleware);
    $group->get('/student/{studentId}/marks/breakdown', [StudentPerformanceController::class, 'getStudentFullBreakdown'])->add($authMiddleware);
    $group->get('/course/{courseId}/marks/comparison', [StudentPerformanceController::class, 'getCourseComparison'])->add($authMiddleware);
    $group->get('/student/{studentId}/course/{courseId}/rank', [StudentPerformanceController::class, 'getStudentRank'])->add($authMiddleware);
    $group->get('/student/{studentId}/performance/trends', [StudentPerformanceController::class, 'getPerformanceTrends'])->add($authMiddleware);
    $group->post('/student/{studentId}/what-if-simulation', [StudentPerformanceController::class, 'whatIfSimulation'])->add($authMiddleware);
    $group->get('/course/{courseId}/averages', [StudentPerformanceController::class, 'getClassAverages'])->add($authMiddleware);
}; 