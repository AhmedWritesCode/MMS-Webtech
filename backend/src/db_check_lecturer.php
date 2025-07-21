<?php
require_once __DIR__ . '/database/Database.php';
use App\Database\Database;

$db = Database::getInstance()->getConnection();

$username = 'lecture01';
$user = $db->prepare("SELECT * FROM users WHERE username = :username");
$user->execute(['username' => $username]);
$user = $user->fetch();

if (!$user) {
    echo "User with username $username does not exist.\n";
    exit(0);
}

// Print user info
printf(
    "Username: %s\nEmail: %s\nUser Type: %s\nIs Active: %s\nPassword Hash: %s\n",
    $user['username'],
    $user['email'],
    $user['user_type'],
    $user['is_active'],
    $user['password_hash']
); 