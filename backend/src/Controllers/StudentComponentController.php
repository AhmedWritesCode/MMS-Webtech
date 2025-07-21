<?php
namespace App\Controllers;

// Import necessary interfaces for HTTP request and response handling
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
// Import the custom Database class for database connection
use App\Database\Database;

/**
 * Controller class for handling student and assessment component related endpoints.
 */
class StudentComponentController
{
    /**
     * Handles GET requests to /students-components.
     * Returns a list of students and assessment components.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @return Response         The HTTP response with JSON data.
     */
    public function index(Request $request, Response $response): Response
    {
        // Get a PDO database connection instance from the Database singleton
        $db = Database::getInstance()->getConnection();

        // Prepare a SQL statement to select student IDs and full names from the users table
        $stmt = $db->prepare("SELECT id, full_name FROM users WHERE user_type = 'student'");
        // Execute the SQL statement
        $stmt->execute();
        // Fetch all resulting student records as an array
        $students = $stmt->fetchAll();

        // Prepare a SQL statement to select assessment component details and their associated course info
        $stmt2 = $db->prepare(
            "SELECT assessment_components.id, assessment_components.component_name, courses.course_code, courses.course_name 
             FROM assessment_components 
             JOIN courses ON courses.id = assessment_components.course_id"
        );
        // Execute the SQL statement
        $stmt2->execute();
        // Fetch all resulting assessment component records as an array
        $components = $stmt2->fetchAll();

        // Combine the students and components data into a single associative array
        $data = [
            'students' => $students,
            'components' => $components
        ];

        // Encode the data array as JSON and write it to the response body
        $response->getBody()->write(json_encode($data));
        // Return the response with a 200 OK status and JSON content type header
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}