<?php
session_start();
include '../db.php'; 

if (!isset($_SESSION['citizen_id'])) {
    header("Location: LoginAndSignup/login.html");
    exit();
}

$logged_in_citizen_id = $_SESSION['citizen_id'];

$name_sql = "SELECT name FROM citizen WHERE citizen_id = ?";
$name_stmt = $conn->prepare($name_sql);
$name_stmt->bind_param("i", $logged_in_citizen_id);
$name_stmt->execute();
$name_result = $name_stmt->get_result();

if ($name_row = $name_result->fetch_assoc()) {
    $citizen_name = $name_row['name'];
} else {
    $citizen_name = "User";
}
$name_stmt->close();

$sql = "
    SELECT 
        (SELECT COUNT(*) FROM complaint WHERE citizen_id = ? AND status IN ('pending', 'in_progress')) AS pending_count,
        (SELECT COUNT(*) FROM complaint WHERE citizen_id = ? AND status = 'resolved') AS resolved_count,
        (SELECT COUNT(*) FROM complaint WHERE citizen_id = ?) AS total_count,
        (SELECT COUNT(*) FROM citizen_notification WHERE citizen_id = ? AND status = 'unread') AS unread_notifications
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iiii", 
    $logged_in_citizen_id, 
    $logged_in_citizen_id, 
    $logged_in_citizen_id, 
    $logged_in_citizen_id
);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

$pending_count = $data['pending_count'] ?? 0;
$resolved_count = $data['resolved_count'] ?? 0;
$total_count = $data['total_count'] ?? 0;
$notification_count = $data['unread_notifications'] ?? 0;

$stmt->close();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Citizen-Home</title>
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
        .icon-register::before { content: '+'; font-weight: bold; }
        .icon-my-complaints::before { content: '📄'; }
        .icon-notifications::before { content: '🔔'; }
        .icon-profile::before { content: '👤'; }
        .icon-add::before { content: '+'; }
        .icon-view::before { content: '📄'; }
    </style>
</head>
<body>
    <header class="header">
        <a href="#" class="logo">MCCCTS</a>
        <nav class="nav-links">
            <a href="#" class="nav-link"><span class="icon icon-register"></span> Register Complaint</a>
            <a href="MyComplaints/MyComplaintsPend.php" class="nav-link"><span class="icon icon-my-complaints"></span> My Complaints</a>
            <a href="#" class="nav-link">
                <span class="icon icon-notifications"></span> Notifications 
                <?php if ($notification_count > 0): ?>
                    <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
                <?php endif; ?>
            </a>
            <a href="#" class="nav-link"><span class="icon icon-profile"></span> Profile</a>
        </nav>
    </header>
    <main class="dashboard-container">
        <section class="welcome-section">
            <h1>Welcome Back, <?php echo htmlspecialchars($citizen_name); ?>!</h1>
            <p>Manage your complaints and track their progress</p>
        </section>
        <section class="stats-grid">
            <div class="stat-card stat-card-pending">
                <span class="icon-placeholder icon-pending"></span>
                <h2><?php echo htmlspecialchars($pending_count); ?></h2>
                <p>Pending Complaints</p>
            </div>
            <div class="stat-card stat-card-resolved">
                <span class="icon-placeholder icon-resolved"></span>
                <h2><?php echo htmlspecialchars($resolved_count); ?></h2>
                <p>Resolved Complaints</p>
            </div>
            <div class="stat-card stat-card-total">
                <span class="icon-placeholder icon-total"></span>
                <h2><?php echo htmlspecialchars($total_count); ?></h2>
                <p>Total Complaints</p>
            </div>
        </section>
        <section class="actions-grid">
            <a href="#" class="action-button register-button">
                <span class="icon-main icon-add"></span>
                <h3>Register New Complaint</h3>
                <p>Submit a new complaint for municipal issues</p>
            </a>
            <a href="MyComplaints/MyComplaintsPend.php" class="action-button view-button">
                <span class="icon-main icon-view"></span>
                <h3>View My Complaints</h3>
                <p>Track the status of your submitted complaints</p>
            </a>
        </section>
    </main>
</body>
</html>