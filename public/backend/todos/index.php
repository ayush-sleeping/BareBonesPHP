<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/backend/todos/index.php
session_start();

// Include required files
require_once '../../../config/constants.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

// Handle todo actions (toggle complete, delete)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $todo_id = (int)($_POST['todo_id'] ?? 0);

    if ($todo_id > 0) {
        try {
            $db = getDB();

            if ($action === 'toggle') {
                // Toggle completion status
                $stmt = $db->prepare("UPDATE todos SET is_completed = NOT is_completed WHERE id = ? AND user_id = ?");
                $stmt->execute([$todo_id, $_SESSION['user_id']]);
            } elseif ($action === 'delete') {
                // Delete todo
                $stmt = $db->prepare("DELETE FROM todos WHERE id = ? AND user_id = ?");
                $stmt->execute([$todo_id, $_SESSION['user_id']]);
            }

            // Redirect to avoid form resubmission
            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            error_log("Todo Action Error: " . $e->getMessage());
            $error = 'An error occurred. Please try again.';
        }
    }
}

// Get filter parameter
$filter = $_GET['filter'] ?? 'all';
$valid_filters = ['all', 'pending', 'completed'];
if (!in_array($filter, $valid_filters)) {
    $filter = 'all';
}

// Get todos based on filter
try {
    $db = getDB();

    $where_clause = "WHERE user_id = ?";
    $params = [$_SESSION['user_id']];

    if ($filter === 'pending') {
        $where_clause .= " AND is_completed = 0";
    } elseif ($filter === 'completed') {
        $where_clause .= " AND is_completed = 1";
    }

    $stmt = $db->prepare("SELECT id, title, description, is_completed, created_at FROM todos $where_clause ORDER BY created_at DESC");
    $stmt->execute($params);
    $todos = $stmt->fetchAll();

    // Get counts for filter tabs
    $stmt = $db->prepare("SELECT COUNT(*) FROM todos WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $total_count = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM todos WHERE user_id = ? AND is_completed = 0");
    $stmt->execute([$_SESSION['user_id']]);
    $pending_count = $stmt->fetchColumn();

    $stmt = $db->prepare("SELECT COUNT(*) FROM todos WHERE user_id = ? AND is_completed = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $completed_count = $stmt->fetchColumn();

} catch (PDOException $e) {
    error_log("Todos Index Error: " . $e->getMessage());
    $todos = [];
    $total_count = $pending_count = $completed_count = 0;
    $error = 'Unable to load todos. Please try again.';
}
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
                    <span class="user-info">Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?></span>
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

                    <?php if (isset($error)): ?>
                        <div class="error"><?php echo htmlspecialchars($error); ?></div>
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
