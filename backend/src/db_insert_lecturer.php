<?php
// Script to insert a new lecturer user

// Include the Database class file for database connection
require_once __DIR__ . '/database/Database.php';

// Import the Database class from the App\Database namespace
use App\Database\Database;

// Set up environment variables if needed (for CLI)
// These are used for database connection if not already set
if (!isset($_ENV['DB_HOST'])) {
    $_ENV['DB_HOST'] = 'localhost';      // Database host
    $_ENV['DB_NAME'] = 'course_marks_db';// Database name
    $_ENV['DB_USER'] = 'root';           // Database username
    $_ENV['DB_PASS'] = '';               // Database password
    $_ENV['DB_PORT'] = '3306';           // Database port
}

// Define the lecturer's username
$username = 'lecture01';

// Define the lecturer's email address
$email = 'lecture01@university.edu';

// Define the lecturer's password hash (bcrypt hash of the password)
$password_hash = '$2y$12$2mMF0Q72h/cZVYWiAB6X0OAXrXDsXdk87.6SkgvM9fGFDwHSTkIbK';

// Define the lecturer's full name
$full_name = 'Dr. Example Lecturer';

// Define the user type as 'lecturer'
$user_type = 'lecturer';

// Get a PDO database connection instance from the Database singleton
$db = Database::getInstance()->getConnection();

// Check if a user with the same username already exists in the database
$stmt = $db->prepare('SELECT * FROM users WHERE username = ?');
$stmt->execute([$username]);
if ($stmt->fetch()) {
    // If a user is found, output a message and exit with error code 1
    echo "User with username $username already exists.\n";
    exit(1);
}

// Prepare an SQL statement to insert a new lecturer user into the users table
$stmt = $db->prepare(
    'INSERT INTO users (username, email, password_hash, full_name, user_type, is_active, created_at, updated_at) 
     VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())'
);

// Execute the prepared statement with the lecturer's details
$stmt->execute([$username, $email, $password_hash, $full_name, $user_type]);

// Output a success message
echo "Lecturer user $username inserted successfully.\n"; 