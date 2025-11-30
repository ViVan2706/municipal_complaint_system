<?php
session_start();
include '../db.php';
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['worker_id'])) {
    $worker_id = $_POST['worker_id'];
    $complaint_id = $_POST['complaint_id'];
    $admin_id = $_SESSION['admin_id'];
    $sql = "UPDATE complaint SET worker_id = ?, admin_id = ?, status = 'in_progress' WHERE complaint_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iii", $worker_id, $admin_id, $complaint_id);
    if ($stmt->execute()) {
        header("Location: AllComplaints.php?msg=assigned");
    } else {
        echo "Error assigning worker.";
    }
    $stmt->close();
}
?>