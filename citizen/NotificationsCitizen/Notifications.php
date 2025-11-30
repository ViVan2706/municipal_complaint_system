<?php
session_start();
include '../../db.php'; 

if (!isset($_SESSION['citizen_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$citizen_id = $_SESSION['citizen_id'];

// fetch notifications
$sql = "SELECT citizen_notification_id, message, date_time, status FROM citizen_notification WHERE citizen_id = ? ORDER BY date_time DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $citizen_id);
$stmt->execute();
$result = $stmt->get_result();

// count unread for badge
$count_sql = "SELECT COUNT(*) AS unread_count FROM citizen_notification WHERE citizen_id = ? AND status = 'unread'";
$count_stmt = $conn->prepare($count_sql);
$count_stmt->bind_param("i", $citizen_id);
$count_stmt->execute();
$count_res = $count_stmt->get_result()->fetch_assoc();
$notification_count = $count_res['unread_count'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Notifications | MCCCTS</title>
<style>
:root {
    --primary-blue: #007bff;
    --card-bg: #ffffff;
    --border-color: #dee2e6;
    --text-dark: #212529;
    --text-muted: #6c757d;
    --background-light: #f8f9fa;
    --success-green: #28a745;
}
body {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
    background-color: var(--background-light);
    color: var(--text-dark);
    margin: 0;
}

/* Navbar Styling */
.header {
    background-color: var(--card-bg);
    border-bottom: 1px solid var(--border-color);
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.logo {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-dark);
    text-decoration: none;
}
.nav-links {
    display: flex;
    align-items: center;
}
.nav-link {
    text-decoration: none;
    color: var(--text-dark);
    margin-left: 20px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
    border-radius: 6px;
    transition: background-color 0.2s, color 0.2s;
}
.nav-link:hover {
    color: var(--primary-blue);
}
.nav-link.active {
    background-color: var(--primary-blue);
    color: #fff;
}
.notifications-badge {
    background-color: #dc3545;
    color: #fff;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.7rem;
    margin-left: 4px;
}
.icon {
    display: inline-block;
    margin-right: 6px;
}
.icon-register::before { content: '+'; font-weight: bold; }
.icon-my-complaints::before { content: '📄'; }
.icon-notifications::before { content: '🔔'; color: #ffb300; }
.icon-profile::before { content: '👤'; color: #4b0082; }

/* Notifications Layout */
.notifications-container {
    max-width: 900px;
    margin: 40px auto;
    padding: 0 20px;
}
h1 {
    font-size: 1.8rem;
    font-weight: 700;
    margin-bottom: 25px;
}
.notification-card {
    background: var(--card-bg);
    border: 1px solid #e0e4ed;
    border-radius: 10px;
    padding: 15px 20px;
    margin-bottom: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    transition: 0.2s ease;
}
.notification-card:hover {
    background: #f4f7ff;
    box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}
.notification-content {
    display: flex;
    align-items: flex-start;
}
.icon-wrapper {
    background: #eef3ff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
    color: var(--primary-blue);
    margin-right: 15px;
}
.notification-text {
    font-size: 0.95rem;
    color: var(--text-dark);
}
.notification-date {
    font-size: 0.85rem;
    color: var(--text-muted);
    margin-top: 5px;
}
.notification-actions {
    display: flex;
    align-items: center;
    gap: 10px; 
}
.badge-new {
    background: var(--primary-blue);
    color: #fff;
    font-size: 0.75rem;
    padding: 4px 10px;
    border-radius: 12px;
    font-weight: 500;
}

.btn-tick {
    background: transparent;
    border: 2px solid var(--success-green);
    color: var(--success-green);
    border-radius: 50%;
    width: 32px;
    height: 32px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 1.1rem;
    transition: all 0.2s ease;
    padding: 0;
}
.btn-tick:hover {
    background-color: var(--success-green);
    color: white;
    transform: scale(1.1);
}
</style>
</head>
<body>

<header class="header">
    <a href="../home.php" class="logo">MCCCTS</a>
    <nav class="nav-links">
        <a href="../RegisterComplaint/file_complaint.php" class="nav-link">
            <span class="icon icon-register"></span> Register Complaint
        </a>
        <a href="../MyComplaints/MyComplaintsPend.php" class="nav-link">
            <span class="icon icon-my-complaints"></span> My Complaints
        </a>
        <a href="../NotificationsCitizen/Notifications.php" class="nav-link active">
            <span class="icon icon-notifications"></span> Notifications
            <?php if ($notification_count > 0): ?>
                <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
            <?php endif; ?>
        </a>
        <a href="../profile/profile.php" class="nav-link">
            <span class="icon icon-profile"></span> Profile
        </a>
    </nav>
</header>

<main class="notifications-container">
    <h1>Notifications</h1>
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="notification-card" style="<?php echo ($row['status'] === 'unread') ? 'background:#f4f7ff; border-left: 4px solid var(--primary-blue);' : ''; ?>">
                <div class="notification-content">
                    <div class="icon-wrapper">
                        <?php
                            if (stripos($row['message'], 'assigned') !== false) echo '👷';
                            else if (stripos($row['message'], 'resolved') !== false) echo '✅';
                            else echo '🔔';
                        ?>
                    </div>
                    <div>
                        <div class="notification-text"><?php echo htmlspecialchars($row['message']); ?></div>
                        <div class="notification-date"><?php echo htmlspecialchars($row['date_time']); ?></div>
                    </div>
                </div>

                <div class="notification-actions">
                    <?php if ($row['status'] === 'unread'): ?>
                        <span class="badge-new">New</span>
                        
                        <form action="mark_read.php" method="POST" style="margin:0;">
                            <input type="hidden" name="notif_id" value="<?php echo $row['citizen_notification_id']; ?>">
                            <button type="submit" class="btn-tick" title="Mark as Read">✔</button>
                        </form>
                    <?php endif; ?>
                </div>

            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="color: var(--text-muted);">No notifications found.</p>
    <?php endif; ?>
</main>

</body>
</html>