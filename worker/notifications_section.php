<style>
    /* ... Baki purani CSS same rahegi (card layout etc) ... */

    /* Sirf Emoji Button ke liye style */
    .btn-emoji {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 1.8rem; /* Emoji ka size */
        padding: 0;
        margin-left: 10px;
        transition: transform 0.2s ease;
    }

    /* Hover karne par thoda bada hoga */
    .btn-emoji:hover {
        transform: scale(1.2);
    }

    .notification-actions {
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>

<?php
// ... Database connection code ...

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        $is_unread = ($row['status'] == 'unread');
        
        // Unread = Blue Highlight, Read = Normal
        $card_style = $is_unread ? 'background:#f0f7ff; border-left: 5px solid #007bff;' : 'background:#fff; border-left: 5px solid transparent; opacity: 0.7;'; 
?>

<div class="notification-card" style="<?php echo $card_style; ?>">
    
    <div class="notification-content">
        <div class="icon-wrapper">
             <?php 
                if (strpos(strtolower($row['message']), 'assigned') !== false) {
                    echo '<i class="fas fa-briefcase"></i>';
                } else {
                    echo '<i class="fas fa-bell"></i>';
                }
             ?>
        </div>
        <div>
            <div class="notification-text"><?php echo htmlspecialchars($row['message']); ?></div>
            <div class="notification-date">
                <?php echo date("d M Y, h:i A", strtotime($row['date_time'])); ?>
            </div>
        </div>
    </div>

    <div class="notification-actions">
        <?php if ($is_unread): ?>
            
            <span class="badge-new">New</span>

            <form action="mark_read.php" method="POST" style="margin:0;">
                <input type="hidden" name="notif_id" value="<?php echo $row['worker_notification_id']; ?>">
                
                <button type="submit" class="btn-emoji" title="Mark as Read">
                    ✅
                </button>
            </form>

        <?php else: ?>
            <?php endif; ?>
    </div>

</div>

<?php
    } 
} else {
    echo "<p style='text-align:center; margin-top:20px; color:#666;'>No notifications found.</p>";
}
?>