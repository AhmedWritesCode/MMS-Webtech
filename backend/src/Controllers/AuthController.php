<?php

// app/Controllers/AuthController.php

namespace App\Controllers;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Exception;

class AuthController extends BaseController
{
    private string $jwtSecret;
    
    public function __construct()
    {
        parent::__construct();
        $this->jwtSecret = $_ENV['JWT_SECRET'] ?? '5f7T&zA9!8vKx2QrL6jPnB3dWm$EjYc';
    }

    /**
     * User login endpoint
     * POST /api/auth/login
     */
    public function login(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            
            // Validate required fields
            $requiredFields = ['username', 'password', 'role'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->errorResponse($response, "Field '$field' is required", 400);
                }
            }
            
            $username = trim($data['username']);
            $password = $data['password'];
            $role = $data['role'];
            $rememberMe = $data['remember_me'] ?? false;
            
            // Validate role
            $validRoles = ['student', 'advisor', 'lecturer', 'admin'];
            if (!in_array($role, $validRoles)) {
                return $this->errorResponse($response, 'Invalid role specified', 400);
            }
            
            // Find user by username and role
            $user = $this->findUserByCredentials($username, $role);
            
            if (!$user) {
                return $this->errorResponse($response, 'Invalid credentials', 401);
            }
            
            // Verify password (note: database uses password_hash field)
            if (!$this->verifyPassword($password, $user['password_hash'], $role)) {
                return $this->errorResponse($response, 'Invalid credentials', 401);
            }
            
            // Check if user account is active
            if (!$this->checkAccountStatus($user)) {
                return $this->errorResponse($response, 'Account is not active', 403);
            }
            
            // Generate JWT token for the user
            $token = $this->generateJWT($user['id'], $role, $rememberMe);
            
            // Update last login
            $this->updateLastLogin($user['id'], $role);
            
            // Prepare user data for response
            $userData = $this->formatUserData($user, $role);
            
            // Calculate expiration time for response
            $expiresIn = $rememberMe ? 2592000 : 28800; // 30 days or 8 hours
            
            return $this->successResponse($response, [
                'token' => $token,
                'user' => $userData,
                'expires_in' => $expiresIn,
            ], 'Login successful');
            
        } catch (Exception $e) {
            error_log("Login error: " . $e->getMessage());
            return $this->errorResponse($response, 'Login failed', 500);
        }
    }
    
    /**
     * User logout endpoint
     * POST /api/auth/logout
     */
    public function logout(Request $request, Response $response): Response
    {
        try {
            // Get user ID from token if available
            $userId = $this->getUserId($request);
            
            if ($userId) {
                // Log the logout activity
                $this->logActivity($userId, 'logout', 'User logged out successfully');
            }
            
            // In a real implementation, you might want to blacklist the token
            // For now, we'll just return success since JWT is stateless
            
            return $this->successResponse($response, null, 'Logout successful');
            
        } catch (Exception $e) {
            error_log("Logout error: " . $e->getMessage());
            return $this->errorResponse($response, 'Logout failed', 500);
        }
    }
    
    /**
     * Refresh token endpoint
     * POST /api/auth/refresh
     */
    public function refreshToken(Request $request, Response $response): Response
    {
        try {
            $authHeader = $request->getHeaderLine('Authorization');
            
            if (empty($authHeader)) {
                return $this->errorResponse($response, 'Authorization header missing', 401);
            }
            
            $token = str_replace('Bearer ', '', $authHeader);
            
            // Verify and decode current token
            $payload = $this->verifyJWT($token);
            
            if (!$payload) {
                return $this->errorResponse($response, 'Invalid token', 401);
            }
            
            // Generate new token
            $newToken = $this->generateJWT($payload['user_id'], $payload['role'], false);
            
            return $this->successResponse($response, [
                'token' => $newToken,
                'expires_in' => 86400, // 24 hours
            ], 'Token refreshed successfully');
            
        } catch (Exception $e) {
            error_log("Token refresh error: " . $e->getMessage());
            return $this->errorResponse($response, 'Token refresh failed', 500);
        }
    }

   
    /**
 * Get user profile endpoint
 * GET /api/user/profile
 */
public function getUserProfile(Request $request, Response $response): Response
{
    try {
        $userId = $this->getUserId($request);
        $userRole = $this->getUserRole($request);
        
        error_log("Getting profile for user ID: $userId, role: $userRole");
        
        if (!$userId || !$userRole) {
            return $this->errorResponse($response, 'User not authenticated', 401);
        }
        
        // Simple database query without role filtering for now
        $sql = "SELECT * FROM users WHERE id = ? AND is_active = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$user) {
            error_log("User not found in database for ID: $userId");
            return $this->errorResponse($response, 'User not found', 404);
        }
        
        error_log("Found user: " . json_encode($user));
        
        $userData = $this->formatUserData($user, $userRole);
        
        return $this->successResponse($response, $userData, 'Profile retrieved successfully');
        
    } catch (Exception $e) {
        error_log("Get profile error: " . $e->getMessage());
        return $this->errorResponse($response, 'Failed to retrieve profile', 500);
    }
}

    /**
     * Change password endpoint
     * PUT /api/auth/change-password
     */
    public function changePassword(Request $request, Response $response): Response
    {
        try {
            $data = $request->getParsedBody();
            $userId = $this->getUserId($request);
            $userRole = $this->getUserRole($request);
            
            if (!$userId || !$userRole) {
                return $this->errorResponse($response, 'User not authenticated', 401);
            }
            
            // Validate required fields
            $requiredFields = ['current_password', 'new_password'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    return $this->errorResponse($response, "Field '$field' is required", 400);
                }
            }
            
            $currentPassword = $data['current_password'];
            $newPassword = $data['new_password'];
            
            // Get current user
            $user = $this->findUserById($userId, $userRole);
            if (!$user) {
                return $this->errorResponse($response, 'User not found', 404);
            }
            
            // Verify current password (note: database uses password_hash field)
            if (!$this->verifyPassword($currentPassword, $user['password_hash'], $userRole)) {
                return $this->errorResponse($response, 'Current password is incorrect', 400);
            }
            
            // Validate new password strength
            if (!$this->validatePasswordStrength($newPassword, $userRole)) {
                $minLength = $userRole === 'student' ? 6 : 6;
                return $this->errorResponse($response, "New password must be at least {$minLength} characters long", 400);
            }
            
            // Hash and update password
            $hashedPassword = $this->hashPassword($newPassword, $userRole);
            $this->updateUserPassword($userId, $userRole, $hashedPassword);
            
            // Log the activity
            $this->logActivity($userId, 'password_change', 'Password changed successfully');
            
            return $this->successResponse($response, null, 'Password changed successfully');
            
        } catch (Exception $e) {
            error_log("Change password error: " . $e->getMessage());
            return $this->errorResponse($response, 'Failed to change password', 500);
        }
    }

    /**
     * Find user by credentials based on role
     */
    private function findUserByCredentials(string $username, string $role): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE username = ? AND user_type = ? AND is_active = 1 LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$username, $role]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
            
        } catch (Exception $e) {
            error_log("Find user error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Find user by ID based on role
     */
    private function findUserById(int $userId, string $role): ?array
    {
        try {
            $sql = "SELECT * FROM users WHERE id = ? AND user_type = ? AND is_active = 1 LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId, $role]);
            
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            return $user ?: null;
            
        } catch (Exception $e) {
            error_log("Find user by ID error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Verify password based on role
     */
    private function verifyPassword(string $inputPassword, string $storedPassword, string $role): bool
    {
        // For all roles, use password_verify for bcrypt hashes
        // The database stores bcrypt hashes for all users
        return password_verify($inputPassword, $storedPassword);
    }

    /**
     * Hash password based on role
     */
    private function hashPassword(string $password, string $role): string
    {
        if ($role === 'student') {
            // For students using PIN, you might store as is or use light hashing
            // In production, consider hashing PINs too
            return $password;
        } else {
            // For other roles, use bcrypt
            return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        }
    }

    /**
     * Generate JWT token
     */
    private function generateJWT(int $userId, string $role, bool $rememberMe = false): string
    {
        $issuedAt = time();
        
        // Set expiration time based on remember me preference
        if ($rememberMe) {
            // Remember me: 30 days (2592000 seconds)
            $expirationTime = $issuedAt + 2592000;
        } else {
            // Session-based: 8 hours (28800 seconds) - expires when browser closes
            $expirationTime = $issuedAt + 28800;
        }
        
        $payload = [
            'iss' => 'student-marks-system',
            'aud' => 'student-marks-system-users',
            'iat' => $issuedAt,
            'exp' => $expirationTime,
            'user_id' => $userId,
            'role' => $role,
            'remember_me' => $rememberMe
        ];
        
        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    /**
     * Verify JWT token
     */
    private function verifyJWT(string $token): ?array
    {
        try {
            $decoded = JWT::decode($token, new Key($this->jwtSecret, 'HS256'));
            return (array) $decoded;
        } catch (Exception $e) {
            error_log("JWT verification error: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Update last login timestamp
     */
    private function updateLastLogin(int $userId, string $role): void
    {
        try {
            // Add last_login column if it doesn't exist, or use updated_at
            $sql = "UPDATE users SET updated_at = NOW() WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$userId]);
        } catch (Exception $e) {
            error_log("Update last login error: " . $e->getMessage());
        }
    }

    /**
     * Update user password
     */
    private function updateUserPassword(int $userId, string $role, string $hashedPassword): void
    {
        $sql = "UPDATE users SET password_hash = ?, updated_at = NOW() WHERE id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$hashedPassword, $userId]);
    }

    /**
     * Format user data for response
     */
    private function formatUserData(array $user, string $role): array
    {
        $baseData = [
            'id' => (int)($user['id'] ?? 0),
            'username' => $user['username'] ?? '',
            'role' => $role,
            'full_name' => $user['full_name'] ?? '',
            'email' => $user['email'] ?? '',
            'user_type' => $user['user_type'] ?? $role,
            'is_active' => isset($user['is_active']) ? (bool)$user['is_active'] : true,
            'created_at' => $user['created_at'] ?? null,
            'updated_at' => $user['updated_at'] ?? null,
        ];

        // Add role-specific data formatting if needed
        switch ($role) {
            case 'student':
                $baseData['matric_number'] = $user['username'] ?? ''; // For students, username is matric number
                break;

            case 'lecturer':
            case 'advisor':
                $baseData['staff_id'] = $user['username'] ?? ''; // For staff, username is staff ID
                break;

            case 'admin':
                // Username remains as is for admin
                break;
        }

        return $baseData;
    }

    /**
     * Check if user account is active
     */
    private function checkAccountStatus(array $user): bool
    {
        return (bool)$user['is_active'];
    }

    /**
     * Log user activity using audit_log table
     */
    private function logActivity(int $userId, string $action, string $description): void
    {
        try {
            $sql = "INSERT INTO audit_log (user_id, action, table_name, record_id, ip_address, user_agent, created_at) 
                    VALUES (?, ?, 'users', ?, ?, ?, NOW())";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $userId,
                strtoupper($action),
                $userId,
                $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
            ]);
        } catch (Exception $e) {
            error_log("Activity log error: " . $e->getMessage());
        }
    }

    /**
     * Validate password strength
     */
    private function validatePasswordStrength(string $password, string $role): bool
    {
        if ($role === 'student') {
            // PIN validation: exactly 6 digits
            return preg_match('/^\d{6}$/', $password);
        } else {
            // Password validation: at least 6 characters
            return strlen($password) >= 6;
        }
    }}