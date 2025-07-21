<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

// Import necessary classes for the test
use App\Application\Actions\ActionError;
use App\Application\Actions\ActionPayload;
use App\Application\Handlers\HttpErrorHandler;
use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;
use DI\Container;
use Slim\Middleware\ErrorMiddleware;
use Tests\TestCase;

/**
 * Class ViewUserActionTest
 * 
 * This class contains unit tests for the ViewUserAction.
 * It tests both the successful retrieval of a user and the case where the user is not found.
 */
class ViewUserActionTest extends TestCase
{
    /**
     * Test the action for successfully retrieving a user.
     */
    public function testAction()
    {
        // Get the Slim application instance
        $app = $this->getAppInstance();

        /** @var Container $container */
        // Retrieve the DI container from the app
        $container = $app->getContainer();

        // Create a User object to be returned by the mocked repository
        $user = new User(1, 'bill.gates', 'Bill', 'Gates');

        // Create a prophecy (mock) for the UserRepository
        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->findUserOfId(1) // Expect the findUserOfId method to be called with argument 1
            ->willReturn($user) // It should return the $user object
            ->shouldBeCalledOnce(); // It should be called exactly once

        // Replace the UserRepository in the container with the mocked version
        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        // Create a GET request to the /users/1 endpoint
        $request = $this->createRequest('GET', '/users/1');
        // Handle the request using the Slim app
        $response = $app->handle($request);

        // Get the response body as a string
        $payload = (string) $response->getBody();
        // Create the expected payload for a successful response
        $expectedPayload = new ActionPayload(200, $user);
        // Serialize the expected payload to JSON (pretty print for readability)
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        // Assert that the actual response matches the expected serialized payload
        $this->assertEquals($serializedPayload, $payload);
    }

    /**
     * Test the action when the user is not found, expecting a UserNotFoundException.
     */
    public function testActionThrowsUserNotFoundException()
    {
        // Get the Slim application instance
        $app = $this->getAppInstance();

        // Retrieve the callable resolver and response factory from the app
        $callableResolver = $app->getCallableResolver();
        $responseFactory = $app->getResponseFactory();

        // Create an HTTP error handler for handling exceptions
        $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
        // Create error middleware and set the custom error handler as default
        $errorMiddleware = new ErrorMiddleware($callableResolver, $responseFactory, true, false, false);
        $errorMiddleware->setDefaultErrorHandler($errorHandler);

        // Add the error middleware to the Slim app
        $app->add($errorMiddleware);

        /** @var Container $container */
        // Retrieve the DI container from the app
        $container = $app->getContainer();

        // Create a prophecy (mock) for the UserRepository
        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        $userRepositoryProphecy
            ->findUserOfId(1) // Expect the findUserOfId method to be called with argument 1
            ->willThrow(new UserNotFoundException()) // It should throw a UserNotFoundException
            ->shouldBeCalledOnce(); // It should be called exactly once

        // Replace the UserRepository in the container with the mocked version
        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        // Create a GET request to the /users/1 endpoint
        $request = $this->createRequest('GET', '/users/1');
        // Handle the request using the Slim app
        $response = $app->handle($request);

        // Get the response body as a string
        $payload = (string) $response->getBody();
        // Create the expected error object for a resource not found
        $expectedError = new ActionError(ActionError::RESOURCE_NOT_FOUND, 'The user you requested does not exist.');
        // Create the expected payload for a 404 response
        $expectedPayload = new ActionPayload(404, null, $expectedError);
        // Serialize the expected payload to JSON (pretty print for readability)
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        // Assert that the actual response matches the expected serialized payload
        $this->assertEquals($serializedPayload, $payload);
    }
}

