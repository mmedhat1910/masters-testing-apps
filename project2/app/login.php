<?php
include 'db.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stmt = $conn->prepare("SELECT username FROM users WHERE username = ? AND password = ?");
    $stmt->bind_param("ss", $_POST['username'], $_POST['password']);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($user = $result->fetch_assoc()) {
        $_SESSION['username'] = $user['username'];
        header("Location: admin.php");
        exit();
    } else {
        $error = "Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Login</title><link rel="stylesheet" type="text/css" href="style.css"></head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form method="post">
            Username: <input type="text" name="username" value="admin"><br>
            Password: <input type="password" name="password" value="password"><br>
            <input type="submit" value="Login">
        </form>
        <?php if ($error) echo "<p class='error'>$error</p>"; ?>
    </div>
</body>
</html>