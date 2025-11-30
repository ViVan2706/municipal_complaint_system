<?php
session_start();
include '../db.php';

// 1. Check Worker Login
if (!isset($_SESSION['worker_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$message = "";

// 2. Update Profile Logic
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    
    // Update worker table (Only Name and Phone are editable)
    $update_sql = "UPDATE worker SET name = ?, phone_no = ? WHERE worker_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("ssi", $name, $phone, $worker_id);
    
    if ($update_stmt->execute()) {
        $message = "Profile updated successfully!";
        $_SESSION['name'] = $name; // Update session name
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

// 4. Fetch Notification Count (for Navbar)
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        :root {
            --primary-blue: #007bff;
            --background-light: #f8f9fa;
            --text-dark: #212529;
            --border-color: #dee2e6;
            --card-bg: #ffffff;
            --danger-red: #dc3545;
            --success-green: #28a745;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
        }
        
        /* --- Header / Navbar --- */
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
            display: flex;
            align-items: center;
            gap: 10px;
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
            transition: 0.2s;
            gap: 8px;
        }
        .nav-link:hover { color: var(--primary-blue); }
        .nav-link.active {
            background-color: var(--primary-blue);
            color: white;
        }
        .notifications-badge {
            background-color: var(--danger-red);
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 4px;
        }

        /* --- Profile Container --- */
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
        }
        
        /* Inputs */
        input {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.2s;
        }
        input:focus {
            outline: none;
            border-color: var(--primary-blue);
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        input[readonly] {
            background-color: #f9fafb;
            color: #6c757d;
            cursor: not-allowed;
            border-color: var(--border-color);
        }
        input[readonly]:hover {
            border-color: #ff4d4f; 
            background-color: #fff5f5;
        }
        
        /* Buttons */
        .btn-save {
            display: block;
            width: 100%;
            background-color: var(--primary-blue);
            color: white;
            font-weight: 500;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 10px;
        }
        .btn-save:hover {
            background-color: #0056b3;
        }

        .btn-logout {
            display: block;
            width: 100%;
            background-color: var(--danger-red);
            color: white;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            border: none;
            padding: 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s;
            margin-top: 15px;
        }
        .btn-logout:hover {
            background-color: #c82333;
        }

        .alert {
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border-radius: 6px;
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
    <header class="header">
        <a href="worker_dashboard.php" class="logo">
            <i class="fas fa-hard-hat"></i> MCCCTS Worker
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

    <main>
        <div class="profile-container">
            <h2>Worker Profile</h2>
            <p>Manage your contact details below</p>

            <?php if ($message): ?>
                <div class="alert"><?php echo htmlspecialchars($message); ?></div>
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
                
                <div class="form-group">
                    <label>Department</label>
                    <input type="text" value="<?php echo htmlspecialchars($user['department']); ?>" readonly>
                </div>
                
                <div class="form-group">
                    <label>Email (Cannot be changed)</label>
                    <input type="email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                </div>

                <div class="form-group">
                    <label>Current Status</label>
                    <input type="text" value="<?php echo htmlspecialchars(ucfirst($user['availability_status'])); ?>" readonly>
                </div>

                <button type="submit" name="update_profile" class="btn-save">Save Changes</button>
            </form>

            <a href="../LoginAndSignup/login.html" class="btn-logout">Logout</a>

        </div>
    </main>
</body>
</html>