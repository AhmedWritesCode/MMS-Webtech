<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database\Database;

class RemarkRequestController
{
    public function submitRequest(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        error_log('Received data: ' . json_encode($data));

        // Validate required fields
        $required = ['student_id', 'assessment_component_id', 'original_marks', 'justification'];
        foreach ($required as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => "Missing required field: $field"
                ]));
                return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
            }
        }

        try {
            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("INSERT INTO remark_requests (student_id, assessment_component_id, original_marks, justification, status, requested_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $data['student_id'],
                $data['assessment_component_id'],
                $data['original_marks'],
                $data['justification'],
                'pending',
                date('Y-m-d H:i:s'), // ✅ native PHP timestamp
            ]);

            $id = $db->lastInsertId();
            $response->getBody()->write(json_encode(['success' => true, 'id' => $id, 'message' => 'Remark request submitted']));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to submit remark request',
                'error' => $e->getMessage()
            ]));
            return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
        }
    }

    public function getStudentRequests(Request $request, Response $response, array $args): Response
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM remark_requests WHERE student_id = ?");
        $stmt->execute([$args['student_id']]);
        $requests = $stmt->fetchAll();

        $response->getBody()->write(json_encode($requests));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getRequestsForLecturer(Request $request, Response $response, array $args): Response
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT rr.*, u.full_name, ac.component_name
            FROM remark_requests as rr
            JOIN assessment_components as ac ON rr.assessment_component_id = ac.id
            JOIN users as u ON rr.student_id = u.id
            WHERE ac.course_id = ?
        ");
        $stmt->execute([$args['course_id']]);
        $requests = $stmt->fetchAll();

        $response->getBody()->write(json_encode($requests));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function reviewRequest(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            UPDATE remark_requests
            SET status = ?, reviewed_by = ?, review_comments = ?, new_marks = ?, reviewed_at = ?
            WHERE id = ?
        ");
        $stmt->execute([
            $data['status'],
            $data['reviewed_by'],
            $data['review_comments'],
            $data['new_marks'],
            date('Y-m-d H:i:s'), // ✅ native PHP timestamp
            $args['id'],
        ]);

        $response->getBody()->write(json_encode(['message' => 'Remark request reviewed']));
        return $response->withHeader('Content-Type', 'application/json');
    }
}


