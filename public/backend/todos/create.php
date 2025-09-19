<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/backend/todos/create.php
session_start();

// Include required files
require_once '../../../config/constants.php';
require_once '../../../config/database.php';
require_once '../../../src/controllers/AuthController.php';
require_once '../../../src/controllers/TodoController.php';

// Initialize controllers
$authController = new AuthController($pdo);
$todoController = new TodoController($pdo);

// Check if user is logged in
$authController->requireAuth();
$currentUser = $authController->getCurrentUser();

// Let the controller handle everything
$pageData = $todoController->handleCreatePage($currentUser['id']);

// Handle redirect if needed
if ($pageData['redirect']) {
    header('Location: ' . $pageData['redirect']);
    exit;
}

// Extract data for the view
$error = $pageData['error'];
$title = $pageData['form_data']['title'];
$description = $pageData['form_data']['description'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Todo - BareBonesPHP</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="../../index.php" style="text-decoration: none; color: inherit;">
                        <h1>BareBonesPHP</h1>
                    </a>
                </div>
                <nav class="nav">
                    <span class="user-info">Welcome, <?php echo htmlspecialchars($currentUser['username']); ?></span>
                    <a href="../../logout.php" class="btn btn-primary">Logout</a>
                </nav>
            </div>
        </div>
    </header>

    <!-- Main Content with Sidebar -->
    <main class="main dashboard-main">
        <div class="container">
            <div class="dashboard-layout">
                <!-- Sidebar (20%) -->
                <aside class="sidebar">
                    <nav class="sidebar-nav">
                        <a href="../dashboard.php" class="sidebar-link">
                            Dashboard
                        </a>
                        <a href="index.php" class="sidebar-link active">
                            My Todos
                        </a>
                    </nav>
                </aside>

                <!-- Main Content Area (80%) -->
                <div class="dashboard-content">
                    <!-- Page Header -->
                    <div class="dashboard-header">
                        <div>
                            <h2>Create New Todo</h2>
                            <p>Add a new task to your todo list</p>
                        </div>
                        <a href="index.php" class="btn btn-outline">Back to Todos</a>
                    </div>

                    <!-- Error/Success Messages -->
                    <?php if ($error): ?>
                        <div class="error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <!-- Create Todo Form -->
                    <div class="form-section">
                        <form method="POST" action="" class="todo-form">
                            <div class="form-group">
                                <label for="title">Todo Title *</label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    value="<?php echo htmlspecialchars($title ?? ''); ?>"
                                    placeholder="Enter todo title"
                                    maxlength="255"
                                    required
                                >
                                <small class="form-help">Maximum 255 characters</small>
                            </div>

                            <div class="form-group">
                                <label for="description">Description (Optional)</label>
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="6"
                                    placeholder="Enter todo description (optional)"
                                    maxlength="1000"
                                ><?php echo htmlspecialchars($description ?? ''); ?></textarea>
                                <small class="form-help">Maximum 1000 characters</small>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Create Todo</button>
                                <a href="index.php" class="btn btn-outline">Cancel</a>
                            </div>
                        </form>
                    </div>
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
                    <a href="../dashboard.php">Dashboard</a>
                    <a href="index.php">Todos</a>
                    <a href="../../logout.php">Logout</a>
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>
