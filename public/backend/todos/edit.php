<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/backend/todos/edit.php
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

$todo_id = (int)($_GET['id'] ?? 0);

// Redirect if no valid ID provided
if ($todo_id <= 0) {
    header('Location: index.php');
    exit;
}

// Let the controller handle everything
$pageData = $todoController->handleEditPage($currentUser['id'], $todo_id);

// Handle redirect if needed
if ($pageData['redirect']) {
    header('Location: ' . $pageData['redirect']);
    exit;
}

// Extract data for the view
$error = $pageData['error'];
$todo = $pageData['todo'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Todo - BareBonesPHP</title>
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
                            <h2>Edit Todo</h2>
                            <p>Update your todo item</p>
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

                    <!-- Edit Todo Form -->
                    <div class="form-section">
                        <form method="POST" action="" class="todo-form">
                            <div class="form-group">
                                <label for="title">Todo Title *</label>
                                <input
                                    type="text"
                                    id="title"
                                    name="title"
                                    value="<?php echo htmlspecialchars($todo['title']); ?>"
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
                                ><?php echo htmlspecialchars($todo['description']); ?></textarea>
                                <small class="form-help">Maximum 1000 characters</small>
                            </div>

                            <div class="form-group">
                                <div class="checkbox-group">
                                    <label class="checkbox-label">
                                        <input
                                            type="checkbox"
                                            name="is_completed"
                                            value="1"
                                            <?php echo $todo['is_completed'] ? 'checked' : ''; ?>
                                        >
                                        <span class="checkbox-text">Mark as completed</span>
                                    </label>
                                </div>
                            </div>

                            <div class="form-actions">
                                <button type="submit" class="btn btn-primary">Update Todo</button>
                                <a href="index.php" class="btn btn-outline">Cancel</a>
                            </div>
                        </form>
                    </div>

                    <!-- Todo Info -->
                    <div class="info-section">
                        <h3>Todo Information</h3>
                        <div class="info-grid">
                            <div class="info-item">
                                <span class="info-label">Status:</span>
                                <span class="info-value">
                                    <span class="status-badge <?php echo $todo['is_completed'] ? 'completed' : 'pending'; ?>">
                                        <?php echo $todo['is_completed'] ? 'Completed' : 'Pending'; ?>
                                    </span>
                                </span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Todo ID:</span>
                                <span class="info-value">#<?php echo $todo['id']; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="quick-actions-section">
                        <h3>Quick Actions</h3>
                        <div class="action-buttons">
                            <form method="POST" action="index.php" style="display: inline;">
                                <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
                                <input type="hidden" name="action" value="toggle">
                                <button type="submit" class="btn btn-outline">
                                    <?php echo $todo['is_completed'] ? 'Mark as Pending' : 'Mark as Complete'; ?>
                                </button>
                            </form>

                            <form method="POST" action="index.php" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this todo?')">
                                <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
                                <input type="hidden" name="action" value="delete">
                                <button type="submit" class="btn btn-delete">Delete Todo</button>
                            </form>
                        </div>
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
