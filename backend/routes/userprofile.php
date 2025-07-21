<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AuthController;
use App\Controllers\UserController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (RouteCollectorProxy $group) {
    $authMiddleware = \App\Middleware\JWTAuthMiddleware::class;

    $group->get('/user/profile', [UserController::class, 'getProfile'])->add($authMiddleware);
    $group->get('/test-protected', function (Request $request, Response $response) {
        $userId = $request->getAttribute('user_id');
        $userRole = $request->getAttribute('user_role');
        $response->getBody()->write(json_encode([
            'message' => 'This is a protected endpoint',
            'user_id' => $userId,
            'user_role' => $userRole,
            'timestamp' => date('Y-m-d H:i:s')
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    })->add($authMiddleware);
    $group->put('/auth/change-password', [AuthController::class, 'changePassword'])->add($authMiddleware);
}; 