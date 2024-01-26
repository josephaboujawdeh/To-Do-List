<?php
$servername = "localhost";
$username = "todo";
$password = "test123";
$dbname = "todoDB";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
