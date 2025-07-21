<?php
use Slim\Routing\RouteCollectorProxy;
use App\Controllers\StudentComponentController;

return function (RouteCollectorProxy $group) {
    $group->get('/students-components', [StudentComponentController::class, 'index'])->setName('students-components.index');
};