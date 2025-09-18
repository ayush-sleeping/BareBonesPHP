<!-- public/login.php -->

<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/login.php
session_start();

// Include required files
require_once '../config/constants.php';
require_once '../config/database.php';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: backend/dashboard.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Basic validation
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            $db = getDB();

            // Find user by username or email
            $stmt = $db->prepare("SELECT id, username, email, password FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];

                // Redirect to dashboard
                header('Location: backend/dashboard.php');
                exit;
            } else {
                $error = 'Invalid username/email or password.';
            }

        } catch (PDOException $e) {
            error_log("Login Error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
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
