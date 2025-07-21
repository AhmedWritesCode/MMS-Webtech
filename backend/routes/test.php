<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (RouteCollectorProxy $group) {
    $group->get('/test', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode([
            'message' => 'API is working!',
            'timestamp' => date('Y-m-d H:i:s'),
            'method' => $request->getMethod(),
            'cors_headers' => [
                'Access-Control-Allow-Origin' => $_ENV['FRONTEND_URL'] ?? 'http://localhost:8081'
            ]
        ]));
        return $response->withHeader('Content-Type', 'application/json');
    });
}; 