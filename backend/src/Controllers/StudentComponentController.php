<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Database\Database;

class StudentComponentController
{
    /**
     * GET /students-components
     * Returns list of students and assessment components
     */
    public function index(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id, full_name FROM users WHERE user_type = 'student'");
        $stmt->execute();
        $students = $stmt->fetchAll();

        $stmt2 = $db->prepare("SELECT assessment_components.id, assessment_components.component_name, courses.course_code, courses.course_name FROM assessment_components JOIN courses ON courses.id = assessment_components.course_id");
        $stmt2->execute();
        $components = $stmt2->fetchAll();

        $data = [
            'students' => $students,
            'components' => $components
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}