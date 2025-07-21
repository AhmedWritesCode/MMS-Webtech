<?php
// backend/tests/api_profile_test.php

// Set your backend API URL and JWT token here
$apiUrl = 'http://localhost:8080/api/user/profile'; // The endpoint for fetching the user profile
$jwtToken = 'PASTE_YOUR_JWT_TOKEN_HERE'; // <-- Replace with a valid token

// Initialize a new cURL session
$ch = curl_init();

// Set the URL to send the request to
curl_setopt($ch, CURLOPT_URL, $apiUrl);

// Return the response as a string instead of outputting it directly
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

// Set HTTP headers for the request, including Authorization and Accept headers
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $jwtToken, // Pass the JWT token for authentication
    'Accept: application/json',           // Specify that we expect a JSON response
]);

// Execute the cURL request and store the response
$response = curl_exec($ch);

// Get the HTTP status code from the response
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Get any error that occurred during the cURL request
$error = curl_error($ch);

// Close the cURL session to free up resources
curl_close($ch);

// Check if there was a cURL error and output it, otherwise print the HTTP status and response
if ($error) {
    echo "cURL Error: $error\n"; // Output the error message if any
} else {
    echo "HTTP Status: $httpCode\n"; // Output the HTTP status code
    echo "Response:\n$response\n";   // Output the actual response from the API
} 