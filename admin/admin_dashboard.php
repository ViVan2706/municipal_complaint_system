<?php
session_start();
include "../db.php";

// CHECK ADMIN LOGIN
if (!isset($_SESSION['admin_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

// Router section
$section = $_GET['section'] ?? 'dashboard';

// ACTIVE TAB UI
function active($name, $current) {
    return $name === $current ? 'active' : '';
}

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | MCCCTS</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body>

<div class="main-container">

    <!-- NAVBAR -->
    <header class="navbar">
        <div class="logo">
            <i class="fas fa-user-shield"></i> MCCCTS Admin Panel
        </div>

        <nav>
            <ul>
                <li><a class="<?= active('dashboard', $section) ?>" href="admin_dashboard.php?section=dashboard">
                    <i class="fas fa-home"></i> Dashboard</a></li>

                <li><a class="<?= active('complaints', $section) ?>" href="admin_dashboard.php?section=complaints">
                    <i class="fas fa-clipboard-list"></i> Complaints</a></li>

                <li><a class="<?= active('workers', $section) ?>" href="admin_dashboard.php?section=workers">
                    <i class="fas fa-users"></i> Workers</a></li>

                <li><a class="<?= active('citizens', $section) ?>" href="admin_dashboard.php?section=citizens">
                    <i class="fas fa-user"></i> Citizens</a></li>

                <li><a class="<?= active('notifications', $section) ?>" href="admin_dashboard.php?section=notifications">
                    <i class="fas fa-bell"></i> Notifications</a></li>

                <li><a class="<?= active('profile', $section) ?>" href="admin_dashboard.php?section=profile">
                    <i class="fas fa-user-circle"></i> Profile</a></li>

                <li><a href="../LoginAndSignup/logout.php">
                    <i class="fas fa-sign-out-alt"></i> Logout</a></li>
            </ul>
        </nav>
    </header>

    <!-- MAIN CONTENT -->
    <main class="content">

        <?php
        if ($section == 'dashboard') {
            include "sections/dashboard_section.php";
        }
        elseif ($section == 'complaints') {
            include "sections/complaints_section.php";
        }
        elseif ($section == 'workers') {
            include "sections/worker_section.php";
        }
        elseif ($section == 'citizens') {
            include "sections/citizen_section.php";
        }
        elseif ($section == 'notifications') {
            include "sections/notifications_section.php";
        }
        elseif ($section == 'profile') {
            include "sections/profile_section.php";
        }
        ?>

    </main>
</div>

</body>
</html>
