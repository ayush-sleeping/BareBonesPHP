<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/signup.php
session_start();

// Include required files
require_once '../config/constants.php';
require_once '../config/database.php';
require_once '../src/controllers/AuthController.php';

// Initialize AuthController
$db = getDB();
$authController = new AuthController($db);

// Redirect if already logged in
$authController->redirectIfLoggedIn('backend/dashboard.php');

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    // Use AuthController to handle registration
    $result = $authController->register($username, $email, $password, $confirm_password);

    if ($result['success']) {
        // Success - redirect to login page
        $authController->setFlashMessage('signup_success', $result['message']);
        header('Location: login.php');
        exit;
    } else {
        // Show validation errors
        $error = implode('<br>', $result['errors']);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - BareBonesPHP</title>
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
                    <a href="index.php" class="btn btn-outline">Home</a>
                    <a href="login.php" class="btn btn-primary">Login</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>Create Your Account</h2>
                    <p>Join BareBonesPHP to manage your todos</p>
                </div>

                <?php if ($error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo htmlspecialchars($username ?? ''); ?>"
                            placeholder="Choose a username (3-50 characters)"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">Email Address</label>
                        <input
                            type="email"
                            id="email"
                            name="email"
                            value="<?php echo htmlspecialchars($email ?? ''); ?>"
                            placeholder="Enter your email address"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter a secure password (min <?php echo PASSWORD_MIN_LENGTH; ?> characters)"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input
                            type="password"
                            id="confirm_password"
                            name="confirm_password"
                            placeholder="Re-enter your password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Create Account</button>
                </form>

                <div class="form-footer">
                    <p>Already have an account? <a href="login.php">Login here</a></p>
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
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>
