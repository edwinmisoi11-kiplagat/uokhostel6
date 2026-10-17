<?php
// Update these values to match your local MySQL setup.
$host = 'localhost';
$dbname = 'hostel6_portal';
$username = 'root';
$password = ''; // Set your MySQL password if one is configured.

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    $pdo = new PDO(
        "mysql:host={$host};dbname={$dbname};charset=utf8mb4",
        $username,
        $password,
        $options
    );
} catch (PDOException $e) {
    error_log('Hostel 6 database connection failed: ' . $e->getMessage());
    http_response_code(500);
    exit('The application service is temporarily unavailable. Please try again later.');
}
