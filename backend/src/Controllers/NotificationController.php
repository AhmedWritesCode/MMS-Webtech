<?php
namespace App\Controllers;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use App\Database\Database;

class NotificationController
{
    /**
     * GET /notifications?user_id={id}
     */
    public function index(Request $request, Response $response): Response
    {
        $userId = $request->getQueryParams()['user_id'] ?? null;
        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'User ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC");
        $stmt->execute(['user_id' => $userId]);
        $notifications = $stmt->fetchAll();

        $response->getBody()->write(json_encode($notifications));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }

    /**
     * DELETE /notifications/{id}?user_id={id}
     */
    public function delete(Request $request, Response $response, array $args): Response
    {
        $id = $args['id'] ?? null;
        $userId = $request->getQueryParams()['user_id'] ?? null;

        if (!$userId) {
            $response->getBody()->write(json_encode(['error' => 'User ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (!$id) {
            $response->getBody()->write(json_encode(['error' => 'Notification ID required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM notifications WHERE id = :id AND user_id = :user_id");
        $stmt->execute(['id' => $id, 'user_id' => $userId]);
        $notification = $stmt->fetch();

        if (!$notification) {
            $response->getBody()->write(json_encode(['error' => 'Notification not found or unauthorized']));
            return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
        }

        $stmt = $db->prepare("DELETE FROM notifications WHERE id = :id");
        $stmt->execute(['id' => $id]);

        $response->getBody()->write(json_encode(['status' => 'deleted']));
        return $response->withStatus(200)->withHeader('Content-Type', 'application/json');
    }
}