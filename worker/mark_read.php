<?php
session_start();
include '../db.php'; 

if (!isset($_SESSION['worker_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notif_id'])) {
    
    $notif_id = intval($_POST['notif_id']);
    $worker_id = $_SESSION['worker_id'];

    $sql = "UPDATE worker_notification SET status = 'read' WHERE worker_notification_id = ? AND worker_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notif_id, $worker_id);
    
    $stmt->execute();
    $stmt->close();
}

header("Location: worker_dashboard.php?section=notifications");
exit();
?>