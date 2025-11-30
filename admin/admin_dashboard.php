<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch statistics
$stats = [];
$queries = [
    "total"        => "SELECT COUNT(*) AS cnt FROM complaint",
    "pending"      => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='pending'",
    "in_progress"  => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='in_progress'",
    "resolved"     => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='resolved'",
    "closed"       => "SELECT COUNT(*) AS cnt FROM complaint WHERE status='closed'"
];

foreach ($queries as $key => $sql) {
    $result = $conn->query($sql);
    $stats[$key] = $result->fetch_assoc()['cnt'] ?? 0;
}

require "admin_header.php";
?>

<h2>Welcome, <?= htmlspecialchars($_SESSION['admin_name']) ?></h2>

<div class="row mt-4">

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>Total Complaints</h5>
            <h3><?= $stats['total'] ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>Pending</h5>
            <h3><?= $stats['pending'] ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>In Progress</h5>
            <h3><?= $stats['in_progress'] ?></h3>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card p-3 text-center">
            <h5>Resolved</h5>
            <h3><?= $stats['resolved'] ?></h3>
        </div>
    </div>

</div>

<hr class="mt-4">

<h4>Recent Complaints</h4>

<div class="table-responsive">
    <table class="table table-bordered table-sm">
        <thead class="table-dark">
            <tr>
                <th>ID</th>
                <th>Citizen</th>
                <th>Category</th>
                <th>Severity</th>
                <th>Status</th>
                <th>Filed Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php
        $sql = "SELECT c.*, ct.name AS citizen_name 
                FROM complaint c
                LEFT JOIN citizen ct 
                ON c.citizen_id = ct.citizen_id
                ORDER BY filed_date DESC
                LIMIT 8";

        $res = $conn->query($sql);

        while ($row = $res->fetch_assoc()):
        ?>
            <tr>
                <td><?= $row['complaint_id'] ?></td>
                <td><?= htmlspecialchars($row['citizen_name']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td><?= $row['severity'] ?></td>
                <td><?= $row['status'] ?></td>
                <td><?= $row['filed_date'] ?></td>
                <td>
                    <a href="view_complaints.php?view=<?= $row['complaint_id'] ?>" class="btn btn-sm btn-primary">View</a>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php require "admin_footer.php"; ?>
