<?php

// Enforce strict types for better type safety.
declare(strict_types=1);

// Define the namespace for the test classes.
namespace Tests;

// Import necessary classes from external libraries and frameworks.
use DI\ContainerBuilder; // For building the dependency injection container.
use Exception; // For exception handling.
use PHPUnit\Framework\TestCase as PHPUnit_TestCase; // Base PHPUnit test case class.
use Prophecy\PhpUnit\ProphecyTrait; // Trait for using Prophecy mocking with PHPUnit.
use Psr\Http\Message\ServerRequestInterface as Request; // PSR-7 request interface.
use Slim\App; // Slim application class.
use Slim\Factory\AppFactory; // Factory for creating Slim app instances.
use Slim\Psr7\Factory\StreamFactory; // Factory for creating PSR-7 streams.
use Slim\Psr7\Headers; // Slim's implementation of HTTP headers.
use Slim\Psr7\Request as SlimRequest; // Slim's implementation of PSR-7 request.
use Slim\Psr7\Uri; // Slim's implementation of PSR-7 URI.

/**
 * Base test case class for the application.
 * Extends PHPUnit's TestCase and provides helper methods for setting up the app and requests.
 */
class TestCase extends PHPUnit_TestCase
{
    // Use the ProphecyTrait to enable Prophecy mocking in tests.
    use ProphecyTrait;

    /**
     * Returns an instance of the Slim application, fully configured for testing.
     *
     * @return App The configured Slim application instance.
     * @throws Exception If there is an error during setup.
     */
    protected function getAppInstance(): App
    {
        // Instantiate PHP-DI ContainerBuilder for dependency injection.
        $containerBuilder = new ContainerBuilder();

        // Note: Container is intentionally not compiled for tests to allow for easier debugging.

        // Load and apply application settings from the settings file.
        $settings = require __DIR__ . '/../app/settings.php';
        $settings($containerBuilder);

        // Load and apply dependencies from the dependencies file.
        $dependencies = require __DIR__ . '/../app/dependencies.php';
        $dependencies($containerBuilder);

        // Load and apply repositories from the repositories file.
        $repositories = require __DIR__ . '/../app/repositories.php';
        $repositories($containerBuilder);

        // Build the PHP-DI container instance with all settings, dependencies, and repositories.
        $container = $containerBuilder->build();

        // Set the built container as the Slim app's container.
        AppFactory::setContainer($container);

        // Create a new Slim application instance.
        $app = AppFactory::create();

        // Load and register middleware from the middleware file.
        $middleware = require __DIR__ . '/../app/middleware.php';
        $middleware($app);

        // Load and register routes from the routes file.
        $routes = require __DIR__ . '/../app/routes.php';
        $routes($app);

        // Return the fully configured Slim application instance.
        return $app;
    }

    /**
     * Creates a PSR-7 server request for use in tests.
     *
     * @param string $method       The HTTP method (e.g., 'GET', 'POST').
     * @param string $path         The request path (e.g., '/api/users').
     * @param array  $headers      Optional HTTP headers for the request.
     * @param array  $cookies      Optional cookies for the request.
     * @param array  $serverParams Optional server parameters for the request.
     * @return Request             The constructed PSR-7 server request.
     */
    protected function createRequest(
        string $method,
        string $path,
        array $headers = ['HTTP_ACCEPT' => 'application/json'],
        array $cookies = [],
        array $serverParams = []
    ): Request {
        // Create a new URI object for the request.
        $uri = new Uri('', '', 80, $path);

        // Create a temporary stream resource for the request body.
        $handle = fopen('php://temp', 'w+');
        $stream = (new StreamFactory())->createStreamFromResource($handle);

        // Create a new Headers object and add provided headers.
        $h = new Headers();
        foreach ($headers as $name => $value) {
            $h->addHeader($name, $value);
        }

        // Create and return a new SlimRequest object with all specified parameters.
        return new SlimRequest($method, $uri, $h, $cookies, $serverParams, $stream);
    }
}

