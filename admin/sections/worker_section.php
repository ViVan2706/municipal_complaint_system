<?php
// This file is included inside admin_dashboard.php
// Admin session & $conn are already available

$sql = "
    SELECT 
        w.worker_id,
        w.name,
        w.email,
        w.phone_no,
        w.department,
        w.availability_status,
        s.name AS supervisor_name
    FROM worker w
    LEFT JOIN worker s 
        ON w.supervisor_worker_id = s.worker_id
    ORDER BY w.department, w.name
";

$res = $conn->query($sql);
?>

<h1 class="page-title">Workers List</h1>
<p class="page-description">All workers across Civil, Electrical, and Water departments</p>

<div class="list-container">

<?php if ($res && $res->num_rows > 0): ?>
    <?php while ($row = $res->fetch_assoc()): ?>

    <div class="list-item">
        <div class="item-header">
            <div class="item-title">
                <i class="fas fa-hard-hat"></i>
                <span><?= htmlspecialchars($row['name']) ?></span>
            </div>

            <!-- Availability Badge -->
            <span class="item-status 
                <?= $row['availability_status'] == 'available' ? 'status-low-priority' : '' ?>
                <?= $row['availability_status'] == 'busy' ? 'status-medium-priority' : '' ?>
                <?= $row['availability_status'] == 'offline' ? 'status-high-priority' : '' ?>">
                <?= ucfirst($row['availability_status']) ?>
            </span>
        </div>

        <div class="item-details item-details-full-width">
            <div class="item-details-row">
                <i class="fas fa-building"></i> Department: <?= htmlspecialchars($row['department']) ?>
            </div>

            <div class="item-details-row">
                <i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email']) ?>
            </div>

            <div class="item-details-row">
                <i class="fas fa-phone"></i> <?= htmlspecialchars($row['phone_no']) ?>
            </div>

            <div class="item-details-row">
                <i class="fas fa-user-tie"></i> Supervisor: 
                <?= htmlspecialchars($row['supervisor_name'] ?? 'None') ?>
            </div>
        </div>
    </div>

    <?php endwhile; ?>
<?php else: ?>
    <p>No workers found.</p>
<?php endif; ?>

</div>
