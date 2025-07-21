<?php

// app/Middleware/JWTAuthMiddleware.php

namespace App\Middleware;

// Import necessary PSR-7 and PSR-15 interfaces for HTTP messages and middleware handling
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

// Import Firebase JWT classes for token decoding and verification
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

/**
 * JWTAuthMiddleware
 * 
 * Middleware to authenticate requests using JWT tokens.
 * Checks for a valid Bearer token in the Authorization header,
 * decodes and verifies the token, and attaches user info to the request.
 */
class JWTAuthMiddleware
{
    // Secret key used to sign and verify JWT tokens
    private string $jwtSecret;

    /**
     * Constructor
     * Initializes the JWT secret from environment variable or uses a default value.
     */
    public function __construct()
    {
        // Get JWT secret from environment variable, fallback to default if not set
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? '5f7T&zA9!8vKx2QrL6jPnB3dWm$EjYc';
    }

    /**
     * Middleware invocation method
     * 
     * @param Request $request The incoming HTTP request
     * @param RequestHandler $handler The next middleware or request handler
     * @return Response The HTTP response
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Get the Authorization header from the request
        $authHeader = $request->getHeaderLine('Authorization');

        // If the Authorization header is missing, return 401 Unauthorized
        if (empty($authHeader)) {
            return $this->unauthorizedResponse('Authorization header missing');
        }

        // Extract the Bearer token from the Authorization header using regex
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            // If the header format is invalid, return 401 Unauthorized
            return $this->unauthorizedResponse('Invalid authorization header format');
        }

        // The JWT token is captured in the first matching group
        $token = $matches[1];

        try {
            // Decode and verify the JWT token using the secret and HS256 algorithm
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            // Convert the decoded payload to an associative array
            $payload = (array) $decoded;

            // Check if the token has expired by comparing 'exp' claim to current time
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                // If expired, return 401 Unauthorized
                return $this->unauthorizedResponse('Token has expired');
            }

            // Attach user information from the token payload to the request attributes
            $request = $request->withAttribute('user_id', $payload['user_id']);
            $request = $request->withAttribute('user_role', $payload['role']);
            $request = $request->withAttribute('jwt_payload', $payload);

            // Pass the request to the next middleware or controller
            return $handler->handle($request);

        } catch (Exception $e) {
            // Log any exceptions during token decoding/verification
            error_log("JWT Authentication error: " . $e->getMessage());
            // Return 401 Unauthorized for invalid or expired tokens
            return $this->unauthorizedResponse('Invalid or expired token');
        }
    }

    /**
     * Helper method to generate a 401 Unauthorized JSON response
     * 
     * @param string $message The error message to return
     * @return Response The HTTP response with 401 status and JSON body
     */
    private function unauthorizedResponse(string $message): Response
    {
        // Create a new Slim PSR-7 response object
        $response = new \Slim\Psr7\Response();
        // Write the error message as a JSON payload to the response body
        $response->getBody()->write(json_encode([
            'error' => true,
            'message' => $message
        ]));

        // Set the Content-Type header to application/json and status to 401 Unauthorized
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
}

