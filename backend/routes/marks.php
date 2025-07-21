<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\MarkController;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return function (RouteCollectorProxy $group) {
    $group->post('/marks', [MarkController::class, 'store'])->setName('marks.store');
    $group->get('/marks/test', function (Request $request, Response $response) {
        $response->getBody()->write(json_encode(['status' => 'Marks route is registered']));
        return $response->withHeader('Content-Type', 'application/json');
    });
};