<?php

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

/**
 * Middleware for validating request data based on provided rules.
 */
class ValidationMiddleware
{
    /**
     * @var array The validation rules for each field.
     */
    private array $rules;

    /**
     * Constructor to initialize validation rules.
     *
     * @param array $rules Array of validation rules.
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Middleware invokable method.
     *
     * @param Request $request The PSR-7 request object.
     * @param RequestHandler $handler The next request handler.
     * @return Response The PSR-7 response object.
     */
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        // Retrieve parsed body data from the request (e.g., POST data)
        $data = $request->getParsedBody();
        $errors = []; // Array to collect validation errors

        // Iterate over each field and its validation rules
        foreach ($this->rules as $field => $rule) {
            // Get the value for the current field, or null if not set
            $value = $data[$field] ?? null;

            // Required field validation
            if (isset($rule['required']) && $rule['required'] && empty($value)) {
                $errors[$field] = "Field '{$field}' is required";
                continue; // Skip further validation for this field
            }

            // If field is empty and not required, skip other validations
            if (empty($value)) {
                continue;
            }

            // Type validation (email, integer, numeric)
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        // Validate email format
                        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
                            $errors[$field] = "Field '{$field}' must be a valid email";
                        }
                        break;
                    case 'integer':
                        // Validate integer value
                        if (!filter_var($value, FILTER_VALIDATE_INT)) {
                            $errors[$field] = "Field '{$field}' must be an integer";
                        }
                        break;
                    case 'numeric':
                        // Validate numeric value
                        if (!is_numeric($value)) {
                            $errors[$field] = "Field '{$field}' must be numeric";
                        }
                        break;
                }
            }

            // Minimum length validation
            if (isset($rule['min_length']) && strlen($value) < $rule['min_length']) {
                $errors[$field] = "Field '{$field}' must be at least {$rule['min_length']} characters";
            }

            // Maximum length validation
            if (isset($rule['max_length']) && strlen($value) > $rule['max_length']) {
                $errors[$field] = "Field '{$field}' must not exceed {$rule['max_length']} characters";
            }

            // Pattern (regex) validation
            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $value)) {
                // Use custom pattern message if provided, otherwise default message
                $errors[$field] = $rule['pattern_message'] ?? "Field '{$field}' format is invalid";
            }

            // Custom validation callback
            if (isset($rule['custom']) && is_callable($rule['custom'])) {
                // Execute custom validation function
                $customResult = $rule['custom']($value);
                // If custom validation fails, add error message
                if ($customResult !== true) {
                    $errors[$field] = $customResult;
                }
            }
        }

        // If there are validation errors, return a 400 response with error details
        if (!empty($errors)) {
            $response = new \Slim\Psr7\Response();
            $response->getBody()->write(json_encode([
                'error' => true,
                'message' => 'Validation failed',
                'errors' => $errors
            ]));

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(400);
        }

        // If validation passes, proceed to the next middleware/handler
        return $handler->handle($request);
    }
}