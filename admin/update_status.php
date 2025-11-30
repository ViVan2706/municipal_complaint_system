<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$complaint_id = isset($_GET['complaint_id']) ? (int)$_GET['complaint_id'] : 0;
$action = $_GET['action'] ?? "";

if (!$complaint_id || !$action) {
    header("Location: view_complaints.php");
    exit();
}

// Determine new status
$newStatus = "";
if ($action == "resolved") {
    $newStatus = "resolved";
} elseif ($action == "rejected") {
    $newStatus = "rejected";
} elseif ($action == "closed") {
    $newStatus = "closed";
} else {
    die("Invalid action");
}

// Update complaint status
if ($newStatus == "resolved") {
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

// Fetch citizen_id for notification
$get = $conn->prepare("SELECT citizen_id FROM complaint WHERE complaint_id = ?");
$get->bind_param("i", $complaint_id);
$get->execute();
$citizen_id = $get->get_result()->fetch_assoc()['citizen_id'];

// Insert notification for citizen
$message = "Status of your complaint #$complaint_id changed to $newStatus.";

$notify = $conn->prepare("
    INSERT INTO citizen_notification (citizen_id, complaint_id, message, date_time, status)
    VALUES (?, ?, ?, NOW(), 'unread')
");
$notify->bind_param("iis", $citizen_id, $complaint_id, $message);
$notify->execute();

// Redirect back to view page
header("Location: view_complaints.php?view=$complaint_id");
exit();
