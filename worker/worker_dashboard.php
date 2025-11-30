<?php
// START SESSION
session_start();
include '../db.php'; 

if (!isset($_SESSION['worker_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$worker_name = "Worker";
$notification_count = 0;

// Fetch worker's name
$sql_worker = "SELECT name FROM worker WHERE worker_id = ?";
$stmt_worker = mysqli_prepare($conn, $sql_worker);
mysqli_stmt_bind_param($stmt_worker, "i", $worker_id);
mysqli_stmt_execute($stmt_worker);
$result_worker = mysqli_stmt_get_result($stmt_worker);
if ($row_worker = mysqli_fetch_assoc($result_worker)) {
    $worker_name = explode(' ', $row_worker['name'])[0]; 
}
mysqli_stmt_close($stmt_worker);

// Fetch unread notification count
$sql_count = "SELECT COUNT(*) AS unread_count FROM worker_notification WHERE worker_id = ? AND status = 'unread'";
$stmt_count = mysqli_prepare($conn, $sql_count);
mysqli_stmt_bind_param($stmt_count, "i", $worker_id);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row_count = mysqli_fetch_assoc($result_count);
$notification_count = $row_count['unread_count'];
mysqli_stmt_close($stmt_count);

$section = $_GET['section'] ?? 'dashboard';
$active_assigned = ($section == 'assigned') ? 'active' : '';
$active_completed = ($section == 'completed') ? 'active' : '';
$active_notifications = ($section == 'notifications') ? 'active' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCCCTS - Worker Dashboard</title>
    <link rel="stylesheet" href="style.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

    <header class="header">
        <a href="worker_dashboard.php" class="logo">
            <i class="fas fa-hard-hat"></i> MCCCTS - Worker
        </a>
        <nav class="nav-links">
            <a href="?section=assigned" class="nav-link <?php echo $active_assigned; ?>">
                <i class="fas fa-clipboard-list"></i> Assigned
            </a>
            <a href="?section=completed" class="nav-link <?php echo $active_completed; ?>">
                <i class="fas fa-check-circle"></i> Completed
            </a>
            <a href="?section=notifications" class="nav-link <?php echo $active_notifications; ?>">
                <i class="fas fa-bell"></i> Notifications
                <?php if ($notification_count > 0): ?>
                    <span class="notifications-badge"><?php echo $notification_count; ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php" class="nav-link">
                <i class="fas fa-user-circle"></i> Profile
            </a>
        </nav>
    </header>

    <main class="container">
        <?php
            if ($section == 'assigned') {
                include 'assigned_complaints_section.php';
            } elseif ($section == 'completed') {
                include 'completed_work_section.php';
            } elseif ($section == 'notifications') {
                include 'notifications_section.php';
            } else {
                include 'dashboard_section.php';
            }
        ?>
    </main>

</body>
</html>