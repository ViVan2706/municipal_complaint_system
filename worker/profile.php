<?php
session_start();
include '../db.php';

// 1. Check Worker Login
if (!isset($_SESSION['worker_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$message = "";
$error = "";

// 2. Update Profile Logic (Only Name and Phone)
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    
    if (!empty($name) && !empty($phone)) {
        $update_sql = "UPDATE worker SET name = ?, phone_no = ? WHERE worker_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $name, $phone, $worker_id);
        
        if ($update_stmt->execute()) {
            $message = "Profile updated successfully!";
            $_SESSION['name'] = $name; // Update session name immediately
        } else {
            $error = "Error updating profile. Please try again.";
        }
        $update_stmt->close();
    } else {
        $error = "Name and Phone number cannot be empty.";
    }
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
            <a href="worker_dashboard.php" class="nav-link">Home</a>
            <a href="worker_dashboard.php?section=assigned" class="nav-link">Assigned</a>
            <a href="worker_dashboard.php?section=completed" class="nav-link">Completed</a>
            <a href="worker_dashboard.php?section=notifications" class="nav-link">
                Notifications
                <?php if ($notification_count > 0): ?>
                    <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
                <?php endif; ?>
            </a>
            <a href="profile.php" class="nav-link active">Profile</a>
        </nav>
    </header>

    <main class="container">
        
        <div class="form-card" style="max-width: 600px; margin: 0 auto;">
            <h2 class="page-title" style="margin-bottom: 5px;">Profile Information</h2>
            <p class="page-description">Update your personal details below</p>

            <?php if ($message): ?>
                <div style="padding: 12px; background-color: #d1e7dd; color: #0f5132; border-radius: 6px; margin-bottom: 20px; border: 1px solid #badbcc;">
                    <i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="padding: 12px; background-color: #f8d7da; color: #842029; border-radius: 6px; margin-bottom: 20px; border: 1px solid #f5c2c7;">
                    <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST">
                
                <div class="form-group">
                    <label>Full Name</label>
                    <input type="text" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?php echo htmlspecialchars($user['phone_no']); ?>" required>
                </div>
                
                <!-- <hr style="border: 0; border-top: 1px solid #eee; margin: 25px 0;"> -->
                
                <div class="form-group">
                    <label>Address (Cannot be changed)</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['department']); ?>" readonly style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed;">
                </div>
                
                <div class="form-group">
                    <label>Email (Cannot be changed)</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label>Current Status</label>
                    <input type="text" value="<?php echo htmlspecialchars(ucfirst($user['availability_status'])); ?>" readonly style="background-color: #e9ecef; color: #6c757d; cursor: not-allowed;">
                </div>

                <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
            </form>

            <a href="../LoginAndSignup/login.html" class="btn-logout">Logout</a>

        </div>
    </main>
</body>
</html>