<?php
session_start();
$db_host = 'db'; // The service name from docker-compose
$db_user = 'user';
$db_pass = 'password';
$db_name = 'project2_db';

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>