<?php

use Slim\Routing\RouteCollectorProxy;
use App\Controllers\AdminController;

return function (RouteCollectorProxy $group) {
    $ctrl = new AdminController();
    $group->get('/admin/systemlogs', [$ctrl, 'getSystemLogs']);
    $group->get('/admin/mark-updates', [$ctrl, 'getMarkUpdates']);
};
