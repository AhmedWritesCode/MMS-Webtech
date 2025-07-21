<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\NotificationController;

return function (RouteCollectorProxy $group) {
    $group->get('/notifications', [NotificationController::class, 'index']);
    $group->delete('/notifications/{id}', [NotificationController::class, 'delete']);
};