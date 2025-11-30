<?php
session_start();
include '../db.php';
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['notif_id'])) {
    $notif_id = intval($_POST['notif_id']);
    $admin_id = $_SESSION['admin_id'];
    $sql = "UPDATE admin_notification SET status='read' WHERE admin_notification_id=? AND admin_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $notif_id, $admin_id);
    $stmt->execute();
    $stmt->close();
}
header("Location: Notifications.php");
exit();
