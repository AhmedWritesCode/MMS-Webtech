/**
 * Exception thrown when a requested user cannot be found in the system.
 *
 * This exception extends the DomainRecordNotFoundException, providing
 * a specific error message for user-related not found errors.
 *
 * @package App\Domain\User
 */
 

/**
 * Class UserNotFoundException
 *
 * Represents a domain exception that is thrown when a user entity
 * is not found in the application domain.
 */
class UserNotFoundException extends DomainRecordNotFoundException
{
    /**
     * @var string $message
     * The exception message indicating that the requested user does not exist.
     */
    public $message = 'The user you requested does not exist.';
}
