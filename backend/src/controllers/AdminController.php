<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database\Database;

class AdminController
{
    // Fetch mark updates (student_marks table)
    public function getMarkUpdates(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $perPage = isset($params['per_page']) ? max(1, min(100, (int)$params['per_page'])) : 10;
        $offset = ($page - 1) * $perPage;

        $studentId = $params['student_id'] ?? null;
        $courseId = $params['course_id'] ?? null;
        $gradedBy = $params['graded_by'] ?? null;
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;

        $db = Database::getInstance()->getConnection();

        $query = $db->prepare("
            SELECT
                student_marks.id,
                student_marks.student_id,
                students.full_name as student_name,
                student_marks.assessment_component_id,
                assessment_components.component_name,
                courses.course_code,
                student_marks.marks_obtained,
                student_marks.remarks,
                student_marks.graded_by,
                graders.full_name as grader_name,
                student_marks.graded_at,
                student_marks.is_final,
                student_marks.created_at
            FROM
                student_marks
            LEFT JOIN
                users as students ON student_marks.student_id = students.id
            LEFT JOIN
                assessment_components ON student_marks.assessment_component_id = assessment_components.id
            LEFT JOIN
                courses ON assessment_components.course_id = courses.id
            LEFT JOIN
                users as graders ON student_marks.graded_by = graders.id
        ");

        if ($studentId) {
            $query->execute(['student_id' => $studentId]);
        } elseif ($courseId) {
            $query->execute(['course_id' => $courseId]);
        } elseif ($gradedBy) {
            $query->execute(['graded_by' => $gradedBy]);
        } elseif ($startDate || $endDate) {
            $query->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        }

        $total = $query->rowCount();
        $logs = $query->fetchAll();

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'data' => $logs,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    // Fetch system logs (audit_log table)
    public function getSystemLogs(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $page = isset($params['page']) ? max(1, (int)$params['page']) : 1;
        $perPage = isset($params['per_page']) ? max(1, min(100, (int)$params['per_page'])) : 10;
        $offset = ($page - 1) * $perPage;

        $userId = $params['user_id'] ?? null;
        $action = $params['action'] ?? null;
        $startDate = $params['start_date'] ?? null;
        $endDate = $params['end_date'] ?? null;

        $db = Database::getInstance()->getConnection();

        $query = $db->prepare("
            SELECT
                audit_log.id,
                audit_log.user_id,
                users.full_name as user_name,
                audit_log.action,
                audit_log.table_name,
                audit_log.record_id,
                audit_log.old_values,
                audit_log.new_values,
                audit_log.ip_address,
                audit_log.user_agent,
                audit_log.created_at
            FROM
                audit_log
            LEFT JOIN
                users ON audit_log.user_id = users.id
        ");

        if ($userId) {
            $query->execute(['user_id' => $userId]);
        } elseif ($action) {
            $query->execute(['action' => $action]);
        } elseif ($startDate || $endDate) {
            $query->execute(['start_date' => $startDate, 'end_date' => $endDate]);
        }

        $total = $query->rowCount();
        $logs = $query->fetchAll();

        $response->getBody()->write(json_encode([
            'status' => 'success',
            'data' => $logs,
            'pagination' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => ceil($total / $perPage)
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
