<?php
include 'db.php';
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$message = '';
// VULNERABILITY: CSRF. No anti-CSRF token check.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_user'])) {
    $newUser = $_POST['new_user'];
    $newPass = $_POST['new_pass'];
    $stmt = $conn->prepare("INSERT INTO users (username, password, is_admin) VALUES (?, ?, TRUE)");
    $stmt->bind_param("ss", $newUser, $newPass);
    if ($stmt->execute()) {
        $message = "Successfully created admin user: " . htmlspecialchars($newUser);
    } else {
        $message = "Error creating user.";
    }
}
?>
<!DOCTYPE html>
<html>
<head><title>Admin Panel</title><link rel="stylesheet" type="text/css" href="style.css"></head>
<body>
    <div class="container">
        <h2>Admin Panel</h2>
        <p><a href="index.php">« Back to Home</a> | <a href="logout.php">Logout</a></p>
        <?php if($message) echo "<p class='message'>$message</p>"; ?>

        <div class="vuln-section">
            <h3>Create New Admin User (CSRF Vulnerability)</h3>
            <form method="post">
                New Username: <input type="text" name="new_user"><br>
                New Password: <input type="text" name="new_pass"><br>
                <input type="submit" value="Create Admin">
            </form>
        </div>

        <div class="vuln-section">
            <h3>Guestbook Comments (Stored XSS Trigger)</h3>
            <?php
            $result = $conn->query("SELECT author, comment, created_at FROM comments ORDER BY created_at DESC");
            while ($row = $result->fetch_assoc()) {
                // VULNERABILITY: Stored XSS. Comment is output without sanitization.
                echo "<div class='comment'><strong>" . htmlspecialchars($row['author']) . "</strong> says:<p>" . $row['comment'] . "</p></div>";
            }
            ?>
        </div>
    </div>
</body>
</html>