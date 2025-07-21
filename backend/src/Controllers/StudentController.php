<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database\Database;

/**
 * Controller class for handling student-related operations.
 */
class StudentController
{
    /**
     * Retrieve a list of all students.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @return Response         The response containing the list of students in JSON format.
     */
    public function index(Request $request, Response $response): Response
    {
        // Get a database connection instance
        $db = Database::getInstance()->getConnection();

        // Prepare SQL statement to select all users with user_type 'student'
        $stmt = $db->prepare("SELECT * FROM users WHERE user_type = 'student'");

        // Execute the SQL statement
        $stmt->execute();

        // Fetch all student records as an array
        $students = $stmt->fetchAll();

        // Write the students array as JSON to the response body
        $response->getBody()->write(json_encode($students));

        // Return the response with the appropriate Content-Type header
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Store a new student in the database.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @return Response         The response containing the new student's ID in JSON format.
     */
    public function store(Request $request, Response $response): Response
    {
        // Get a database connection instance
        $db = Database::getInstance()->getConnection();

        // Parse the request body to get input data
        $data = $request->getParsedBody();

        // Use provided password or default to 'default123'
        $password = $data['password'] ?? 'default123';

        // Hash the password for secure storage
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Prepare SQL statement to insert a new student user
        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, user_type, is_active) VALUES (?, ?, ?, ?, 'student', ?)");

        // Execute the SQL statement with provided data
        $stmt->execute([
            $data['username'],
            $data['email'],
            $password_hash,
            $data['full_name'] ?? '',
            $data['is_active'] ?? true
        ]);

        // Get the ID of the newly inserted student
        $id = $db->lastInsertId();

        // Write the new student's ID as JSON to the response body
        $response->getBody()->write(json_encode(['id' => $id]));

        // Return the response with the appropriate Content-Type header
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Update an existing student's information.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param array $args       Route arguments, including the student's ID.
     * @return Response         The response indicating update status in JSON format.
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        // Get a database connection instance
        $db = Database::getInstance()->getConnection();

        // Parse the request body to get input data
        $data = $request->getParsedBody();

        // Prepare data for updating the student
        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'full_name' => $data['full_name'] ?? '',
            'is_active' => $data['is_active'] ?? true,
        ];

        // If a new password is provided, hash it and include in update data
        if (!empty($data['password'])) {
            $updateData['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        // Prepare SQL statement to update the student user
        $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, is_active = ? WHERE id = ? AND user_type = 'student'");

        // Execute the SQL statement with provided data and student ID
        $stmt->execute([
            $updateData['username'],
            $updateData['email'],
            $updateData['full_name'],
            $updateData['is_active'],
            $args['id']
        ]);

        // Check if any rows were updated
        if ($stmt->rowCount() > 0) {
            // Write 'updated' status as JSON to the response body
            $response->getBody()->write(json_encode(['status' => 'updated']));
        } else {
            // Write 'not_found' status as JSON to the response body
            $response->getBody()->write(json_encode(['status' => 'not_found']));
        }

        // Return the response with the appropriate Content-Type header
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Delete a student from the database.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param array $args       Route arguments, including the student's ID.
     * @return Response         The response indicating deletion status in JSON format.
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        // Get a database connection instance
        $db = Database::getInstance()->getConnection();

        // Prepare SQL statement to delete the student user by ID
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND user_type = 'student'");

        // Execute the SQL statement with the student ID
        $stmt->execute([$args['id']]);

        // Check if any rows were deleted
        if ($stmt->rowCount() > 0) {
            // Write 'deleted' status as JSON to the response body
            $response->getBody()->write(json_encode(['status' => 'deleted']));
        } else {
            // Write 'not_found' status as JSON to the response body
            $response->getBody()->write(json_encode(['status' => 'not_found']));
        }

        // Return the response with the appropriate Content-Type header
        return $response->withHeader('Content-Type', 'application/json');
    }
}

