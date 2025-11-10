<?php
// START SESSION
session_start();

// 1. ESTABLISH DATABASE CONNECTION
include '../db.php'; // Updated to use your db.php file

// ------------------------------------------------------------------
// --- SIMULATED LOGIN ---
// ------------------------------------------------------------------
// This hard-codes the user as Worker ID 306 ("Manish Light Tech")
// because he has data in your SQL file (Complaint #2, Notification #2).
//
// !!! REPLACE THIS with your real login system
// Your real login page should set $_SESSION['worker_id']
// ------------------------------------------------------------------
$_SESSION['worker_id'] = 306;
// ------------------------------------------------------------------


// 2. CHECK IF WORKER IS LOGGED IN
if (!isset($_SESSION['worker_id'])) {
    // If no session, redirect to login page
    // header("Location: ../LoginAndSignup/login.php"); // Adjust path as needed
    // exit();
    die("Simulated login failed. No worker_id in session."); // Fail loudly for this example
}

// 3. GET LOGGED-IN WORKER'S DETAILS
$worker_id = $_SESSION['worker_id'];
$worker_name = "Worker";
$notification_count = 0;

// Fetch worker's name
$sql_worker = "SELECT name FROM worker WHERE worker_id = ?";
$stmt_worker = mysqli_prepare($conn, $sql_worker);
mysqli_stmt_bind_param($stmt_worker, "i", $worker_id);
mysqli_stmt_execute($stmt_worker);
$result_worker = mysqli_stmt_get_result($stmt_worker);

if ($row_worker = mysqli_fetch_assoc($result_worker)) {
    // Get the first name
    $worker_name = explode(' ', $row_worker['name'])[0]; 
}
mysqli_stmt_close($stmt_worker);

// Fetch unread notification count
$sql_count = "SELECT COUNT(*) AS unread_count FROM worker_notification WHERE worker_id = ? AND status = 'unread'";
$stmt_count = mysqli_prepare($conn, $sql_count);
mysqli_stmt_bind_param($stmt_count, "i", $worker_id);
mysqli_stmt_execute($stmt_count);
$result_count = mysqli_stmt_get_result($stmt_count);
$row_count = mysqli_fetch_assoc($result_count);
$notification_count = $row_count['unread_count'];
mysqli_stmt_close($stmt_count);


// 4. ROUTER LOGIC
$section = $_GET['section'] ?? 'dashboard';
$active_assigned = ($section == 'assigned') ? 'active' : '';
$active_completed = ($section == 'completed') ? 'active' : '';
$active_notifications = ($section == 'notifications') ? 'active' : '';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MCCCTS - Worker Dashboard</title>
    <link rel="stylesheet" href="style.css"> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    
</head>
<body>

    <div class="container">
        
        <header class="navbar">
            <div class="logo">
                <i class="fas fa-hard-hat"></i> MCCCTS - Worker
            </div>
            <nav>
                <ul>
                    <li><a href="?section=assigned" class="nav-item <?php echo $active_assigned; ?>"><i class="fas fa-clipboard-list"></i> Assigned Complaints</a></li>
                    <li><a href="?section=completed" class="nav-item <?php echo $active_completed; ?>"><i class="fas fa-check-circle"></i> Completed Work</a></li>
                    <li>
                        <a href="?section=notifications" class="nav-item <?php echo $active_notifications; ?>">
                            <i class="fas fa-bell"></i> Notifications
                            <?php if ($notification_count > 0): ?>
                                <span class="notification-badge"><?php echo $notification_count; ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="#" class="nav-item"><i class="fas fa-user-circle"></i> Profile</a></li>
                </ul>
            </nav>
        </header>

        
        <main class="content">
            <?php
                // These included files can access $conn, $worker_id, and $worker_name
                // because they are all in the same script scope.
                
                if ($section == 'assigned') {
                    include 'assigned_complaints_section.php';
                } elseif ($section == 'completed') {
                    include 'completed_work_section.php';
                } elseif ($section == 'notifications') {
                    include 'notifications_section.php';
                } else {
                    // Default/Dashboard section
                    include 'dashboard_section.php';
                }
            ?>
        </main>
    </div>

    </body>
</html>