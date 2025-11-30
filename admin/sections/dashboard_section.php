<?php
// Ensure DB is loaded
if (!isset($conn)) {
    include __DIR__ . "/../../db.php";
}

$admin_name = $_SESSION['admin_name'] ?? "Admin";

// Fetch complaint stats
$stats = [];
$queries = [
    "total"        => "SELECT COUNT(*) AS cnt FROM complaint",
    "pending"      => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='pending'",
    "in_progress"  => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='in_progress'",
    "resolved"     => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='resolved'",
    "closed"       => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='closed'",
    "rejected"     => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='rejected'"
];

foreach ($queries as $key => $sql) {
    $stats[$key] = ($conn->query($sql)->fetch_assoc()['cnt']) ?? 0;
}
?>

<h1 class="dash-title">Dashboard</h1>
<p class="dash-welcome">Welcome, <b><?= htmlspecialchars($admin_name) ?></b>!</p>

<div class="card-grid">

    <div class="stat-card blue">
        <i class="fas fa-list-check icon"></i>
        <h3>Total Complaints</h3>
        <p><?= $stats['total'] ?></p>
    </div>

    <div class="stat-card orange">
        <i class="fas fa-hourglass-half icon"></i>
        <h3>Pending</h3>
        <p><?= $stats['pending'] ?></p>
    </div>

    <div class="stat-card purple">
        <i class="fas fa-spinner icon"></i>
        <h3>In Progress</h3>
        <p><?= $stats['in_progress'] ?></p>
    </div>

    <div class="stat-card green">
        <i class="fas fa-check-circle icon"></i>
        <h3>Resolved</h3>
        <p><?= $stats['resolved'] ?></p>
    </div>

    <div class="stat-card gray">
        <i class="fas fa-folder-minus icon"></i>
        <h3>Closed</h3>
        <p><?= $stats['closed'] ?></p>
    </div>

    <div class="stat-card red">
        <i class="fas fa-times-circle icon"></i>
        <h3>Rejected</h3>
        <p><?= $stats['rejected'] ?></p>
    </div>

</div>

<style>
.dash-title {
    font-size: 32px;
    font-weight: 700;
    color: #0d47a1;
}

.dash-welcome {
    color: #555;
    margin-bottom: 30px;
}

.card-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
    gap: 20px;
}

/* Cards */
.stat-card {
    padding: 20px;
    border-radius: 10px;
    color: white;
    box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    text-align: center;
}

.stat-card h3 {
    margin: 10px 0 5px;
}

.stat-card p {
    font-size: 28px;
    font-weight: 700;
}

/* Colors */
.stat-card.blue { background: #1976d2; }
.stat-card.orange { background: #fb8c00; }
.stat-card.purple { background: #8e24aa; }
.stat-card.green { background: #2e7d32; }
.stat-card.red { background: #c62828; }
.stat-card.gray { background: #546e7a; }

/* Icons */
.icon {
    font-size: 32px;
    opacity: 0.9;
}
</style>
