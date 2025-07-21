<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database\Database;

class UserController
{
    public function index(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        $params = $request->getQueryParams();
        $sql = "SELECT * FROM users";
        $queryParams = [];
        if (!empty($params['type'])) {
            $sql .= " WHERE user_type = ?";
            $queryParams[] = $params['type'];
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($queryParams);
        $users = $stmt->fetchAll();
        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $data = $request->getParsedBody();
        $db = Database::getInstance()->getConnection();
        $sql = "INSERT INTO users (username, email, password_hash, full_name, user_type, is_active) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password_hash'] ?? 'default123',
            $data['full_name'] ?? '',
            $data['user_type'],
            $data['is_active'] ?? true,
        ]);
        $id = $db->lastInsertId();
        $response->getBody()->write(json_encode(['id' => $id]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $data = $request->getParsedBody();
        $db = Database::getInstance()->getConnection();
        $sql = "UPDATE users SET username = ?, email = ?, password_hash = ?, full_name = ?, user_type = ?, is_active = ? WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([
            $data['username'],
            $data['email'],
            $data['password_hash'] ?? 'default123',
            $data['full_name'] ?? '',
            $data['user_type'],
            $data['is_active'] ?? true,
            $args['id'],
        ]);
        $response->getBody()->write(json_encode(['status' => 'updated']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $db = Database::getInstance()->getConnection();
        $sql = "DELETE FROM users WHERE id = ?";
        $stmt = $db->prepare($sql);
        $stmt->execute([$args['id']]);
        $response->getBody()->write(json_encode(['status' => 'deleted']));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getProfile(Request $request, Response $response): Response
    {
        try {
            $userId = $request->getAttribute('user_id');
            if (!$userId) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not authenticated',
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(401);
            }

            $db = Database::getInstance()->getConnection();
            $stmt = $db->prepare('SELECT * FROM users WHERE id = ?');
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
            if (!$user) {
                $response->getBody()->write(json_encode([
                    'success' => false,
                    'message' => 'User not found',
                ]));
                return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
            }

            unset($user['password_hash']);

            $response->getBody()->write(json_encode([
                'success' => true,
                'data' => $user,
            ]));
            return $response->withHeader('Content-Type', 'application/json');
        } catch (\Exception $e) {
            $response->getBody()->write(json_encode([
                'success' => false,
                'message' => 'Failed to fetch user profile: ' . $e->getMessage(),
            ]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
        }
    }
}
