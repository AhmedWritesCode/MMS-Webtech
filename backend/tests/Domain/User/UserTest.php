<?php

declare(strict_types=1); // Enforce strict type checking for this file

namespace Tests\Domain\User; // Define the namespace for this test class

use App\Domain\User\User; // Import the User class to be tested
use Tests\TestCase; // Import the base TestCase class for PHPUnit

/**
 * Class UserTest
 * 
 * This class contains unit tests for the User domain model.
 */
class UserTest extends TestCase
{
    /**
     * Provides test data for user-related tests.
     *
     * @return array List of user data sets, each containing:
     *               - int $id: The user's ID
     *               - string $username: The user's username
     *               - string $firstName: The user's first name
     *               - string $lastName: The user's last name
     */
    public function userProvider(): array
    {
        return [
            [1, 'bill.gates', 'Bill', 'Gates'],
            [2, 'steve.jobs', 'Steve', 'Jobs'],
            [3, 'mark.zuckerberg', 'Mark', 'Zuckerberg'],
            [4, 'evan.spiegel', 'Evan', 'Spiegel'],
            [5, 'jack.dorsey', 'Jack', 'Dorsey'],
        ];
    }

    /**
     * Test the getter methods of the User class.
     *
     * @dataProvider userProvider Supplies multiple sets of user data to this test.
     * @param int    $id        The user's ID
     * @param string $username  The user's username
     * @param string $firstName The user's first name
     * @param string $lastName  The user's last name
     */
    public function testGetters(int $id, string $username, string $firstName, string $lastName)
    {
        // Create a new User instance with the provided data
        $user = new User($id, $username, $firstName, $lastName);

        // Assert that each getter returns the expected value
        $this->assertEquals($id, $user->getId());
        $this->assertEquals($username, $user->getUsername());
        $this->assertEquals($firstName, $user->getFirstName());
        $this->assertEquals($lastName, $user->getLastName());
    }

    /**
     * Test the JSON serialization of the User class.
     *
     * @dataProvider userProvider Supplies multiple sets of user data to this test.
     * @param int    $id        The user's ID
     * @param string $username  The user's username
     * @param string $firstName The user's first name
     * @param string $lastName  The user's last name
     */
    public function testJsonSerialize(int $id, string $username, string $firstName, string $lastName)
    {
        // Create a new User instance with the provided data
        $user = new User($id, $username, $firstName, $lastName);

        // Prepare the expected JSON payload as a string
        $expectedPayload = json_encode([
            'id' => $id,
            'username' => $username,
            'firstName' => $firstName,
            'lastName' => $lastName,
        ]);

        // Assert that serializing the User object to JSON matches the expected payload
        $this->assertEquals($expectedPayload, json_encode($user));
    }
}

