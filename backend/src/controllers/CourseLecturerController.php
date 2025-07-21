<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database\Database;

class CourseLecturerController
{
  
    public function index(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT
                course_lecturers.id,
                course_lecturers.role,
                course_lecturers.assigned_at,
                users.id as lecturer_id,
                users.full_name as lecturer_name,
                courses.id as course_id,
                courses.course_code,
                courses.course_name
            FROM
                course_lecturers
            JOIN
                users
            ON
                users.id = course_lecturers.lecturer_id
            JOIN
                courses
            ON
                courses.id = course_lecturers.course_id
            ORDER BY
                course_lecturers.assigned_at DESC
        ");
        $stmt->execute();
        $records = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($records));
        return $response->withHeader('Content-Type', 'application/json');
    }

    
public function assign(Request $request, Response $response): Response
{
    $data = $request->getParsedBody();

    // Log incoming data for debugging
    error_log('Assign data: ' . json_encode($data));

    // Validate input
    if (empty($data['lecturer_id']) || empty($data['course_id']) || empty($data['role'])) {
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Missing required fields: lecturer_id, course_id, or role'
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            INSERT INTO course_lecturers (lecturer_id, course_id, role, assigned_at)
            VALUES (:lecturer_id, :course_id, :role, :assigned_at)
        ");
        $stmt->execute([
            ':lecturer_id' => $data['lecturer_id'],
            ':course_id' => $data['course_id'],
            ':role' => $data['role'],
            ':assigned_at' => date('Y-m-d H:i:s'),
        ]);

        $response->getBody()->write(json_encode(['status' => 'assigned']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (\Exception $e) {
        // Log the error
        error_log('Failed to assign lecturer: ' . $e->getMessage());

        // Return detailed error
        $response->getBody()->write(json_encode([
            'status' => 'error',
            'message' => 'Failed to assign lecturer: ' . $e->getMessage()
        ]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
}



    // Delete assignment
    public function delete(Request $request, Response $response, $args): Response
    {
        try {
            $courseId = $args['courseId'];
            $lecturerId = $args['lecturerId'];

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare("
                DELETE FROM course_lecturers
                WHERE course_id = :course_id AND lecturer_id = :lecturer_id
            ");
            $stmt->execute([
                ':course_id' => $courseId,
                ':lecturer_id' => $lecturerId,
            ]);

        $response->getBody()->write(json_encode(['status' => 'deleted']));
        return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'status' => 'error',
                'message' => 'Failed to delete assignment: ' . $e->getMessage()
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }

    // Get lecturers for a specific course
    public function get(Request $request, Response $response, $args): Response
    {
        $courseId = $args['id'];
        
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("
            SELECT
                course_lecturers.id,
                users.id as lecturer_id,
                users.full_name as lecturer_name,
                course_lecturers.role,
                course_lecturers.assigned_at
            FROM
                course_lecturers
            JOIN
                users
            ON
                users.id = course_lecturers.lecturer_id
            WHERE
                course_lecturers.course_id = :course_id
        ");
        $stmt->execute([':course_id' => $courseId]);
        $records = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($records));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
