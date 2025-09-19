<?php
include 'db.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Basic validation
    if (!empty($name) && !empty($email)) {
        $sql = "INSERT INTO users (name, email, phone) VALUES ('$name', '$email', '$phone')";

        if ($conn->query($sql)) {
            header("Location: index.php?message=User created successfully");
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
    <title>Create New User</title>
</head>
<body>
    <h1>Add New User</h1>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <form method="POST">
        <p>
            <label>Name:</label><br>
            <input type="text" name="name" value="<?php echo isset($_POST['name']) ? $_POST['name'] : ''; ?>" required>
        </p>

        <p>
            <label>Email:</label><br>
            <input type="email" name="email" value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" required>
        </p>

        <p>
            <label>Phone:</label><br>
            <input type="text" name="phone" value="<?php echo isset($_POST['phone']) ? $_POST['phone'] : ''; ?>">
        </p>

        <p>
            <button type="submit">Create User</button>
            <a href="index.php">
                <button type="button">Cancel</button>
            </a>
        </p>
    </form>

</body>
</html>
