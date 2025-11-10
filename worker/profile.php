<?php
session_start();
include '../db.php'; // Updated path assuming this file is in the 'worker' folder

// 1. Check for worker session
if (!isset($_SESSION['worker_id'])) {
    header("Location: ../LoginAndSignup/login.html"); // Or your worker login page
    exit();
}

$worker_id = $_SESSION['worker_id'];

// 2. Fetch worker profile data
$sql = "SELECT name, email, phone_no, availability_status, department 
        FROM worker 
        WHERE worker_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $worker_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    die("Error: Worker not found.");
}

// 3. Fetch unread worker notifications count
$notif_sql = "SELECT COUNT(*) AS unread 
              FROM worker_notification 
              WHERE worker_id = ? AND status = 'unread'";
$notif_stmt = $conn->prepare($notif_sql);
$notif_stmt->bind_param("i", $worker_id);
$notif_stmt->execute();
$notif_result = $notif_stmt->get_result()->fetch_assoc();
$notification_count = $notif_result['unread'] ?? 0;
$notif_stmt->close();

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Profile | MCCCTS</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-blue: #007bff;
            --background-light: #f8f9fa;
            --text-dark: #212529;
            --border-color: #dee2e6;
            --card-bg: #ffffff;
            --danger-red: #dc3545;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
        }
        
        /* --- Worker Navigation Bar --- */
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
            color: var(--primary-blue);
            text-decoration: none;
            display: flex;
            align-items: center;
        }
        .logo i {
            margin-right: 8px;
        }
        .nav-links {
            display: flex;
            align-items: center;
            list-style: none;
        }
        .nav-link {
            text-decoration: none;
            color: #555;
            margin-left: 20px;
            padding: 8px 12px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
            font-weight: 500;
            border-radius: 6px;
            transition: 0.2s;
        }
        .nav-link i {
            margin-right: 5px;
            color: #888;
        }
        .nav-link:hover { 
            color: var(--primary-blue);
            background-color: #f0f5ff;
        }
        .nav-link.active {
            background-color: #e6f0ff;
            color: var(--primary-blue);
            font-weight: 600;
        }
        .nav-link.active i {
            color: var(--primary-blue);
        }
        .notifications-badge {
            background-color: var(--danger-red);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 5px;
            position: relative;
            top: -8px;
            right: 5px;
        }
        /* --- End Worker Navigation Bar --- */

        .profile-container {
            max-width: 600px;
            margin: 60px auto;
            background-color: var(--card-bg);
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.08);
            padding: 30px;
        }
        .profile-container h2 {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .profile-container p {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            font-weight: 500;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        input[readonly] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.95rem;
            background-color: #f9fafb;
            color: #495057;
            cursor: not-allowed;
        }

    </style>
</head>
<body>
    <header class="header">
        <a href="worker_dashboard.php" class="logo">
            <i class="fas fa-hard-hat"></i> MCCCTS - Worker
        </a>
        <nav>
            <ul class="nav-links">
                <li><a href="worker_dashboard.php?section=assigned" class="nav-link">
                    <i class="fas fa-clipboard-list"></i> Assigned Complaints
                </a></li>
                <li><a href="worker_dashboard.php?section=completed" class="nav-link">
                    <i class="fas fa-check-circle"></i> Completed Work
                </a></li>
                <li>
                    <a href="worker_dashboard.php?section=notifications" class="nav-link">
                        <i class="fas fa-bell"></i> Notifications
                        <?php if ($notification_count > 0): ?>
                            <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li><a href="profile.php" class="nav-link active">
                    <i class="fas fa-user-circle"></i> Profile
                </a></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <h2>Profile Information</h2>
            <p>Your worker profile details</p>

            <div class="form-group">
                <label>Full Name</label>
                <input type="text" value="<?php echo htmlspecialchars($user['name']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Phone Number</label>
                <input type="text" value="<?php echo htmlspecialchars($user['phone_no']); ?>" readonly>
            </div>
            
            <div class="form-group">
                <label>Department</label>
                <input type="text" value="<?php echo htmlspecialchars($user['department']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Availability Status</label>
                <input type="text" value="<?php echo htmlspecialchars(ucfirst($user['availability_status'])); ?>" readonly>
            </div>

            </div>
    </main>
</body>
</html>