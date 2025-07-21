<?php

use Slim\Routing\RouteCollectorProxy;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (RouteCollectorProxy $group) {
    $group->any('/{routes:.+}', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode([
            'error' => true,
            'message' => 'API endpoint not found',
            'requested_path' => $request->getUri()->getPath(),
            'method' => $request->getMethod(),
            'available_endpoints' => [
                'health' => 'GET /api/health',
                'test' => 'GET /api/test',
                'student_performance' => '/api/student/{id}/...',
                'advisor_module' => '/api/advisor/{id}/...'
            ]
        ]));
        return $response->withStatus(404)->withHeader('Content-Type', 'application/json');
    });
}; 