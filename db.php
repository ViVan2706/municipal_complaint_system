<?php
$servername = $_ENV['DB_HOST'] ?? 'localhost'; 
$username = $_ENV['DB_USERNAME'] ?? 'root';    
$password = $_ENV['DB_PASSWORD'] ?? ''; 
$dbname = $_ENV['DB_NAME'] ?? 'complaint_system';
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    error_log("Connection Failed: " . $conn->connect_error);
    die("Database connection failed. Please try again later.");
}
?>