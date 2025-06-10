<?php
include 'db.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['comment'])) {
    // VULNERABILITY: Stored XSS. Input is not sanitized before being stored.
    $stmt = $conn->prepare("INSERT INTO comments (author, comment) VALUES (?, ?)");
    $stmt->bind_param("ss", $_POST['author'], $_POST['comment']);
    $stmt->execute();
}
?>
<!DOCTYPE html>
<html>
<head><title>Guestbook</title><link rel="stylesheet" type="text/css" href="style.css"></head>
<body>
    <div class="container">
        <h2>Sign the Guestbook</h2>
        <p><a href="index.php">« Back to Home</a></p>
        <form method="post">
            Author: <input type="text" name="author" value="Anonymous"><br>
            Comment: <textarea name="comment" rows="4" cols="50"></textarea><br>
            <input type="submit" value="Sign">
        </form>
        <p><b>XSS Payload Example:</b> <code><script>alert('XSS by ' + document.domain)</script></code></p>
        <hr>
        <h3>Entries</h3>
        <?php
        $result = $conn->query("SELECT author, comment, created_at FROM comments ORDER BY created_at DESC");
        while ($row = $result->fetch_assoc()) {
            // Here we escape for display, but it's too late. The XSS is already in the DB.
            echo "<div class='comment'><strong>" . htmlspecialchars($row['author']) . "</strong> (" . $row['created_at'] . ")<p>" . htmlspecialchars($row['comment']) . "</p></div>";
        }
        ?>
    </div>
</body>
</html>