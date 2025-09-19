<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/login.php
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

// Check for flash messages (like signup success)
$success = $authController->getFlashMessage('signup_success');
$success = $success ?: $authController->getFlashMessage('logout_success');

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Use AuthController to handle login
    $result = $authController->login($username, $password);

    if ($result['success']) {
        // Login successful - redirect to dashboard
        header('Location: backend/dashboard.php');
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
    <title>Login - BareBonesPHP</title>
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
                    <a href="signup.php" class="btn btn-primary">Sign Up</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="main">
        <div class="container">
            <div class="form-container">
                <div class="form-header">
                    <h2>Login to Your Account</h2>
                    <p>Enter your credentials to access your todos</p>
                </div>

                <?php if ($error): ?>
                    <div class="error"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>

                <?php if ($success): ?>
                    <div class="success"><?php echo htmlspecialchars($success); ?></div>
                <?php endif; ?>

                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username or Email</label>
                        <input
                            type="text"
                            id="username"
                            name="username"
                            value="<?php echo htmlspecialchars($username ?? ''); ?>"
                            placeholder="Enter your username or email"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="password">Password</label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            placeholder="Enter your password"
                            required
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-full">Login</button>
                </form>

                <div class="form-footer">
                    <p>Don't have an account? <a href="signup.php">Sign up here</a></p>
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
                    <a href="signup.php">Register</a>
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>
