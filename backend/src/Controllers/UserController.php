<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database\Database;

/**
 * UserController handles CRUD operations and profile retrieval for users.
 */
class UserController
{
    /**
     * List users, optionally filtered by user type.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @return Response         The response with user data in JSON.
     */
    public function index(Request $request, Response $response): Response
    {
        // Get database connection
        $db = Database::getInstance()->getConnection();

        // Get query parameters from the request
        $params = $request->getQueryParams();

        // Base SQL query to select all users
        $sql = "SELECT * FROM users";
        $queryParams = [];

        // If 'type' parameter is provided, filter by user_type
        if (!empty($params['type'])) {
            $sql .= " WHERE user_type = ?";
            $queryParams[] = $params['type'];
        }

        // Prepare and execute the SQL statement
        $stmt = $db->prepare($sql);
        $stmt->execute($queryParams);

        // Fetch all users as an array
        $users = $stmt->fetchAll();

        // Write the users array as JSON to the response body
        $response->getBody()->write(json_encode($users));

        // Return the response with JSON content type
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Store a new user in the database.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @return Response         The response with the new user's ID in JSON.
     */
    public function store(Request $request, Response $response): Response
    {
        // Parse the request body for user data
        $data = $request->getParsedBody();

        // Get database connection
        $db = Database::getInstance()->getConnection();

        // SQL query to insert a new user
        $sql = "INSERT INTO users (username, email, password_hash, full_name, user_type, is_active) VALUES (?, ?, ?, ?, ?, ?)";

        // Prepare the SQL statement
        $stmt = $db->prepare($sql);

        // Execute the statement with provided data, using defaults if necessary
        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password_hash'] ?? 'default123', // Default password if not provided
            $data['full_name'] ?? '',               // Default empty name if not provided
            $data['user_type'],
            $data['is_active'] ?? true,             // Default active if not provided
        ]);

        // Get the ID of the newly inserted user
        $id = $db->lastInsertId();

        // Write the new user ID as JSON to the response body
        $response->getBody()->write(json_encode(['id' => $id]));

        // Return the response with JSON content type
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Update an existing user in the database.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param array $args       Route arguments, including user ID.
     * @return Response         The response indicating update status.
     */
    public function update(Request $request, Response $response, array $args): Response
    {
        // Parse the request body for updated user data
        $data = $request->getParsedBody();

        // Get database connection
        $db = Database::getInstance()->getConnection();

        // SQL query to update user by ID
        $sql = "UPDATE users SET username = ?, email = ?, password_hash = ?, full_name = ?, user_type = ?, is_active = ? WHERE id = ?";

        // Prepare the SQL statement
        $stmt = $db->prepare($sql);

        // Execute the statement with provided data and user ID
        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password_hash'] ?? 'default123', // Default password if not provided
            $data['full_name'] ?? '',               // Default empty name if not provided
            $data['user_type'],
            $data['is_active'] ?? true,             // Default active if not provided
            $args['id'],
        ]);

        // Write update status as JSON to the response body
        $response->getBody()->write(json_encode(['status' => 'updated']));

        // Return the response with JSON content type
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Delete a user from the database.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @param array $args       Route arguments, including user ID.
     * @return Response         The response indicating delete status.
     */
    public function destroy(Request $request, Response $response, array $args): Response
    {
        // Get database connection
        $db = Database::getInstance()->getConnection();

        // SQL query to delete user by ID
        $sql = "DELETE FROM users WHERE id = ?";

        // Prepare the SQL statement
        $stmt = $db->prepare($sql);

        // Execute the statement with user ID
        $stmt->execute([$args['id']]);

        // Write delete status as JSON to the response body
        $response->getBody()->write(json_encode(['status' => 'deleted']));

        // Return the response with JSON content type
        return $response->withHeader('Content-Type', 'application/json');
    }

    /**
     * Retrieve the profile of the authenticated user.
     *
     * @param Request $request  The HTTP request object.
     * @param Response $response The HTTP response object.
     * @return Response         The response with user profile data or error message.
     */
    public function getProfile(Request $request, Response $response): Response
    {
        try {
            // Get the authenticated user's ID from request attributes
            $userId = $request->getAttribute('user_id');

            // If user ID is not present, return 401 Unauthorized
            if (!$userId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not authenticated',
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

            // Get database connection
            $db = Database::getInstance()->getConnection();

            // Prepare SQL to fetch user by ID
            $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$userId]);

            // Fetch the user record
            $user = $stmt->fetch();

            // If user not found, return 404 Not Found
            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not found',
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            // Remove password hash from the user data before returning
            unset($user['password_hash']);

            // Write user data as JSON to the response body
            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
            ]));

            // Return the response with JSON content type
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            // Handle any exceptions and return 500 Internal Server Error
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to fetch user profile: ' . $e->getMessage(),
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}

