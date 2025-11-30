<?php
session_start();
include '../../db.php'; 

if (!isset($_SESSION['citizen_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['notif_id'])) {
    
    $notif_id = intval($_POST['notif_id']);
    $citizen_id = $_SESSION['citizen_id'];

    $sql = "UPDATE citizen_notification SET status = 'read' WHERE citizen_notification_id = ? AND citizen_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notif_id, $citizen_id);
    
    $stmt->execute();
    $stmt->close();
}

header("Location: Notifications.php");
exit();
?>