<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/test_db.php

require_once 'config/database.php';

try {
    $db = getDB();
    echo "✅ Database connection successful!<br>";
    echo "Connected to database: " . $db->query('SELECT DATABASE()')->fetchColumn() . "<br>";

    // Test if tables exist
    $tables = $db->query("SHOW TABLES")->fetchAll(PDO::FETCH_COLUMN);

    if (in_array('users', $tables) && in_array('todos', $tables)) {
        echo "✅ Required tables (users, todos) exist!<br>";
    } else {
        echo "❌ Tables not found. Please run the database.sql file.<br>";
    }

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
