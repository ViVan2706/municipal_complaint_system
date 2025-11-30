<?php
session_start();
include '../../db.php';

// Check admin login
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../LoginAndSignup/login.html");
    exit();
}

$complaint_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$action = $_GET['action'] ?? '';

if (!$complaint_id || !$action) {
    header("Location: ../admin_dashboard.php?section=complaints");
    exit();
}

// Determine status
$valid = ['resolved', 'rejected', 'closed'];
if (!in_array($action, $valid)) {
    die("Invalid status");
}

$newStatus = $action;

// Update complaint
if ($newStatus === 'resolved') {
    $update = $conn->prepare("
        UPDATE complaint 
        SET status = ?, resolved_date = CURDATE()
        WHERE complaint_id = ?
    ");
    $update->bind_param("si", $newStatus, $complaint_id);
} else {
    $update = $conn->prepare("
        UPDATE complaint 
        SET status = ?
        WHERE complaint_id = ?
    ");
    $update->bind_param("si", $newStatus, $complaint_id);
}

$update->execute();

// Fetch citizen_id
$get = $conn->prepare("SELECT citizen_id FROM complaint WHERE complaint_id = ?");
$get->bind_param("i", $complaint_id);
$get->execute();
$row = $get->get_result()->fetch_assoc();
$citizen_id = $row['citizen_id'];

// Insert citizen notification
$message = "Status of your complaint #$complaint_id changed to $newStatus.";

$notify = $conn->prepare("
    INSERT INTO citizen_notification (citizen_id, complaint_id, message, date_time, status)
    VALUES (?, ?, ?, NOW(), 'unread')
");
$notify->bind_param("iis", $citizen_id, $complaint_id, $message);
$notify->execute();

// Redirect to complaints list
header("Location: ../admin_dashboard.php?section=complaints");
exit();
