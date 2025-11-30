<?php
include "../db.php";

$queries = [
    "total" => "SELECT COUNT(*) AS cnt FROM complaint",
    "pending" => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='pending'",
    "in_progress" => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='in_progress'",
    "resolved" => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='resolved'",
    "closed" => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='closed'",
    "rejected" => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='rejected'"
];

$stats = [];

foreach ($queries as $key => $sql) {
    $result = $conn->query($sql);
    $stats[$key] = $result->fetch_assoc()['cnt'] ?? 0;
}
?>

<h1>Dashboard</h1>
<p>Welcome, <?= $_SESSION['admin_name'] ?? $_SESSION['name'] ?? "Admin" ?>!</p>


<div class="dashboard-cards">
    <div class="card"><h3>Total Complaints</h3><p><?= $stats['total'] ?></p></div>
    <div class="card"><h3>Pending</h3><p><?= $stats['pending'] ?></p></div>
    <div class="card"><h3>In Progress</h3><p><?= $stats['in_progress'] ?></p></div>
    <div class="card"><h3>Resolved</h3><p><?= $stats['resolved'] ?></p></div>
    <div class="card"><h3>Closed</h3><p><?= $stats['closed'] ?></p></div>
    <div class="card"><h3>Rejected</h3><p><?= $stats['rejected'] ?></p></div>
</div>
