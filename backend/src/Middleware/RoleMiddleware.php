/**
 * RoleMiddleware
 *
 * Middleware to restrict access to routes based on user roles.
 * Checks if the current user's role is in the list of allowed roles.
 * If not, returns a 403 Forbidden response.
 *
 * @package App\Middleware
 */

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class RoleMiddleware
{
    /**
     * @var array List of roles allowed to access the route.
     */
    private array $allowedRoles;

    /**
     * RoleMiddleware constructor.
     *
     * @param array $allowedRoles Array of allowed user roles.
     */
    public function __construct(array $allowedRoles)
    {
        $this->allowedRoles = $allowedRoles;
    }

    /**
     * Middleware invokable method.
     *
     * Checks if the user role is allowed. If not, returns a forbidden response.
     *
     * @param Request $request The PSR-7 server request.
     * @param RequestHandler $handler The request handler.
     * @return Response The response, either from the handler or a forbidden response.
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        $userRole = $request->getAttribute('user_role');

        if (!$userRole || !in_array($userRole, $this->allowedRoles)) {
            return $this->forbiddenResponse('Access denied: Insufficient permissions');
        }

        return $handler->handle($request);
    }

    /**
     * Generates a 403 Forbidden response with a JSON error message.
     *
     * @param string $message The error message to return.
     * @return Response The forbidden response.
     */
    private function forbiddenResponse(string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode([
            'error' => true,
            'message' => $message
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(403);
    }
}