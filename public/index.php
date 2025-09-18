<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/index.php
session_start();

// Include helper functions (if they exist)
if (file_exists('../src/helpers/functions.php')) {
    require_once '../src/helpers/functions.php';
}
if (file_exists('../config/constants.php')) {
    require_once '../config/constants.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BareBonesPHP - Core PHP Learning</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <h1>BareBonesPHP</h1>
                </div>
                <nav class="nav">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User'); ?></span>
                        <a href="backend/dashboard.php" class="btn btn-outline">Dashboard</a>
                        <a href="logout.php" class="btn btn-primary">Logout</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-outline">Login</a>
                        <a href="signup.php" class="btn btn-primary">Sign Up</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="hero">
                <h2>Core PHP Authentication & CRUD System</h2>
                <p>Built from scratch to understand the fundamentals beneath Laravel frameworks</p>
            </div>

            <div class="cards-grid">
                <div class="card">
                    <h3>Project Purpose</h3>
                    <ul>
                        <li>Understand underlying mechanics that Laravel abstracts away</li>
                        <li>Learn how requests and responses work at HTTP level</li>
                        <li>Master session and cookie management without framework magic</li>
                        <li>Write raw SQL queries without Eloquent ORM</li>
                        <li>Implement manual routing without Laravel's router</li>
                        <li>Build security features without middleware abstractions</li>
                    </ul>
                </div>

                <div class="card">
                    <h3>Core Features</h3>
                    <ul>
                        <li>User Registration with validation</li>
                        <li>User Login with password hashing</li>
                        <li>Session management and authentication</li>
                        <li>Protected dashboard requiring login</li>
                        <li>CRUD operations on Todo items</li>
                        <li>Create, Read, Update, Delete functionality</li>
                    </ul>
                </div>

                <div class="card">
                    <h3>Learning Outcomes</h3>
                    <ul>
                        <li>Request/Response cycle without framework routing</li>
                        <li>Session management at native PHP level</li>
                        <li>Raw database interactions using PDO</li>
                        <li>Manual password hashing and validation</li>
                        <li>SQL injection prevention techniques</li>
                        <li>MVC pattern implementation from scratch</li>
                    </ul>
                </div>

                <div class="card">
                    <h3>Tech Stack</h3>
                    <ul>
                        <li>Backend: Pure PHP 8+</li>
                        <li>Database: MySQL with raw SQL queries</li>
                        <li>Frontend: Basic HTML/CSS</li>
                        <li>Server: Apache (XAMPP)</li>
                        <li>No frameworks or ORMs</li>
                        <li>No external dependencies</li>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-info">
                    <h4>BareBonesPHP</h4>
                    <p>Core PHP project for understanding Laravel fundamentals</p>
                </div>
                <nav class="footer-nav">
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="backend/dashboard.php">Dashboard</a>
                        <a href="todos/">My Todos</a>
                    <?php endif; ?>
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>
