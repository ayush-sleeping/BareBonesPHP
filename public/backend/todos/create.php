<?php
// filepath: /Applications/XAMPP/xamppfiles/htdocs/BareBonesPHP/public/backend/todos/create.php
session_start();

// Include required files
require_once '../../../config/constants.php';
require_once '../../../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Validation
    if (empty($title)) {
        $error = 'Title is required.';
    } elseif (strlen($title) > 255) {
        $error = 'Title must be less than 255 characters.';
    } elseif (strlen($description) > 1000) {
        $error = 'Description must be less than 1000 characters.';
    } else {
        try {
            $db = getDB();

            // Insert new todo
            $stmt = $db->prepare("INSERT INTO todos (user_id, title, description) VALUES (?, ?, ?)");
            $stmt->execute([$_SESSION['user_id'], $title, $description]);

            // Redirect to todos index with success message
            $_SESSION['todo_success'] = 'Todo created successfully!';
            header('Location: index.php');
            exit;

        } catch (PDOException $e) {
            error_log("Todo Create Error: " . $e->getMessage());
            $error = 'An error occurred while creating the todo. Please try again.';
        }
    }
}
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
