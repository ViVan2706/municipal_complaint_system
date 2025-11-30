<?php
session_start();
include "../db.php";

// CHECK ADMIN LOGIN
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";
$section = $_GET['section'] ?? 'dashboard';

// Active tabs
$active_dashboard     = ($section == 'dashboard') ? 'active' : '';
$active_workers       = ($section == 'workers') ? 'active' : '';
$active_complaints    = ($section == 'complaints') ? 'active' : '';
$active_citizens      = ($section == 'citizens') ? 'active' : '';
$active_notifications = ($section == 'notifications') ? 'active' : '';
$active_profile       = ($section == 'profile') ? 'active' : '';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard | MCCCTS</title>

    <link rel="stylesheet" 
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f7faff;
        }

        /* WHITE HEADER */
        .navbar {
            background: white;
            padding: 15px 30px;
            border-bottom: 2px solid #e6e6e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 22px;
            font-weight: 700;
            color: #0d47a1;
        }

        .navbar ul {
            display: flex;
            gap: 20px;
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .navbar a {
            text-decoration: none;
            font-weight: 600;
            color: #0d47a1;
            padding: 8px 12px;
            border-radius: 6px;
            transition: 0.2s;
        }

        .navbar a:hover {
            background: #e3f2fd;
        }

        .active {
            background: #bbdefb !important;
            color: #0d47a1 !important;
        }

        .content {
            padding: 25px;
        }
    </style>

</head>
<body>

<!-- HEADER -->
<header class="navbar">
    <div class="logo">
        <i class="fas fa-user-shield"></i> MCCCTS Admin Panel
    </div>

    <nav>
        <ul>
            <li><a class="<?= $active_dashboard ?>" 
                   href="admin_dashboard.php?section=dashboard">
                   <i class="fas fa-home"></i> Dashboard
            </a></li>

            <li><a class="<?= $active_complaints ?>"
                   href="admin_dashboard.php?section=complaints">
                   <i class="fas fa-clipboard-list"></i> Complaints
            </a></li>

            <li><a class="<?= $active_workers ?>"
                   href="admin_dashboard.php?section=workers">
                   <i class="fas fa-users"></i> Workers
            </a></li>

            <li><a class="<?= $active_citizens ?>"
                   href="admin_dashboard.php?section=citizens">
                   <i class="fas fa-user"></i> Citizens
            </a></li>

            <li><a class="<?= $active_notifications ?>"
                   href="admin_dashboard.php?section=notifications">
                   <i class="fas fa-bell"></i> Notifications
            </a></li>

            <li><a class="<?= $active_profile ?>"
                   href="admin_dashboard.php?section=profile">
                   <i class="fas fa-user-circle"></i> Profile
            </a></li>

            <li><a href="../LoginAndSignup/logout.php">
                   <i class="fas fa-sign-out-alt"></i> Logout
            </a></li>
        </ul>
    </nav>
</header>



<!-- MAIN CONTENT -->
<div class="content">
<?php

// ROUTER — Loads correct section file
switch ($section) {

    case 'dashboard':
        include "sections/dashboard_section.php";
        break;

    case 'workers':
        include "sections/worker_section.php";
        break;

    case 'citizens':
        include "sections/citizen_section.php";
        break;

    case 'complaints':
        include "sections/complaints_section.php";
        break;

    case 'notifications':
        include "sections/notifications_section.php";
        break;

    case 'view_complaint':
        include "sections/view_complaint_section.php";
        break;

    case 'profile':
        include "sections/profile_section.php";
        break;

    default:
        echo "<h2>Page not found</h2>";
}
?>
</div>

</body>
</html>
