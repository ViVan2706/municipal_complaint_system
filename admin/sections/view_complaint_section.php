<?php
// This section runs inside admin_dashboard.php
// Admin login is already verified, $conn is available

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

$sql = "
    SELECT c.*, 
           ct.name AS citizen_name, 
           w.name AS worker_name
    FROM complaint c
    LEFT JOIN citizen ct ON c.citizen_id = ct.citizen_id
    LEFT JOIN worker w ON c.worker_id = w.worker_id
    $where
    ORDER BY c.filed_date DESC
";

$res = $conn->query($sql);
?>

<h1 class="page-title">Complaints</h1>
<p class="page-description">Manage, assign, and update complaint statuses</p>

<!-- FILTERS -->
<form method="GET" class="filter-bar">
    <input type="hidden" name="section" value="complaints">

    <input type="text" name="search" class="filter-input"
           placeholder="Search by ID / Name / Category"
           value="<?= htmlspecialchars($search) ?>">

    <select name="status" class="filter-select">
        <option value="">All Status</option>
        <option value="pending"      <?= $statusFilter=='pending' ? 'selected':'' ?>>Pending</option>
        <option value="in_progress"  <?= $statusFilter=='in_progress' ? 'selected':'' ?>>In Progress</option>
        <option value="resolved"     <?= $statusFilter=='resolved' ? 'selected':'' ?>>Resolved</option>
        <option value="closed"       <?= $statusFilter=='closed' ? 'selected':'' ?>>Closed</option>
        <option value="rejected"     <?= $statusFilter=='rejected' ? 'selected':'' ?>>Rejected</option>
    </select>

    <button class="filter-button">Apply</button>
</form>

<!-- COMPLAINT LIST -->
<div class="list-container">
<?php if ($res->num_rows > 0): ?>
    <?php while ($row = $res->fetch_assoc()): ?>

    <div class="list-item">
        <div class="item-header">
            <div class="item-title">
                <i class="fas fa-file-alt"></i>
                <span><?= htmlspecialchars($row['category']) ?></span>
            </div>
            <span class="item-status status-medium-priority">
                <?= ucfirst(str_replace('_', ' ', $row['status'])) ?>
            </span>
        </div>

        <div class="item-details item-details-full-width">
            <div class="item-details-row"><i class="fas fa-user"></i> Citizen: <?= htmlspecialchars($row['citizen_name']) ?></div>
            <div class="item-details-row"><i class="fas fa-bolt"></i> Severity: <?= $row['severity'] ?></div>
            <div class="item-details-row"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['location']) ?></div>
            <div class="item-details-row"><i class="fas fa-calendar"></i> Filed: <?= $row['filed_date'] ?></div>
            <div class="item-details-row"><i class="fas fa-user-cog"></i> Worker: <?= $row['worker_name'] ?? 'Unassigned' ?></div>
        </div>

        <div class="assigned-actions">
            <a href="admin_dashboard.php?section=view_complaint&id=<?= $row['complaint_id'] ?>" class="view-details-button">View</a>

            <?php if ($row['status'] == 'pending'): ?>
                <a href="admin_dashboard.php?section=assign_worker&id=<?= $row['complaint_id'] ?>"
                   class="view-details-button" style="background:#28a745;">Assign Worker</a>
            <?php endif; ?>
        </div>
    </div>

    <?php endwhile; ?>

<?php else: ?>
    <p>No complaints found.</p>
<?php endif; ?>
</div>
