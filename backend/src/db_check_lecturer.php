<?php
// Include the Database class file from the 'database' directory relative to this file
require_once __DIR__ . '/database/Database.php';

// Import the Database class from the App\Database namespace
use App\Database\Database;

// Get a singleton instance of the Database and retrieve the PDO connection
$db = Database::getInstance()->getConnection();

// Set the username to search for in the database
$username = 'lecture01';

// Prepare a SQL statement to select all columns from the 'users' table where the username matches
$user = $db->prepare("SELECT * FROM users WHERE username = :username");

// Execute the prepared statement, binding the 'username' parameter to the value of $username
$user->execute(['username' => $username]);

// Fetch the first row from the result set as an associative array
$user = $user->fetch();

// If no user was found with the given username, print a message and exit the script
if (!$user) {
    echo "User with username $username does not exist.\n";
    exit(0);
}

// If the user exists, print their information in a formatted string
printf(
    "Username: %s\nEmail: %s\nUser Type: %s\nIs Active: %s\nPassword Hash: %s\n",
    $user['username'],      // The username of the user
    $user['email'],         // The email address of the user
    $user['user_type'],     // The type of user (e.g., lecturer, student, admin)
    $user['is_active'],     // Whether the user is active (likely 1 for active, 0 for inactive)
    $user['password_hash']  // The hashed password of the user
); 