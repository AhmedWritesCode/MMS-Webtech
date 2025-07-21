<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Database\Database;

class MarkController
{
    /**
     * POST /marks
     * body: { student_id, assessment_component_id, marks_obtained, remarks?, is_final? }
     */
    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();

        // Log request data for debugging
        error_log('POST /marks received: ' . json_encode($data));

        // Validate required fields
        $studentId = $data['student_id'] ?? null;
        $assessmentComponentId = $data['assessment_component_id'] ?? null;
        $marksObtained = $data['marks_obtained'] ?? null;
        $remarks = $data['remarks'] ?? null;
        $isFinal = $data['is_final'] ?? false;

        if (!$studentId || !$assessmentComponentId || $marksObtained === null) {
            $error = 'student_id, assessment_component_id, and marks_obtained are required';
            error_log('Validation error: ' . $error);
            $response->getBody()->write(json_encode(['error' => $error]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validate student (must exist and be a student)
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE id = :id AND user_type = 'student'");
        $stmt->execute(['id' => $studentId]);
        $student = $stmt->fetch();

        if (!$student) {
            $error = 'Invalid or non-student user';
            error_log('Validation error: ' . $error);
            $response->getBody()->write(json_encode(['error' => $error]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Validate assessment component
        $stmt = $db->prepare("SELECT * FROM assessment_components WHERE id = :id");
        $stmt->execute(['id' => $assessmentComponentId]);
        $component = $stmt->fetch();

        if (!$component) {
            $error = 'Invalid assessment component';
            error_log('Validation error: ' . $error);
            $response->getBody()->write(json_encode(['error' => $error]));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        // Check if mark exists
        $stmt = $db->prepare("SELECT * FROM student_marks WHERE student_id = :student_id AND assessment_component_id = :assessment_component_id");
        $stmt->execute(['student_id' => $studentId, 'assessment_component_id' => $assessmentComponentId]);
        $existingMark = $stmt->fetch();

        $now = date('Y-m-d H:i:s');

        if ($existingMark) {
            // Update existing mark
            $stmt = $db->prepare("UPDATE student_marks SET marks_obtained = :marks_obtained, remarks = :remarks, graded_at = :graded_at, is_final = :is_final, updated_at = :updated_at WHERE id = :id");
            $stmt->execute([
                'marks_obtained' => $marksObtained,
                'remarks' => $remarks,
                'graded_at' => $now,
                'is_final' => $isFinal,
                'updated_at' => $now,
                'id' => $existingMark['id'],
            ]);
        } else {
            // Insert new mark
            $stmt = $db->prepare("INSERT INTO student_marks (student_id, assessment_component_id, marks_obtained, remarks, graded_at, is_final, created_at, updated_at) VALUES (:student_id, :assessment_component_id, :marks_obtained, :remarks, :graded_at, :is_final, :created_at, :updated_at)");
            $stmt->execute([
                'student_id' => $studentId,
                'assessment_component_id' => $assessmentComponentId,
                'marks_obtained' => $marksObtained,
                'remarks' => $remarks,
                'graded_at' => $now,
                'is_final' => $isFinal,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // Create notification
        $stmt = $db->prepare("SELECT * FROM courses WHERE id = :id");
        $stmt->execute(['id' => $component['course_id']]);
        $course = $stmt->fetch();

        $stmt = $db->prepare("INSERT INTO notifications (user_id, title, message, type, is_read, created_at) VALUES (:user_id, :title, :message, :type, :is_read, :created_at)");
        $stmt->execute([
            'user_id' => $studentId,
            'title' => "Mark Updated for {$course['course_code']}",
            'message' => "Your marks for {$component['component_name']} in {$course['course_name']} have been updated to {$marksObtained}.",
            'type' => 'mark_update',
            'is_read' => false,
            'created_at' => $now,
        ]);

        $response->getBody()->write(json_encode(['status' => 'marks_saved']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}