<?php

declare(strict_types=1);

namespace Tests\Infrastructure\Persistence\User;

// Import the User domain model
use App\Domain\User\User;
// Import the exception thrown when a user is not found
use App\Domain\User\UserNotFoundException;
// Import the in-memory user repository implementation
use App\Infrastructure\Persistence\User\InMemoryUserRepository;
// Import the base test case class
use Tests\TestCase;

/**
 * Class InMemoryUserRepositoryTest
 *
 * Unit tests for the InMemoryUserRepository class.
 */
class InMemoryUserRepositoryTest extends TestCase
{
    /**
     * Test that findAll() returns all users in the repository.
     */
    public function testFindAll()
    {
        // Create a User instance
        $user = new User(1, 'bill.gates', 'Bill', 'Gates');

        // Instantiate the repository with one user
        $userRepository = new InMemoryUserRepository([1 => $user]);

        // Assert that findAll returns an array containing the user
        $this->assertEquals([$user], $userRepository->findAll());
    }

    /**
     * Test that findAll() returns the default set of users when no users are provided.
     */
    public function testFindAllUsersByDefault()
    {
        // Define the expected default users
        $users = [
            1 => new User(1, 'bill.gates', 'Bill', 'Gates'),
            2 => new User(2, 'steve.jobs', 'Steve', 'Jobs'),
            3 => new User(3, 'mark.zuckerberg', 'Mark', 'Zuckerberg'),
            4 => new User(4, 'evan.spiegel', 'Evan', 'Spiegel'),
            5 => new User(5, 'jack.dorsey', 'Jack', 'Dorsey'),
        ];

        // Instantiate the repository with no users (should use defaults)
        $userRepository = new InMemoryUserRepository();

        // Assert that findAll returns the default users (as a numerically indexed array)
        $this->assertEquals(array_values($users), $userRepository->findAll());
    }

    /**
     * Test that findUserOfId() returns the correct user for a given ID.
     */
    public function testFindUserOfId()
    {
        // Create a User instance
        $user = new User(1, 'bill.gates', 'Bill', 'Gates');

        // Instantiate the repository with one user
        $userRepository = new InMemoryUserRepository([1 => $user]);

        // Assert that findUserOfId returns the correct user
        $this->assertEquals($user, $userRepository->findUserOfId(1));
    }

    /**
     * Test that findUserOfId() throws UserNotFoundException when the user does not exist.
     */
    public function testFindUserOfIdThrowsNotFoundException()
    {
        // Instantiate the repository with no users
        $userRepository = new InMemoryUserRepository([]);
        // Expect a UserNotFoundException to be thrown
        $this->expectException(UserNotFoundException::class);
        // Attempt to find a user that does not exist
        $userRepository->findUserOfId(1);
    }
}

