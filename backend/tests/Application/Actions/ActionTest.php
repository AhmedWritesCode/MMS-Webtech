<?php

declare(strict_types=1);

namespace Tests\Application\Actions;

use App\Application\Actions\Action;
use App\Application\Actions\ActionPayload;
use DateTimeImmutable;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Log\LoggerInterface;
use Tests\TestCase;

/**
 * Class ActionTest
 *
 * This class contains tests for the Action base class, specifically testing
 * how HTTP status codes are set in responses.
 */
class ActionTest extends TestCase
{
    /**
     * Test that the respond() method sets the HTTP status code from ActionPayload.
     *
     * This test creates an anonymous Action subclass, overrides the action() method
     * to return a response with a custom status code (202), and asserts that the
     * response status code matches the expected value.
     */
    public function testActionSetsHttpCodeInRespond()
    {
        // Get the Slim application instance
        $app = $this->getAppInstance();

        // Retrieve the dependency injection container from the app
        $container = $app->getContainer();

        // Get the logger instance from the container
        $logger = $container->get(LoggerInterface::class);

        // Create an anonymous subclass of Action for testing
        $testAction = new class ($logger) extends Action {
            /**
             * Constructor for the anonymous Action subclass.
             *
             * @param LoggerInterface $loggerInterface
             */
            public function __construct(
                LoggerInterface $loggerInterface
            ) {
                // Call the parent constructor with the logger
                parent::__construct($loggerInterface);
            }

            /**
             * Handle the action and return a response.
             *
             * @return Response
             */
            public function action(): Response
            {
                // Respond with an ActionPayload with status code 202 and a timestamp
                return $this->respond(
                    new ActionPayload(
                        202,
                        [
                            'willBeDoneAt' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM)
                        ]
                    )
                );
            }
        };

        // Register the test route with the app, using the test action
        $app->get('/test-action-response-code', $testAction);

        // Create a GET request to the test route
        $request = $this->createRequest('GET', '/test-action-response-code');

        // Handle the request and get the response
        $response = $app->handle($request);

        // Assert that the response status code is 202
        $this->assertEquals(202, $response->getStatusCode());
    }

    /**
     * Test that the respondWithData() method sets the HTTP status code.
     *
     * This test creates an anonymous Action subclass, overrides the action() method
     * to return a response with a custom status code (202) using respondWithData(),
     * and asserts that the response status code matches the expected value.
     */
    public function testActionSetsHttpCodeRespondData()
    {
        // Get the Slim application instance
        $app = $this->getAppInstance();

        // Retrieve the dependency injection container from the app
        $container = $app->getContainer();

        // Get the logger instance from the container
        $logger = $container->get(LoggerInterface::class);

        // Create an anonymous subclass of Action for testing
        $testAction = new class ($logger) extends Action {
            /**
             * Constructor for the anonymous Action subclass.
             *
             * @param LoggerInterface $loggerInterface
             */
            public function __construct(
                LoggerInterface $loggerInterface
            ) {
                // Call the parent constructor with the logger
                parent::__construct($loggerInterface);
            }

            /**
             * Handle the action and return a response.
             *
             * @return Response
             */
            public function action(): Response
            {
                // Respond with data and a custom status code (202)
                return $this->respondWithData(
                    [
                        'willBeDoneAt' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM)
                    ],
                    202
                );
            }
        };

        // Register the test route with the app, using the test action
        $app->get('/test-action-response-code', $testAction);

        // Create a GET request to the test route
        $request = $this->createRequest('GET', '/test-action-response-code');

        // Handle the request and get the response
        $response = $app->handle($request);

        // Assert that the response status code is 202
        $this->assertEquals(202, $response->getStatusCode());
    }
}

