<?php
// Database connection settings
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'municipal_complaint_system';

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}

// Important: Maintain UTF8 Encoding
$conn->set_charset("utf8mb4");
?>
