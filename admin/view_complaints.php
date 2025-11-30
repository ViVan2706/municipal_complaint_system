<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// filters
$statusFilter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

$where = "WHERE 1=1";

if ($statusFilter) {
    $where .= " AND c.status='" . $conn->real_escape_string($statusFilter) . "'";
}

if ($search) {
    $s = $conn->real_escape_string($search);
    $where .= " AND (c.complaint_id='$s' OR ct.name LIKE '%$s%' OR c.category LIKE '%$s%')";
}

// query
$sql = "SELECT 
            c.*, 
            ct.name AS citizen_name, 
            w.name AS worker_name,
            a.name AS admin_name
        FROM complaint c
        LEFT JOIN citizen ct ON c.citizen_id = ct.citizen_id
        LEFT JOIN worker w ON c.worker_id = w.worker_id
        LEFT JOIN admin a ON c.admin_id = a.admin_id
        $where
        ORDER BY c.filed_date DESC";

$res = $conn->query($sql);

require "admin_header.php";
?>

<h3>All Complaints</h3>

<!-- Filter/Search Bar -->
<form class="row g-2 mb-3">
    <div class="col-auto">
        <input name="search" value="<?= htmlspecialchars($search) ?>" 
               class="form-control" placeholder="Search ID / Name / Category">
    </div>

    <div class="col-auto">
        <select name="status" class="form-select">
            <option value="">All statuses</option>
            <option value="pending"      <?= $statusFilter=='pending' ? 'selected' : '' ?>>Pending</option>
            <option value="in_progress"  <?= $statusFilter=='in_progress' ? 'selected' : '' ?>>In Progress</option>
            <option value="resolved"     <?= $statusFilter=='resolved' ? 'selected' : '' ?>>Resolved</option>
            <option value="closed"       <?= $statusFilter=='closed' ? 'selected' : '' ?>>Closed</option>
            <option value="rejected"     <?= $statusFilter=='rejected' ? 'selected' : '' ?>>Rejected</option>
        </select>
    </div>

    <div class="col-auto">
        <button class="btn btn-secondary">Filter</button>
    </div>
</form>

<!-- Complaints Table -->
<div class="table-responsive">
<table class="table table-bordered table-sm">
    <thead class="table-dark">
        <tr>
            <th>ID</th>
            <th>Citizen</th>
            <th>Category</th>
            <th>Severity</th>
            <th>Status</th>
            <th>Worker</th>
            <th>Filed Date</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
    <?php while ($row = $res->fetch_assoc()): ?>
        <tr>
            <td><?= $row['complaint_id'] ?></td>
            <td><?= htmlspecialchars($row['citizen_name']) ?></td>
            <td><?= htmlspecialchars($row['category']) ?></td>
            <td><?= $row['severity'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= $row['worker_name'] ?? 'Unassigned' ?></td>
            <td><?= $row['filed_date'] ?></td>

            <td>
                <a href="view_complaints.php?view=<?= $row['complaint_id'] ?>" class="btn btn-sm btn-primary">View</a>
                <a href="assign_worker.php?complaint_id=<?= $row['complaint_id'] ?>" class="btn btn-sm btn-success">Assign</a>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
</div>

<?php
// DETAILED VIEW OF SINGLE COMPLAINT
if (isset($_GET['view'])) {
    $id = (int) $_GET['view'];

    $stmt = $conn->prepare("
        SELECT 
            c.*, 
            ct.name AS citizen_name, 
            ct.email AS citizen_email,
            w.name AS worker_name,
            a.name AS admin_name
        FROM complaint c
        LEFT JOIN citizen ct ON c.citizen_id = ct.citizen_id
        LEFT JOIN worker w ON c.worker_id = w.worker_id
        LEFT JOIN admin a ON c.admin_id = a.admin_id
        WHERE c.complaint_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $detail = $stmt->get_result()->fetch_assoc();

    if ($detail):
?>

<hr>
<h4>Complaint #<?= $de
