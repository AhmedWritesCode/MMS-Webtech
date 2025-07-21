<?php

declare(strict_types=1);

namespace Tests\Application\Actions\User;

use App\Application\Actions\ActionPayload;
use App\Domain\User\UserRepository;
use App\Domain\User\User;
use DI\Container;
use Tests\TestCase;

/**
 * Class ListUserActionTest
 * 
 * This class contains a test for the ListUserAction, which is responsible for listing users.
 */
class ListUserActionTest extends TestCase
{
    /**
     * Test the ListUserAction.
     *
     * This test verifies that the /users endpoint returns the expected payload
     * when the UserRepository returns a single user.
     */
    public function testAction()
    {
        // Get the application instance (Slim app)
        $app = $this->getAppInstance();

        /** @var Container $container */
        // Retrieve the dependency injection container from the app
        $container = $app->getContainer();

        // Create a User object to be returned by the mocked repository
        $user = new User(1, 'bill.gates', 'Bill', 'Gates');

        // Create a prophecy (mock) for the UserRepository
        $userRepositoryProphecy = $this->prophesize(UserRepository::class);
        // Set up the expectation: findAll() will be called once and will return an array with the $user
        $userRepositoryProphecy
            ->findAll()
            ->willReturn([$user])
            ->shouldBeCalledOnce();

        // Inject the mocked UserRepository into the container
        $container->set(UserRepository::class, $userRepositoryProphecy->reveal());

        // Create a GET request to the /users endpoint
        $request = $this->createRequest('GET', '/users');
        // Handle the request using the app, which returns a response
        $response = $app->handle($request);

        // Get the response body as a string (JSON payload)
        $payload = (string) $response->getBody();
        // Create the expected ActionPayload object (status 200, with the user array)
        $expectedPayload = new ActionPayload(200, [$user]);
        // Serialize the expected payload to JSON (pretty print for readability)
        $serializedPayload = json_encode($expectedPayload, JSON_PRETTY_PRINT);

        // Assert that the actual response payload matches the expected serialized payload
        $this->assertEquals($serializedPayload, $payload);
    }
}

