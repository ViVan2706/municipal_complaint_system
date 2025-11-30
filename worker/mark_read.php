<?php
session_start();

// 1. Database connection file include karein
// (Path check kar lena, agar db.php 2 folder peeche hai to '../../db.php' sahi hai)
include '../db.php'; 

// 2. Check karein ki Worker login hai ya nahi
if (!isset($_SESSION['worker_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

// 3. Form submit hua hai ya nahi check karein
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['notif_id'])) {
    
    $notification_id = $_POST['notif_id'];
    $worker_id = $_SESSION['worker_id'];

    // 4. Update Query: Status ko 'unread' se 'read' mein badalna
    // Dhyan de: Hum worker_id bhi check kar rahe hain taaki koi aur worker kisi aur ka status change na kar sake
    $sql = "UPDATE worker_notification 
            SET status = 'read' 
            WHERE worker_notification_id = ? AND worker_id = ?";

    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param("ii", $notification_id, $worker_id);
        
        if ($stmt->execute()) {
            // Success
        } else {
            // Error handling (Optional)
        }
        $stmt->close();
    }
}

// 5. Wapas pichle page par bhej do (Refresh page)
if (isset($_SERVER['HTTP_REFERER'])) {
    header("Location: " . $_SERVER['HTTP_REFERER']);
} else {
    // Agar referer nahi mila to direct file par bhej do
    header("Location: Notifications.php"); 
}
exit();
?>