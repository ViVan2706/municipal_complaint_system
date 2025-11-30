<?php
// Do NOT call session_start() — already started in admin_dashboard.php

include __DIR__ . "/../../db.php";

// Check admin login
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../LoginAndSignup/login.html");
    exit();
}

$admin_id = $_SESSION['admin_id'];

// Mark all unread as read if user clicks the button
if (isset($_GET['mark_all'])) {
    $conn->query("
        UPDATE admin_notification 
        SET status = 'read' 
        WHERE admin_id = $admin_id
    ");

    header("Location: ../admin_dashboard.php?section=notifications");
    exit();
}

// Fetch notifications
$sql = "
    SELECT * FROM admin_notification 
    WHERE admin_id = $admin_id
    ORDER BY date_time DESC
";

$notifications = $conn->query($sql);
?>

<h1 class="page-title">Notifications</h1>
<p class="page-description">
    All updates related to complaints and worker activity.
</p>

<a href="../admin_dashboard.php?section=notifications&mark_all=1" 
   class="btn-mark-all">Mark all as read</a>

<div class="notif-container">

<?php while ($row = $notifications->fetch_assoc()): ?>
    <div class="notif-card <?= $row['status'] === 'unread' ? 'unread' : '' ?>">

        <div class="notif-message">
            <?= htmlspecialchars($row['message']) ?>
        </div>

        <div class="notif-time">
            <?= date("d M Y, h:i A", strtotime($row['date_time'])) ?>
        </div>

    </div>
<?php endwhile; ?>

</div>

<style>
.notif-container {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 20px;
}

.notif-card {
    padding: 15px;
    background: white;
    border-radius: 8px;
    border-left: 5px solid #1976d2;
    box-shadow: 0 2px 6px rgba(0,0,0,0.08);
}

.notif-card.unread {
    background: #e3f2fd;
    border-left-color: #0d47a1;
}

.notif-message {
    font-size: 15px;
    color: #0d47a1;
    font-weight: 600;
}

.notif-time {
    font-size: 13px;
    color: #555;
    margin-top: 4px;
}

.btn-mark-all {
    background: #0d47a1;
    color: white;
    padding: 8px 14px;
    border-radius: 6px;
    text-decoration: none;
    font-weight: 600;
}
.btn-mark-all:hover {
    background: #08306b;
}
.page-title {
    font-size: 26px;
    color: #0d47a1;
    font-weight: 700;
}
.page-description {
    color: #444;
    margin-bottom: 15px;
}
</style>
