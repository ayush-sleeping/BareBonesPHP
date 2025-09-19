<?php
include 'db.php';

// Get user ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: index.php?message=Invalid user ID");
    exit();
}

$id = $_GET['id'];

// Fetch user data
$result = $conn->query("SELECT * FROM users WHERE id = $id");
if ($result->num_rows == 0) {
    header("Location: index.php?message=User not found");
    exit();
}

$user = $result->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Basic validation
    if (!empty($name) && !empty($email)) {
        $sql = "UPDATE users SET name='$name', email='$email', phone='$phone' WHERE id=$id";

        if ($conn->query($sql)) {
            header("Location: index.php?message=User updated successfully");
            exit();
        } else {
            $error = "Error: " . $conn->error;
        }
    } else {
        $error = "Name and Email are required fields";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
</head>
<body>
    <h1>Edit User</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label>Name:</label><br>
            <input type="text" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : $user['name']; ?>" required>
        </p>

        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : $user['email']; ?>" required>
        </p>

        <p>
            <label>Phone:</label><br>
            <input type="text" name="phone" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : $user['phone']; ?>">
        </p>

        <p>
            <button type="submit">Update User</button>
            <a href="index.php">
                <button type="button">Cancel</button>
            </a>
        </p>
    </form>

</body>
</html>
