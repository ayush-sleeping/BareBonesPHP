<?php
include 'db.php';

// Handle DELETE operation via POST
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $id = $_POST['delete_id'];
    $sql = "DELETE FROM users WHERE id=$id";
    if ($conn->query($sql)) {
        header("Location: index.php?message=Record deleted successfully");
        exit();
    } else {
        $error = "Error deleting record: " . $conn->error;
    }
}

// READ operation - fetch all users
$users = $conn->query("SELECT * FROM users ORDER BY id DESC");

// Display success message
$message = '';
if (isset($_GET['message'])) {
    $message = $_GET['message'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP CRUD - Home</title>
</head>
<body>
    <h1>User Management System</h1>

    <?php if ($message): ?>
        <p style="color: green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <p>
        <a href="create.php">
            <button>Add New User</button>
        </a>
    </p>

    <hr>

    <!-- READ - DISPLAY USERS -->
    <h2>All Users</h2>
    <?php if ($users->num_rows > 0): ?>
        <table border="1">
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Created At</th>
                <th>Actions</th>
            </tr>
            <?php while ($row = $users->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td><?php echo $row['phone']; ?></td>
                    <td><?php echo $row['created_at']; ?></td>
                    <td>
                        <a href="edit.php?id=<?php echo $row['id']; ?>">
                            <button>Edit</button>
                        </a>

                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this user?')">
                            <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                            <button type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No users found. <a href="create.php">Add some users</a></p>
    <?php endif; ?>

</body>
</html>
