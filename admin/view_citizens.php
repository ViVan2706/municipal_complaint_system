<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch all citizens
$sql = "SELECT * FROM citizen ORDER BY date_registered DESC";
$res = $conn->query($sql);

require "admin_header.php";
?>

<h3>Citizens List</h3>

<div class="table-responsive mt-3">
<table class="table table-bordered table-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Phone</th>
            <th>Date Registered</th>
        </tr>
    </thead>

    <tbody>
        <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $row['citizen_id'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone_no']) ?></td>
            <td><?= $row['date_registered'] ?></td>
        </tr>
        <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php require "admin_footer.php"; ?>
