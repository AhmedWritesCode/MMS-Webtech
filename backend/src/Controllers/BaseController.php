<?php

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

abstract class BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    /**
     * Send JSON response with proper headers
     */
    protected function jsonResponse(Response $response, $data, int $statusCode = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }

    /**
     * Send error response
     */
    protected function errorResponse(Response $response, string $message, int $statusCode = 400): Response
    {
        return $this->jsonResponse($response, [
            'error' => true,
            'message' => $message
        ], $statusCode);
    }

    /**
     * Send success response
     */
    protected function successResponse(Response $response, $data = null, string $message = 'Success'): Response
    {
        $responseData = [
            'success' => true,
            'message' => $message
        ];

        if ($data !== null) {
            $responseData['data'] = $data;
        }

        return $this->jsonResponse($response, $responseData);
    }

    /**
     * Get user ID from request (assuming JWT middleware sets it)
     */
    protected function getUserId(Request $request): ?int
    {
        $userId = $request->getAttribute('user_id');
        return $userId ? (int)$userId : null;
    }

    /**
     * Get user role from request
     */
    protected function getUserRole(Request $request): ?string
    {
        return $request->getAttribute('user_role');
    }

    /**
     * Validate required fields in request body
     */
    protected function validateRequired(array $data, array $required): array
    {
        $missing = [];
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        return $missing;
    }

    /**
     * Calculate GPA from marks
     */
    protected function calculateGPA(array $marks): float
    {
        if (empty($marks)) {
            return 0.0;
        }

        $totalPoints = 0;
        $totalCredits = 0;

        foreach ($marks as $mark) {
            $gradePoint = $this->getGradePoint($mark['percentage']);
            $totalPoints += $gradePoint * $mark['credits'];
            $totalCredits += $mark['credits'];
        }

        return $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0.0;
    }

    /**
     * Convert percentage to grade point
     */
    protected function getGradePoint(float $percentage): float
    {
        if ($percentage >= 80) return 4.0;
        if ($percentage >= 75) return 3.7;
        if ($percentage >= 70) return 3.3;
        if ($percentage >= 65) return 3.0;
        if ($percentage >= 60) return 2.7;
        if ($percentage >= 55) return 2.3;
        if ($percentage >= 50) return 2.0;
        if ($percentage >= 45) return 1.7;
        if ($percentage >= 40) return 1.3;
        return 0.0;
    }

    /**
     * Get grade letter from percentage
     */
    protected function getGradeLetter(float $percentage): string
    {
        if ($percentage >= 80) return 'A';
        if ($percentage >= 75) return 'A-';
        if ($percentage >= 70) return 'B+';
        if ($percentage >= 65) return 'B';
        if ($percentage >= 60) return 'B-';
        if ($percentage >= 55) return 'C+';
        if ($percentage >= 50) return 'C';
        if ($percentage >= 45) return 'C-';
        if ($percentage >= 40) return 'D';
        return 'F';
    }
}