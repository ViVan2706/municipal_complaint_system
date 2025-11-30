<?php
session_start();
include '../db.php'; 

if (!isset($_SESSION['worker_id']) || $_SESSION['role'] !== 'worker') {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$worker_name = "Worker";

$sql_worker = "SELECT name FROM worker WHERE worker_id = ?";
$stmt_worker = $conn->prepare($sql_worker);
$stmt_worker->bind_param("i", $worker_id);
$stmt_worker->execute();
$result_worker = $stmt_worker->get_result();
if ($row_worker = $result_worker->fetch_assoc()) {
    $worker_name = explode(' ', $row_worker['name'])[0]; 
}
$stmt_worker->close();

$notification_count = 0;
$sql_count = "SELECT COUNT(*) AS unread_count FROM worker_notification WHERE worker_id = ? AND status = 'unread'";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $worker_id);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
if ($row_count = $result_count->fetch_assoc()) {
    $notification_count = $row_count['unread_count'];
}
$stmt_count->close();

$stats_sql = "
    SELECT 
        (SELECT COUNT(*) FROM complaint WHERE worker_id = ? AND status IN ('pending', 'in_progress')) AS assigned_count,
        (SELECT COUNT(*) FROM complaint WHERE worker_id = ? AND status IN ('resolved', 'closed')) AS completed_count,
        (SELECT COUNT(*) FROM complaint WHERE worker_id = ?) AS total_count
";
$stmt_stats = $conn->prepare($stats_sql);
$stmt_stats->bind_param("iii", $worker_id, $worker_id, $worker_id);
$stmt_stats->execute();
$data = $stmt_stats->get_result()->fetch_assoc();
$stmt_stats->close();

$assigned_count = $data['assigned_count'] ?? 0;
$completed_count = $data['completed_count'] ?? 0;
$total_count = $data['total_count'] ?? 0;

$section = $_GET['section'] ?? 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Dashboard | MCCCTS</title>
    <style>
        :root {
            --primary-blue:#007bff;
            --success-green:#28a745;
            --background-light:#f8f9fa;
            --text-dark:#212529;
            --border-color:#dee2e6;
            --card-bg:#ffffff;
        }
        * {
            box-sizing:border-box;
            margin:0;
            padding:0;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background-color: var(--background-light);
            color: var(--text-dark);
            line-height: 1.6;
        }
        .icon {
            display: inline-block;
            width: 1em;
            height: 1em;
            margin-right: 8px;
            transform: translateY(-5px);
            vertical-align: middle; 
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
            padding: 5px 10px;
            display: flex;
            align-items: center;
            font-size: 0.95rem;
        }
        .nav-link:hover {
            color: var(--primary-blue);
        }
        .notifications-badge {
            background-color: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 6px;
            font-size: 0.7rem;
            margin-left: 4px;
        }
        .dashboard-container {
            padding: 30px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .welcome-section h1 {
            font-size: 1.8rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .welcome-section p {
            font-size: 1rem;
            color: #6c757d;
            margin-bottom: 30px;
        }
        .stats-grid {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
            flex-wrap: wrap;
        }
        .stat-card {
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 20px;
            flex-grow: 1;
            flex-basis: 0;
            min-width: 250px;
            border: 1px solid var(--border-color);
        }
        .stat-card-pending {
            border-left: 5px solid var(--primary-blue);
        }
        .stat-card-resolved {
            background-color: #e2ffe8; 
            border: 1px solid #c3e6cb;
            border-left: 5px solid var(--success-green);
        }
        .stat-card-total {
            border-left: 5px solid #6c757d;
        }
        .stat-card .icon-placeholder {
            font-size: 1.5rem;
            margin-bottom: 10px;
            display: block;
        }
        .stat-card h2 {
            font-size: 2.2rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .stat-card p {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .icon-pending { color: var(--primary-blue); content: '⌚'; font-size: 2.5rem; }
        .icon-resolved { color: var(--success-green); content: '✔'; font-size: 2.5rem; }
        .icon-total { color: #6c757d; content: '📄'; font-size: 2.5rem; }
        
        .actions-grid {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .action-button {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: flex-start;
            background-color: var(--card-bg);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 30px;
            flex-grow: 1;
            flex-basis: 0;
            min-width: 300px;
            text-decoration: none;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .action-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .register-button {
            border: 2px solid var(--primary-blue);
            color: var(--text-dark);
        }
        .register-button .icon-main {
            color: var(--primary-blue);
            font-size: 2.2rem;
            margin-bottom: 10px;
        }
        .view-button {
            background-color: #e6fff0;
            border: 2px solid var(--success-green);
            color: var(--text-dark);
        }        
        .view-button .icon-main {
            color: var(--success-green);
            font-size: 2.2rem;
            margin-bottom: 10px;
        } 
        .action-button h3 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .action-button p {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .icon-register::before { content: '🏠'; font-weight: bold; } 
        .icon-my-complaints::before { content: '🛠️'; } 
        .icon-completed::before { content: '✅'; } 
        .icon-notifications::before { content: '🔔'; }
        .icon-profile::before { content: '👤'; }
        .icon-add::before { content: '📋'; } 
        .icon-view::before { content: '🗂️'; } 
    </style>
</head>
<body>

    <header class="header">
        <a href="worker_dashboard.php" class="logo">MCCCTS Worker</a>
        <nav class="nav-links">
            
            <a href="?section=assigned" class="nav-link"><span class="icon icon-my-complaints"></span> Assigned</a>
            
            <a href="?section=completed" class="nav-link"><span class="icon icon-completed"></span> Completed</a>
            
            <a href="?section=notifications" class="nav-link">
                <span class="icon icon-notifications"></span> Notifications 
                <?php if ($notification_count > 0): ?>
                    <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
                <?php endif; ?>
            </a>
            
            <a href="profile.php" class="nav-link"><span class="icon icon-profile"></span> Profile</a>
        </nav>
    </header>

    <main class="dashboard-container">
        
        <?php if ($section == 'dashboard'): ?>
            <section class="welcome-section">
                <h1>Welcome Back, <?php echo htmlspecialchars($worker_name); ?>!</h1>
                <p>Manage your assigned tasks and track work progress</p>
            </section>

            <section class="stats-grid">
                
                <div class="stat-card stat-card-pending">
                    <span class="icon-placeholder icon-pending"></span>
                    <h2><?php echo htmlspecialchars($assigned_count); ?></h2>
                    <p>Assigned Tasks</p>
                </div>

                <div class="stat-card stat-card-resolved">
                    <span class="icon-placeholder icon-resolved"></span>
                    <h2><?php echo htmlspecialchars($completed_count); ?></h2>
                    <p>Completed Work</p>
                </div>

                <div class="stat-card stat-card-total">
                    <span class="icon-placeholder icon-total"></span>
                    <h2><?php echo htmlspecialchars($total_count); ?></h2>
                    <p>Total Assignments</p>
                </div>

            </section>

            <section class="actions-grid">
                
                <a href="?section=assigned" class="action-button register-button">
                    <span class="icon-main icon-add"></span>
                    <h3>View Assigned Work</h3>
                    <p>Check pending complaints and update status</p>
                </a>

                <a href="?section=completed" class="action-button view-button">
                    <span class="icon-main icon-view"></span>
                    <h3>View Work History</h3>
                    <p>See list of all resolved and closed tasks</p>
                </a>

            </section>

        <?php else: ?>
            <?php
                if ($section == 'assigned') {
                    include 'assigned_complaints_section.php';
                } elseif ($section == 'completed') {
                    include 'completed_work_section.php';
                } elseif ($section == 'notifications') {
                    include 'notifications_section.php';
                }
            ?>
        <?php endif; ?>

    </main>

</body>
</html>