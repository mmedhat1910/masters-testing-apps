<?php include 'db.php'; ?>
<!DOCTYPE html>
<html>
<head>
    <title>Project 2</title>
    <link rel="stylesheet" type="text/css" href="style.css">
</head>
<body>
    <div class="container">
        <h1>Project 2</h1>
        <?php if (isset($_SESSION['username'])): ?>
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>! You are logged in.</p>
            <a href="admin.php">Admin Panel</a> |
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <p>You are not logged in. <a href="login.php">Login as admin</a></p>
        <?php endif; ?>
        <hr>
        <h2>Public Links</h2>
        <ul>
            <li><a href="guestbook.php">Sign the Guestbook</a></li>
            <li><a href="network.php">Network Ping Tool</a></li>
        </ul>
    </div>
</body>
</html>