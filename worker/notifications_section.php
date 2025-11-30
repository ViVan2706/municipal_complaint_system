<h1 class="page-title">Notifications</h1>

<?php

$sql = "SELECT * FROM worker_notification
        WHERE worker_id = ?
        ORDER BY date_time DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $msg = strtolower($row['message']);
        if (strpos($msg, 'assigned') !== false) {
            $icon = "fas fa-user";
            $icon_style = "icon-orange"; 
        } elseif (strpos($msg, 'resolved') !== false || strpos($msg, 'completed') !== false) {
            $icon = "fas fa-check";
            $icon_style = "icon-green"; 
        } else {
            $icon = "fas fa-info";
            $icon_style = "icon-blue"; 
        }
        
        // 2. Determine Status (Read/Unread)
        $is_unread = ($row['status'] == 'unread');
        // 'unread' class triggers the blue left border and light blue background
        $card_class = $is_unread ? 'unread' : ''; 
?>

<div class="notification-card <?php echo $card_class; ?>">
    
    <div class="notif-left">
        <div class="notif-icon-box <?php echo $icon_style; ?>">
            <i class="<?php echo $icon; ?>"></i>
        </div>
        
        <div>
            <div class="notif-message">
                <?php echo htmlspecialchars($row['message']); ?>
            </div>
            <div class="notif-date">
                <?php echo date("Y-m-d H:i:s", strtotime($row['date_time'])); ?>
            </div>
        </div>
    </div>

    <div class="notif-actions">
        <?php if ($is_unread): ?>
            <span class="badge-new">New</span>
            
            <form action="mark_read.php" method="POST" style="margin:0;">
                <input type="hidden" name="notif_id" value="<?php echo $row['worker_notification_id']; ?>">
                <button type="submit" class="btn-tick" title="Mark as Read">
                    <i class="fas fa-check"></i>
                </button>
            </form>
        <?php else: ?>
            <span style="color: #ccc; font-size: 1.2rem;">
                <i class="fas fa-check-double"></i>
             </span>
        <?php endif; ?>
    </div>

</div>

<?php
    } 
} else {
    echo "<p style='color:#6c757d; text-align:center; margin-top:20px;'>You have no notifications.</p>";
}
mysqli_stmt_close($stmt);
?>