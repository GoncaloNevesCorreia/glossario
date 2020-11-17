<?php
$servername = "localhost";
$username = "root";
$password = "";
$db = "glossario";

// Create connection
$conn = new mysqli($servername, $username, $password, $db);
$conn->set_charset('utf8mb4');

// Check connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

?>