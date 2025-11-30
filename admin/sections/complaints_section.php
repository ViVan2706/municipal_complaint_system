<?php

// session already started in admin_dashboard.php — do NOT start again
// session_start();  <-- DELETE THIS

// Always correct db path
include __DIR__ . "/../../db.php";

// Check admin login
if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../LoginAndSignup/login.html");
    exit();
}


// Fetch all complaints with citizen + worker
$sql = "
    SELECT c.*, 
           ct.name AS citizen_name,
           w.name AS worker_name
    FROM complaint c
    LEFT JOIN citizen ct ON c.citizen_id = ct.citizen_id
    LEFT JOIN worker w ON c.worker_id = w.worker_id
    ORDER BY c.filed_date DESC
";

$result = $conn->query($sql);
?>

<h1 class="page-title">Complaints</h1>
<p class="page-description">Manage all complaints registered in the system.</p>

<!-- Complaints Table -->
<div class="table-container">
<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Citizen</th>
            <th>Category</th>
            <th>Severity</th>
            <th>Status</th>
            <th>Worker</th>
            <th>Date Filed</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row["complaint_id"] ?></td>
                <td><?= htmlspecialchars($row["citizen_name"]) ?></td>
                <td><?= htmlspecialchars($row["category"]) ?></td>
                <td><?= ucfirst($row["severity"]) ?></td>
                <td><?= ucfirst($row["status"]) ?></td>
                <td><?= $row["worker_name"] ? $row["worker_name"] : "Unassigned" ?></td>
                <td><?= $row["filed_date"] ?></td>

                <td>
                    <!-- View Button -->
                    <a href="../admin_dashboard.php?section=view_complaint&id=<?= $row['complaint_id'] ?>"
                       class="btn-view">View</a>

                    <!-- Assign Worker -->
                    <a href="../admin_dashboard.php?section=assign_worker&id=<?= $row['complaint_id'] ?>"
                       class="btn-assign">Assign</a>

                    <!-- Status Buttons -->
                    <a href="update_status.php?id=<?= $row['complaint_id'] ?>&action=resolved" 
                       class="btn-status green">Resolve</a>

                    <a href="update_status.php?id=<?= $row['complaint_id'] ?>&action=rejected" 
                       class="btn-status red">Reject</a>

                    <a href="update_status.php?id=<?= $row['complaint_id'] ?>&action=closed" 
                       class="btn-status blue">Close</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<style>
.table-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: #0d47a1;
    color: white;
    padding: 10px;
}

.table td {
    padding: 8px;
    border-bottom: 1px solid #ddd;
}

.btn-view {
    background: #1565c0;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
}

.btn-assign {
    background: #6a1b9a;
    color: white;
    padding: 6px 12px;
    border-radius: 6px;
    text-decoration: none;
    font-size: 13px;
}

.btn-status {
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 12px;
    text-decoration: none;
    color: white;
}

.btn-status.green { background: #2e7d32; }
.btn-status.red { background: #c62828; }
.btn-status.blue { background: #0277bd; }

.page-title {
    font-size: 26px;
    color: #0d47a1;
    font-weight: 700;
}

.page-description {
    color: #444;
    margin-bottom: 15px;
}
</style>
