<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch workers + their supervisors
$sql = "
    SELECT 
        w.*, 
        s.name AS supervisor_name
    FROM worker w
    LEFT JOIN worker s 
        ON w.supervisor_worker_id = s.worker_id
    ORDER BY w.department, w.name
";

$res = $conn->query($sql);

require "admin_header.php";
?>

<h3>Workers List</h3>

<div class="table-responsive mt-3">
<table class="table table-bordered table-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Department</th>
            <th>Phone</th>
            <th>Availability</th>
            <th>Supervisor</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $row['worker_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['department']) ?></td>
            <td><?= htmlspecialchars($row['phone_no']) ?></td>
            <td><?= $row['availability_status'] ?></td>
            <td><?= htmlspecialchars($row['supervisor_name'] ?? "") ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php require "admin_footer.php"; ?>
