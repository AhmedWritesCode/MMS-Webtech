<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\User;

use App\Domain\User\User;
use App\Domain\User\UserNotFoundException;
use App\Domain\User\UserRepository;

/**
 * In-memory implementation of the UserRepository interface.
 * Stores User objects in a local array for testing or prototyping purposes.
 */
class InMemoryUserRepository implements UserRepository
{
    /**
     * @var User[] Array of User objects, keyed by user ID.
     */
    private array $users;

    /**
     * Constructor for InMemoryUserRepository.
     *
     * @param User[]|null $users Optional array of User objects to initialize the repository.
     *                           If null, a default set of users is created.
     */
    public function __construct(array $users = null)
    {
        // If no users are provided, initialize with a default set of users.
        $this->users = $users ?? [
            1 => new User(1, 'bill.gates', 'Bill', 'Gates'),
            2 => new User(2, 'steve.jobs', 'Steve', 'Jobs'),
            3 => new User(3, 'mark.zuckerberg', 'Mark', 'Zuckerberg'),
            4 => new User(4, 'evan.spiegel', 'Evan', 'Spiegel'),
            5 => new User(5, 'jack.dorsey', 'Jack', 'Dorsey'),
        ];
    }

    /**
     * Returns all users in the repository.
     *
     * @return User[] Array of User objects.
     */
    public function findAll(): array
    {
        // Return all users as a numerically indexed array.
        return array_values($this->users);
    }

    /**
     * Finds a user by their unique ID.
     *
     * @param int $id The ID of the user to find.
     * @return User The User object corresponding to the given ID.
     * @throws UserNotFoundException If no user with the given ID exists.
     */
    public function findUserOfId(int $id): User
    {
        // Check if the user exists in the array.
        if (!isset($this->users[$id])) {
            // Throw an exception if the user is not found.
            throw new UserNotFoundException();
        }

        // Return the found User object.
        return $this->users[$id];
    }
}

