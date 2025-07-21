<?php
// backend/tests/api_profile_test.php

// Set your backend API URL and JWT token here
$apiUrl = 'http://localhost:8080/api/user/profile';
$jwtToken = 'PASTE_YOUR_JWT_TOKEN_HERE'; // <-- Replace with a valid token

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $apiUrl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $jwtToken,
    'Accept: application/json',
]);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($error) {
    echo "cURL Error: $error\n";
} else {
    echo "HTTP Status: $httpCode\n";
    echo "Response:\n$response\n";
} 