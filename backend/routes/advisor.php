<?php

use App\Controllers\AdvisorController;

return function ($group) {
    $authMiddleware = \App\Middleware\JWTAuthMiddleware::class;

    $group->get('/advisor/{advisorId}/advisees', [AdvisorController::class, 'getAdvisees'])->add($authMiddleware);
    $group->get('/advisor/{advisorId}/advisee/{studentId}/breakdown', [AdvisorController::class, 'getAdviseeBreakdown'])->add($authMiddleware);
    $group->get('/advisor/{advisorId}/at-risk-students', [AdvisorController::class, 'getAtRiskStudents'])->add($authMiddleware);
    $group->post('/advisor/{advisorId}/advisee/{studentId}/notes', [AdvisorController::class, 'addAdviseNote'])->add($authMiddleware);
    $group->get('/advisor/{advisorId}/advisee/{studentId}/notes', [AdvisorController::class, 'getAdviseNotes'])->add($authMiddleware);
    $group->delete('/advisor/{advisorId}/advisee/{studentId}/notes/{noteId}', [AdvisorController::class, 'deleteAdviseNote'])->add($authMiddleware);
    $group->get('/advisor/{advisorId}/advisee/{studentId}/report', [AdvisorController::class, 'exportConsultationReport'])->add($authMiddleware);
}; 