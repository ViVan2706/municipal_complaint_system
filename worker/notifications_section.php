<h1 class="page-title">Notifications</h1>

<?php
// This file assumes $conn and $worker_id are available from worker_dashboard.php

$sql = "SELECT * FROM worker_notification
        WHERE worker_id = ?
        ORDER BY date_time DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        // Logic to determine icon based on message content
        $icon_class = "fas fa-info-circle"; // default
        $message_lower = strtolower($row['message']);
        
        if (strpos($message_lower, 'assigned') !== false) {
            $icon_class = "fas fa-user";
        } elseif (strpos($message_lower, 'completed') !== false || strpos($message_lower, 'approved') !== false) {
            $icon_class = "fas fa-check-circle";
        } elseif (strpos($message_lower, 'progress') !== false) {
            $icon_class = "fas fa-clock";
        }
        
        // Show 'New' badge
        $is_new = ($row['status'] == 'unread');
?>

<div class="list-item notification-item">
    <div class="notification-icon-text">
        <i class="<?php echo $icon_class; ?>"></i>
        <div class="notification-content">
            <div class="notification-text"><?php echo htmlspecialchars($row['message']); ?></div>
            <div class="notification-date"><?php echo date("Y-m-d h:i A", strtotime($row['date_time'])); ?></div>
        </div>
    </div>
    <?php if ($is_new): ?>
        <span class="item-status status-new">New</span>
    <?php endif; ?>
</div>

<?php
    } // end while

    // After loading, let's mark all 'unread' as 'read' for this worker
    $sql_update = "UPDATE worker_notification SET status = 'read' WHERE worker_id = ? AND status = 'unread'";
    $stmt_update = mysqli_prepare($conn, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "i", $worker_id);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

} else {
    echo "<p>You have no notifications.</p>";
}
mysqli_stmt_close($stmt);
?>