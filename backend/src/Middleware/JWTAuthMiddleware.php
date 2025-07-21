<?php

// app/Middleware/JWTAuthMiddleware.php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class JWTAuthMiddleware
{
    private string $jwtSecret;

    public function __construct()
    {
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? '5f7T&zA9!8vKx2QrL6jPnB3dWm$EjYc';
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Get the Authorization header
        $authHeader = $request->getHeaderLine('Authorization');

        if (empty($authHeader)) {
            return $this->unauthorizedResponse('Authorization header missing');
        }

        // Extract token from Bearer header
        if (!preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
            return $this->unauthorizedResponse('Invalid authorization header format');
        }

        $token = $matches[1];

        try {
            // Verify and decode the JWT token
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            $payload = (array) $decoded;

            // Check if token is expired
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                return $this->unauthorizedResponse('Token has expired');
            }

            // Add user information to request attributes
            $request = $request->withAttribute('user_id', $payload['user_id']);
            $request = $request->withAttribute('user_role', $payload['role']);
            $request = $request->withAttribute('jwt_payload', $payload);

            // Continue to the next middleware/controller
            return $handler->handle($request);

        } catch (Exception $e) {
            error_log("JWT Authentication error: " . $e->getMessage());
            return $this->unauthorizedResponse('Invalid or expired token');
        }
    }

    private function unauthorizedResponse(string $message): Response
    {
        $response = new \Slim\Psr7\Response();
        $response->getBody()->write(json_encode([
            'error' => true,
            'message' => $message
        ]));

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus(401);
    }
}
