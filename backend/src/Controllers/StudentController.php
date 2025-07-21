<?php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use App\Database\Database;

class StudentController
{
    public function index(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM users WHERE user_type = 'student'");
        $stmt->execute();
        $students = $stmt->fetchAll();
        $response->getBody()->write(json_encode($students));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function store(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        $data = $request->getParsedBody();
        $password = $data['password'] ?? 'default123';
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $db->prepare("INSERT INTO users (username, email, password_hash, full_name, user_type, is_active) VALUES (?, ?, ?, ?, 'student', ?)");
        $stmt->execute([
            $data['username'],
            $data['email'],
            $password_hash,
            $data['full_name'] ?? '',
            $data['is_active'] ?? true
        ]);
        $id = $db->lastInsertId();
        $response->getBody()->write(json_encode(['id' => $id]));
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function update(Request $request, Response $response, array $args): Response
    {
        $db = Database::getInstance()->getConnection();
        $data = $request->getParsedBody();
        $updateData = [
            'username' => $data['username'],
            'email' => $data['email'],
            'full_name' => $data['full_name'] ?? '',
            'is_active' => $data['is_active'] ?? true,
        ];
        if (!empty($data['password'])) {
            $updateData['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }
        $stmt = $db->prepare("UPDATE users SET username = ?, email = ?, full_name = ?, is_active = ? WHERE id = ? AND user_type = 'student'");
        $stmt->execute([
            $updateData['username'],
            $updateData['email'],
            $updateData['full_name'],
            $updateData['is_active'],
            $args['id']
        ]);
        if ($stmt->rowCount() > 0) {
            $response->getBody()->write(json_encode(['status' => 'updated']));
        } else {
            $response->getBody()->write(json_encode(['status' => 'not_found']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }

    public function destroy(Request $request, Response $response, array $args): Response
    {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("DELETE FROM users WHERE id = ? AND user_type = 'student'");
        $stmt->execute([$args['id']]);
        if ($stmt->rowCount() > 0) {
            $response->getBody()->write(json_encode(['status' => 'deleted']));
        } else {
            $response->getBody()->write(json_encode(['status' => 'not_found']));
        }
        return $response->withHeader('Content-Type', 'application/json');
    }
}
