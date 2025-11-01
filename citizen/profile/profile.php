<?php
session_start();
include '../../db.php';

if (!isset($_SESSION['citizen_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$citizen_id = $_SESSION['citizen_id'];

// Fetch profile data
$sql = "SELECT name, email, phone_no, address, date_registered FROM citizen WHERE citizen_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $citizen_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

// Fetch unread notifications count
$notif_sql = "SELECT COUNT(*) AS unread FROM citizen_notification WHERE citizen_id = ? AND status = 'unread'";
$notif_stmt = $conn->prepare($notif_sql);
$notif_stmt->bind_param("i", $citizen_id);
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
    <title>Profile | MCCCTS</title>
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
            transition: 0.2s;
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
        input[readonly] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            font-size: 0.9rem;
            background-color: #f9fafb;
            color: #6c757d;
            cursor: not-allowed;
            transition: all 0.2s ease-in-out;
        }

        input[readonly]:hover {
            border-color: #ff4d4f; 
            background-color: #fff5f5;
        }

        .btn-primary {
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
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }

        .icon-register::before { content: '+'; font-weight: bold; }
        .icon-my-complaints::before { content: '📄'; }
        .icon-notifications::before { content: '🔔'; }
        .icon-profile::before { content: '👤'; }
    </style>
</head>
<body>
    <header class="header">
        <a href="../home.php" class="logo">MCCCTS</a>
        <nav class="nav-links">
            <a href="../RegisterComplaint/file_complaint.php" class="nav-link"><span class="icon icon-register"></span> Register Complaint</a>
            <a href="../MyComplaints/MyComplaintsPend.php" class="nav-link"><span class="icon icon-my-complaints"></span> My Complaints</a>
            <a href="../NotificationsCitizen/Notifications.php" class="nav-link">
                <span class="icon icon-notifications"></span> Notifications
                <?php if ($notification_count > 0): ?>
                    <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
                <?php endif; ?>
            </a>
            <a href="../profile/profile.php" class="nav-link active"><span class="icon icon-profile"></span> Profile</a>
        </nav>
    </header>

    <main>
        <div class="profile-container">
            <h2>Profile Information</h2>
            <p>View and manage your profile details</p>

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
                <label>Address</label>
                <input type="text" value="<?php echo htmlspecialchars($user['address']); ?>" readonly>
            </div>
            <div class="form-group">
                <label>Date Registered</label>
                <input type="text" value="<?php echo htmlspecialchars($user['date_registered']); ?>" readonly>
            </div>

            <!--button class="btn-primary">Edit Profile</button-->
        </div>
    </main>
</body>
</html>
