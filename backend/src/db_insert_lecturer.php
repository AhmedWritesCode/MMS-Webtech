<?php
// Script to insert a new lecturer user
require_once __DIR__ . '/database/Database.php';

use App\Database\Database;

// Set up environment variables if needed (for CLI)
if (!isset($_ENV['DB_HOST'])) {
    $_ENV['DB_HOST'] = 'localhost';
    $_ENV['DB_NAME'] = 'course_marks_db';
    $_ENV['DB_USER'] = 'root';
    $_ENV['DB_PASS'] = '';
    $_ENV['DB_PORT'] = '3306';
}

$username = 'lecture01';
$email = 'lecture01@university.edu';
$password_hash = '$2y$12$2mMF0Q72h/cZVYWiAB6X0OAXrXDsXdk87.6SkgvM9fGFDwHSTkIbK';
$full_name = 'Dr. Example Lecturer';
$user_type = 'lecturer';

$db = Database::getInstance()->getConnection();

// Check if user already exists
$stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    echo "User with username $username already exists.\n";
    exit(1);
}

// Insert new lecturer
$stmt = $db->prepare('INSERT INTO users (username, email, password_hash, full_name, user_type, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())');
$stmt->execute([$username, $email, $password_hash, $full_name, $user_type]);
echo "Lecturer user $username inserted successfully.\n"; 