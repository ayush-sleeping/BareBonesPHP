<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/backend/todos/index.php
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
$pageData = $todoController->handleIndexPage($currentUser['id']);

// Handle redirect if needed
if ($pageData['redirect']) {
    header('Location: ' . $pageData['redirect']);
    exit;
}

// Extract data for the view
$error = $pageData['error'];
$success = $pageData['success_message'];
$todos = $pageData['todos'];
$counts = $pageData['counts'];
$filter = $pageData['filter'];
$total_count = $counts['all'] ?? 0;
$pending_count = $counts['pending'] ?? 0;
$completed_count = $counts['completed'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Todos - BareBonesPHP</title>
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
                        <a href="../dashboard.php?page=dashboard" class="sidebar-link">
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
                            <h2>My Todos</h2>
                            <p>Manage your todo items</p>
                        </div>
                        <a href="create.php" class="btn btn-primary">Add New Todo</a>
                    </div>

                    <?php if ($error): ?>
                        <div class="error"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="success"><?php echo htmlspecialchars($success); ?></div>
                    <?php endif; ?>

                    <!-- Filter Tabs -->
                    <div class="filter-tabs">
                        <a href="index.php?filter=all" class="filter-tab <?php echo $filter === 'all' ? 'active' : ''; ?>">
                            All (<?php echo $total_count; ?>)
                        </a>
                        <a href="index.php?filter=pending" class="filter-tab <?php echo $filter === 'pending' ? 'active' : ''; ?>">
                            Pending (<?php echo $pending_count; ?>)
                        </a>
                        <a href="index.php?filter=completed" class="filter-tab <?php echo $filter === 'completed' ? 'active' : ''; ?>">
                            Completed (<?php echo $completed_count; ?>)
                        </a>
                    </div>

                    <!-- Todos List -->
                    <?php if (empty($todos)): ?>
                        <div class="empty-state">
                            <p>
                                <?php if ($filter === 'all'): ?>
                                    No todos found. Create your first todo to get started!
                                <?php elseif ($filter === 'pending'): ?>
                                    No pending todos. Great job staying on top of things!
                                <?php else: ?>
                                    No completed todos yet. Complete some tasks to see them here.
                                <?php endif; ?>
                            </p>
                            <?php if ($filter === 'all'): ?>
                                <a href="create.php" class="btn btn-primary">Create Your First Todo</a>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="todos-container">
                            <?php foreach ($todos as $todo): ?>
                                <div class="todo-card <?php echo $todo['is_completed'] ? 'completed' : ''; ?>">
                                    <div class="todo-main">
                                        <div class="todo-content">
                                            <h4><?php echo htmlspecialchars($todo['title']); ?></h4>
                                            <?php if ($todo['description']): ?>
                                                <p><?php echo htmlspecialchars($todo['description']); ?></p>
                                            <?php endif; ?>
                                            <small>Created: <?php echo date('M j, Y g:i A', strtotime($todo['created_at'])); ?></small>
                                        </div>
                                        <div class="todo-status">
                                            <span class="status-badge <?php echo $todo['is_completed'] ? 'completed' : 'pending'; ?>">
                                                <?php echo $todo['is_completed'] ? 'Completed' : 'Pending'; ?>
                                            </span>
                                        </div>
                                    </div>

                                    <div class="todo-actions">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
                                            <input type="hidden" name="action" value="toggle">
                                            <button type="submit" class="btn-action btn-toggle">
                                                <?php echo $todo['is_completed'] ? 'Mark Pending' : 'Mark Complete'; ?>
                                            </button>
                                        </form>

                                        <a href="edit.php?id=<?php echo $todo['id']; ?>" class="btn-action btn-edit">
                                            Edit
                                        </a>

                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this todo?')">
                                            <input type="hidden" name="todo_id" value="<?php echo $todo['id']; ?>">
                                            <input type="hidden" name="action" value="delete">
                                            <button type="submit" class="btn-action btn-delete">
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
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
