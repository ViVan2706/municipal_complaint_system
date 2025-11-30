<?php
include '../db.php';

?>

<style>
/* Button Styling */
.btn-tick {
    background: transparent;
    border: 2px solid #28a745; /* Green color */
    color: #28a745;
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1rem;
    transition: all 0.2s ease;
    padding: 0;
}
.btn-tick:hover {
    background-color: #28a745;
    color: white;
    transform: scale(1.1);
}

/* Unread Card Styling */
.unread {
    background-color: #f0f7ff; /* Light Blue */
    border-left: 4px solid #007bff;
}

.badge-new {
    background: #007bff;
    color: #fff;
    font-size: 0.75rem;
    padding: 2px 8px;
    border-radius: 12px;
    margin-right: 10px;
    font-weight: bold;
}
</style>

<h1 class="page-title">Notifications</h1>

<?php
// Query to fetch worker notifications
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
        
        // Icon Logic
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
        
        // Check Status
        $is_unread = ($row['status'] == 'unread');
        $card_class = $is_unread ? 'unread' : ''; 
?>

<div class="notification-card <?php echo $card_class; ?>" style="display: flex; justify-content: space-between; align-items: center; padding: 15px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05);">
    
    <div class="notif-left" style="display: flex; gap: 15px; align-items: center;">
        <div class="notif-icon-box <?php echo $icon_style; ?>" style="font-size: 1.2rem;">
            <i class="<?php echo $icon; ?>"></i>
        </div>
        
        <div>
            <div class="notif-message" style="font-weight: 500;">
                <?php echo htmlspecialchars($row['message']); ?>
            </div>
            <div class="notif-date" style="font-size: 0.85rem; color: #888;">
                <?php echo date("Y-m-d h:i A", strtotime($row['date_time'])); ?>
            </div>
        </div>
    </div>

    <div class="notif-actions" style="display: flex; align-items: center;">
        <?php if ($is_unread): ?>
            <span class="badge-new">New</span>
            
            <form action="mark_read.php" method="POST" style="margin:0;">
                <input type="hidden" name="notif_id" value="<?php echo $row['worker_notification_id']; ?>">
                <button type="submit" class="btn-tick" title="Mark as Read">
                    <i class="fas fa-check"></i>
                </button>
            </form>
        <?php else: ?>
            <span style="color: #ccc; font-size: 1.2rem; padding: 0 10px;">
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