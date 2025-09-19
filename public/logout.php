<!-- public/logout.php -->
<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/logout.php
session_start();

// Include required files
require_once '../config/constants.php';

// Store username for display (before destroying session)
$username = $_SESSION['username'] ?? 'User';
$was_logged_in = isset($_SESSION['user_id']);

// Destroy all session data
session_unset();
session_destroy();

// Start a new session for the logout message
session_start();

// Set logout success message
if ($was_logged_in) {
    $_SESSION['logout_success'] = 'You have been successfully logged out.';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Logged Out - BareBonesPHP</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="index.php" style="text-decoration: none; color: inherit;">
                        <h1>BareBonesPHP</h1>
                    </a>
                </div>
                <nav class="nav">
                    <a href="login.php" class="btn btn-outline">Login</a>
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="logout-container">
                <?php if ($was_logged_in): ?>
                    <!-- Successful Logout -->
                    <div class="logout-success">
                        <div class="logout-icon">
                            <div class="checkmark">✓</div>
                        </div>
                        <h2>Successfully Logged Out</h2>
                        <p>Thank you for using BareBonesPHP, <?php echo htmlspecialchars($username); ?>!</p>
                        <p>Your session has been securely terminated.</p>
                    </div>
                <?php else: ?>
                    <!-- Already Logged Out -->
                    <div class="logout-info">
                        <h2>Already Logged Out</h2>
                        <p>You are not currently logged in to any account.</p>
                    </div>
                <?php endif; ?>

                <!-- Action Buttons -->
                <div class="logout-actions">
                    <a href="login.php" class="btn btn-primary">Login Again</a>
                    <a href="index.php" class="btn btn-outline">Go to Homepage</a>
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
                    <a href="index.php">Home</a>
                    <a href="login.php">Login</a>
                    <a href="signup.php">Register</a>
                </nav>
            </div>
        </div>
    </footer>

    <!-- Auto redirect script (optional) -->
    <script>
        // Uncomment below to auto-redirect to homepage after 10 seconds
        // setTimeout(function() {
        //     window.location.href = 'index.php';
        // }, 10000);
    </script>
</body>
</html>
