<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/config/database.php
// Database configuration
$host = 'localhost';
$dbname = 'barebonesphp';
$username = 'root';
$password = '';

// PDO options for better security and error handling
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
];

try {
    // Create PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
    // Set charset to utf8mb4 for full UTF-8 support
    $pdo->exec("SET NAMES utf8mb4");

} catch (PDOException $e) {
    // Log the error (in production, don't show database errors to users)
    error_log("Database Connection Error: " . $e->getMessage());
    // Show user-friendly error (in development)
    die("Database connection failed. Please check your configuration.");
}

// Function to get database connection
function getDB() {
    global $pdo;
    return $pdo;
}
