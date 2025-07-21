<?php

use Slim\Factory\AppFactory;
use Slim\Middleware\ErrorMiddleware;
use DI\Container;
use Dotenv\Dotenv;
use Slim\Psr7\Response;

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// Create Container
$container = new Container();

// Set container to create App with on AppFactory
AppFactory::setContainer($container);
$app = AppFactory::create();

// Parse json, form data and xml
$app->addBodyParsingMiddleware();

// Add Routing Middleware
$app->addRoutingMiddleware();

// CORS Middleware - Enable cross-origin requests from frontend
$app->add(function ($request, $handler) use ($app) {
    $origin = $_ENV['FRONTEND_URL'] ?? 'http://localhost:8081';

    // Handle preflight OPTIONS request
    if ($request->getMethod() === 'OPTIONS') {
        $response = new Response();
        return $response
            ->withStatus(200)
            ->withHeader('Access-Control-Allow-Origin', $origin)
            ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
            ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->withHeader('Access-Control-Allow-Credentials', 'true')
            ->withHeader('Access-Control-Max-Age', '3600');
    }

    // Handle actual request
        $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true');
});

// Add a simple test route to verify server is working
$app->get('/', function ($request, $response) {
    $response->getBody()->write(json_encode([
        'message' => 'Student Marks System API',
        'version' => '1.0.0',
        'status' => 'running',
        'timestamp' => date('Y-m-d H:i:s'),
        'cors_enabled' => true,
        'frontend_url' => $_ENV['FRONTEND_URL'] ?? 'http://localhost:8081'
    ]));
    return $response->withHeader('Content-Type', 'application/json');
});

// Group all routes under /api
$app->group('/api', function ($group) {
    (require __DIR__ . '/../routes/users.php')($group);
    (require __DIR__ . '/../routes/students.php')($group);
    (require __DIR__ . '/../routes/course_enrollments.php')($group);
    (require __DIR__ . '/../routes/marks.php')($group);
    (require __DIR__ . '/../routes/notifications.php')($group);
    (require __DIR__ . '/../routes/students-components.php')($group);
    (require __DIR__ . '/../routes/courses.php')($group);
    (require __DIR__ . '/../routes/course_lecturers.php')($group);
    (require __DIR__ . '/../routes/admin.php')($group);
    (require __DIR__ . '/../routes/remark_requests.php')($group);
    (require __DIR__ . '/../routes/auth.php')($group);
    (require __DIR__ . '/../routes/health.php')($group);
    (require __DIR__ . '/../routes/test.php')($group);
    (require __DIR__ . '/../routes/userprofile.php')($group);
    (require __DIR__ . '/../routes/student_performance.php')($group);
    (require __DIR__ . '/../routes/lecturer.php')($group);
    (require __DIR__ . '/../routes/advisor.php')($group);
    (require __DIR__ . '/../routes/catchall.php')($group);
});

// Error Middleware with better error handling
$errorMiddleware = $app->addErrorMiddleware(
    $_ENV['APP_DEBUG'] === 'true', // Display error details
    true, // Log errors
    true  // Log error details
);

// Custom error handler
$errorHandler = function (
    \Psr\Http\Message\ServerRequestInterface $request,
    \Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $statusCode = 500;
    $errorMessage = 'Internal server error';
    
    if ($exception instanceof \Slim\Exception\HttpNotFoundException) {
        $statusCode = 404;
        $errorMessage = 'Resource not found';
    } elseif ($exception instanceof \Slim\Exception\HttpMethodNotAllowedException) {
        $statusCode = 405;
        $errorMessage = 'Method not allowed';
    }

    $response = $app->getResponseFactory()->createResponse($statusCode);
    $errorData = [
        'error' => true,
        'message' => $errorMessage,
        'status_code' => $statusCode
    ];

    if ($displayErrorDetails) {
        $errorData['details'] = $exception->getMessage();
        $errorData['file'] = $exception->getFile();
        $errorData['line'] = $exception->getLine();
        $errorData['trace'] = $exception->getTraceAsString();
    }

    $response->getBody()->write(json_encode($errorData, JSON_PRETTY_PRINT));
    
    // Add CORS headers to error responses as well
    $origin = $_ENV['FRONTEND_URL'] ?? 'http://localhost:8081';
    $response = $response
        ->withHeader('Access-Control-Allow-Origin', $origin)
        ->withHeader('Access-Control-Allow-Headers', 'X-Requested-With, Content-Type, Accept, Origin, Authorization')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
        ->withHeader('Access-Control-Allow-Credentials', 'true')
        ->withHeader('Content-Type', 'application/json');

    return $response;
};

$errorMiddleware->setDefaultErrorHandler($errorHandler);

// Run the application
$app->run();
