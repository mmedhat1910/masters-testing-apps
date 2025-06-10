<!DOCTYPE html>
<html>
<head><title>Network Tool</title><link rel="stylesheet" type="text/css" href="style.css"></head>
<body>
    <div class="container">
        <h2>Network Ping Tool</h2>
        <p><a href="index.php">« Back to Home</a></p>
        <form method="get">
            Enter an IP to ping: <input type="text" name="ip" value="127.0.0.1">
            <input type="submit" value="Ping">
        </form>
        <p><b>Command Injection Payload:</b> <code>127.0.0.1; id</code> or <code>127.0.0.1; ls -la</code></p>
        <hr>
        <pre>
        <?php
        if (isset($_GET['ip'])) {
            $ip = $_GET['ip'];
            // VULNERABILITY: Command Injection. User input is directly passed to shell_exec.
            echo "Pinging " . htmlspecialchars($ip) . "...\n";
            echo shell_exec('ping -c 3 ' . $ip);
        }
        ?>
        </pre>
    </div>
</body>
</html>