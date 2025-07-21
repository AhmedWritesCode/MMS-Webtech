/**
 * Class User
 *
 * Represents a user entity within the system.
 * Implements JsonSerializable for easy JSON encoding.
 *
 * @package App\Domain\User
 */
class User implements JsonSerializable
{
    /**
     * The unique identifier of the user.
     *
     * @var int|null
     */
    private ?int $id;

    /**
     * The username of the user (stored in lowercase).
     *
     * @var string
     */
    private string $username;

    /**
     * The first name of the user (first letter capitalized).
     *
     * @var string
     */
    private string $firstName;

    /**
     * The last name of the user (first letter capitalized).
     *
     * @var string
     */
    private string $lastName;

    /**
     * User constructor.
     *
     * @param int|null $id        The unique identifier of the user.
     * @param string   $username  The username of the user.
     * @param string   $firstName The first name of the user.
     * @param string   $lastName  The last name of the user.
     */
    public function __construct(?int $id, string $username, string $firstName, string $lastName)
    {
        // ...
    }

    /**
     * Gets the unique identifier of the user.
     *
     * @return int|null The user ID.
     */
    public function getId(): ?int
    {
        // ...
    }

    /**
     * Gets the username of the user.
     *
     * @return string The username.
     */
    public function getUsername(): string
    {
        // ...
    }

    /**
     * Gets the first name of the user.
     *
     * @return string The first name.
     */
    public function getFirstName(): string
    {
        // ...
    }

    /**
     * Gets the last name of the user.
     *
     * @return string The last name.
     */
    public function getLastName(): string
    {
        // ...
    }

    /**
     * Specify data which should be serialized to JSON.
     *
     * @return array The data to be serialized by json_encode().
     */
    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        // ...
    }
}
