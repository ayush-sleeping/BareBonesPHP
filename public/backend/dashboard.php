<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/backend/dashboard.php
session_start();

// Include required files - Fix paths for backend folder
require_once '../../config/constants.php';
require_once '../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit;
}

// Get user's todo statistics
try {
    $db = getDB();

    // Get total todos
    $stmt = $db->prepare("SELECT COUNT(*) as total FROM todos WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total_todos = $stmt->fetchColumn();

    // Get completed todos
    $stmt = $db->prepare("SELECT COUNT(*) as completed FROM todos WHERE user_id = ? AND is_completed = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $completed_todos = $stmt->fetchColumn();

    // Get pending todos
    $pending_todos = $total_todos - $completed_todos;

    // Get recent todos (last 5)
    $stmt = $db->prepare("SELECT id, title, is_completed, created_at FROM todos WHERE user_id = ? ORDER BY created_at DESC LIMIT 5");
    $stmt->execute([$_SESSION['user_id']]);
    $recent_todos = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    $total_todos = $completed_todos = $pending_todos = 0;
    $recent_todos = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - BareBonesPHP</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="container">
            <div class="header-content">
                <div class="logo">
                    <a href="../index.php" style="text-decoration: none; color: inherit;">
                        <h1>BareBonesPHP</h1>
                    </a>
                </div>
                <nav class="nav">
                    <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    <a href="../logout.php" class="btn btn-primary">Logout</a>
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
                        <a href="dashboard.php" class="sidebar-link active">
                            Dashboard
                        </a>
                        <a href="todos/index.php" class="sidebar-link">
                            My Todos
                        </a>
                    </nav>
                </aside>

                <!-- Main Content Area (80%) -->
                <div class="dashboard-content">
                    <!-- Dashboard Overview -->
                    <div class="dashboard-header">
                        <div>
                            <h2>Dashboard Overview</h2>
                            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! Here's your todo summary.</p>
                        </div>
                    </div>

                    <!-- Stats Cards -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $total_todos; ?></div>
                            <div class="stat-label">Total Todos</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $completed_todos; ?></div>
                            <div class="stat-label">Completed</div>
                        </div>
                        <div class="stat-card">
                            <div class="stat-number"><?php echo $pending_todos; ?></div>
                            <div class="stat-label">Pending</div>
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h3>Quick Actions</h3>
                        </div>
                        <div class="quick-actions">
                            <a href="todos/create.php" class="action-card">
                                <h4>Create New Todo</h4>
                                <p>Add a new task to your todo list</p>
                            </a>
                            <a href="todos/index.php" class="action-card">
                                <h4>View All Todos</h4>
                                <p>Manage and organize your tasks</p>
                            </a>
                        </div>
                    </div>

                    <!-- Recent Todos -->
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h3>Recent Todos</h3>
                            <a href="todos/index.php" class="btn btn-outline">View All</a>
                        </div>

                        <?php if (empty($recent_todos)): ?>
                            <div class="empty-state">
                                <p>No todos yet. Create your first todo to get started!</p>
                                <a href="todos/create.php" class="btn btn-primary">Create Todo</a>
                            </div>
                        <?php else: ?>
                            <div class="todos-list">
                                <?php foreach ($recent_todos as $todo): ?>
                                    <div class="todo-item <?php echo $todo['is_completed'] ? 'completed' : ''; ?>">
                                        <div class="todo-content">
                                            <h4><?php echo htmlspecialchars($todo['title']); ?></h4>
                                            <small><?php echo date('M j, Y g:i A', strtotime($todo['created_at'])); ?></small>
                                        </div>
                                        <div class="todo-status">
                                            <?php if ($todo['is_completed']): ?>
                                                <span class="status-badge completed">Completed</span>
                                            <?php else: ?>
                                                <span class="status-badge pending">Pending</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- User Info -->
                    <div class="dashboard-section">
                        <div class="section-header">
                            <h3>Account Information</h3>
                        </div>
                        <div class="user-info-card">
                            <div class="info-item">
                                <span class="info-label">Username:</span>
                                <span class="info-value"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Email:</span>
                                <span class="info-value"><?php echo htmlspecialchars($_SESSION['email']); ?></span>
                            </div>
                            <div class="info-item">
                                <span class="info-label">Total Todos:</span>
                                <span class="info-value"><?php echo $total_todos; ?></span>
                            </div>
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
                    <a href="dashboard.php">Dashboard</a>
                    <a href="todos/index.php">Todos</a>
                    <a href="../logout.php">Logout</a>
                </nav>
            </div>
        </div>
    </footer>
</body>
</html>
