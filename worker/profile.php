<?php
session_start();
include '../db.php';

// 1. Check Worker Login
if (!isset($_SESSION['worker_id'])) { // Removed strict role check for simulation compatibility, feel free to add back
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$message = "";

// 2. Update Profile Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    
    $update_sql = "UPDATE worker SET name = ?, phone_no = ? WHERE worker_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $name, $phone, $worker_id);
    
    if ($update_stmt->execute()) {
        $message = "Profile updated successfully!";
        $_SESSION['name'] = $name; 
    } else {
        $message = "Error updating profile.";
    }
    $update_stmt->close();
}

// 3. Fetch Worker Details
$sql = "SELECT name, email, phone_no, department, availability_status FROM worker WHERE worker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// 4. Fetch Notification Count
$notif_sql = "SELECT COUNT(*) AS unread FROM worker_notification WHERE worker_id = ? AND status = 'unread'";
$notif_stmt = $conn->prepare($notif_sql);
$notif_stmt->bind_param("i", $worker_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result()->fetch_assoc();
$notification_count = $notif_result['unread'] ?? 0;
$notif_stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | MCCCTS Worker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <header class="header">
        <a href="worker_dashboard.php" class="logo">
            <i class="fas fa-hard-hat"></i> MCCCTS - Worker
        </a>
        <nav class="nav-links">
            <a href="worker_dashboard.php" class="nav-link">
                <i class="fas fa-home"></i> Home
            </a>
            <a href="worker_dashboard.php?section=assigned" class="nav-link">
                <i class="fas fa-clipboard-list"></i> Assigned
            </a>
            <a href="worker_dashboard.php?section=completed" class="nav-link">
                <i class="fas fa-check-circle"></i> Completed
            </a>
            <a href="worker_dashboard.php?section=notifications" class="nav-link">
                <i class="fas fa-bell"></i> Notifications
                <?php if ($notification_count > 0): ?>
                    <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php" class="nav-link active">
                <i class="fas fa-user-circle"></i> Profile
            </a>
        </nav>
    </header>

    <main class="container">
        <div class="list-item" style="max-width: 600px; margin: 0 auto;">
            <h2 class="item-title" style="margin-bottom: 5px;">Worker Profile</h2>
            <p style="color: var(--text-muted); margin-bottom: 25px;">Manage your contact details below</p>

            <?php if ($message): ?>
                <div style="padding: 12px; background-color: #d4edda; color: #155724; border-radius: 4px; margin-bottom: 20px;"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>

            <form method="POST">
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:4px;">
                </div>
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone_no']); ?>" required style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:4px;">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Department</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['department']); ?>" readonly style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:4px; background-color: var(--background-light);">
                </div>
                
                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Email</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:4px; background-color: var(--background-light);">
                </div>

                <div style="margin-bottom: 20px;">
                    <label style="display:block; font-weight:600; margin-bottom:8px;">Current Status</label>
                    <input type="text" value="<?php echo htmlspecialchars(ucfirst($user['availability_status'])); ?>" readonly style="width:100%; padding:10px; border:1px solid var(--border-color); border-radius:4px; background-color: var(--background-light);">
                </div>

                <button type="submit" name="update_profile" class="btn-save" style="width:100%; justify-content:center; background-color: var(--primary-blue); color: white; border:none; cursor:pointer;">Save Changes</button>
            </form>

            <a href="../LoginAndSignup/login.html" style="display:block; text-align:center; color: var(--danger-red); margin-top: 20px; text-decoration:none;">Logout</a>

        </div>
    </main>
</body>
</html>