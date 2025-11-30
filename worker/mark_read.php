<?php
session_start();

include '../db.php'; 


if (!isset($_SESSION['worker_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'])) {
    
    $notification_id = $_POST['notif_id'];
    $worker_id = $_SESSION['worker_id'];


    $sql = "UPDATE worker_notification 
            SET status = 'read' 
            WHERE worker_notification_id = ? AND worker_id = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $notification_id, $worker_id);
        
        if ($stmt->execute()) {

        } else {

        }
        $stmt->close();
    }
}

if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {

    header("Location: Notifications.php"); 
}
exit();
?>