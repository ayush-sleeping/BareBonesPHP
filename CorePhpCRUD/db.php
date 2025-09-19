<?php
$host = 'localhost';
$username = 'root';
$password = '';
$database = 'corephp_db';

// Create connection
$conn = new mysqli($host, $username, $password);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if it doesn't exist
$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);

// Create users table if it doesn't exist
$create_table = "CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if (!$conn->query($create_table)) {
    echo "Error creating table: " . $conn->error;
}
?>
