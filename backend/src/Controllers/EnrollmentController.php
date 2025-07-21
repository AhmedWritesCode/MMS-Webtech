<?php
namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface  as Request;
use App\Database\Database;

class EnrollmentController
{
    /**
     * GET /course_enrollments
     * GET /course_enrollments/course/{course_id}
     */
    public function index(Request $request, Response $response, array $args): Response
    {
        $db = Database::getInstance()->getConnection();
        $sql = "SELECT ce.*, users.full_name as student_name, courses.course_code, courses.course_name FROM course_enrollments ce
                JOIN users ON users.id = ce.student_id
                JOIN courses ON courses.id = ce.course_id";
        $params = [];
        if (isset($args['course_id'])) {
            $sql .= " WHERE ce.course_id = ?";
            $params[] = $args['course_id'];
        }
        $sql .= " ORDER BY ce.enrollment_date DESC";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $records = $stmt->fetchAll();
        $response->getBody()->write(json_encode($records));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * POST /course_enrollments
     * body: { student_id, course_id, status? }
     */
    public function store(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        $data = $request->getParsedBody();
        $sql = "INSERT INTO course_enrollments (student_id, course_id, status, enrollment_date) VALUES (?, ?, ?, NOW())";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['student_id'],
            $data['course_id'],
            $data['status'] ?? 'active'
        ]);
        $id = $db->lastInsertId();
        $response->getBody()->write(json_encode(['id' => $id]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * DELETE /course_enrollments/{id}
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        if (! $id) {
            $response->getBody()->write(json_encode(['error' => 'Enrollment ID required']));
            return $response
                ->withStatus(400)
                ->withHeader('Content-Type', 'application/json');
        }

        $db = Database::getInstance()->getConnection();
        $sql = "DELETE FROM course_enrollments WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$id]);

        $response->getBody()->write(json_encode(['status' => 'deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }
} 