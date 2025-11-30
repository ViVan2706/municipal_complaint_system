<h1 class="page-title">Completed Work</h1>

<?php
$sql = "SELECT c.complaint_id, c.category, c.status, c.location, c.filed_date, c.resolved_date,
               cit.name AS citizen_name
        FROM complaint c
        JOIN citizen cit ON c.citizen_id = cit.citizen_id
        WHERE c.worker_id = ? AND c.status IN ('resolved', 'closed')
        ORDER BY c.resolved_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $status_text = ucwords(str_replace('_', ' ', $row['status']));
        $status_class = str_replace('_', '-', strtolower($row['status']));
?>

<div class="list-item">
    <div class="item-header">
        <div class="item-title">
            <?php echo htmlspecialchars($row['category']); ?> 
            <br><span style="font-size: 0.9rem; color: #6c757d; font-weight: normal;">ID: C<?php echo str_pad($row['complaint_id'], 3, '0', STR_PAD_LEFT); ?></span>
        </div>
        <span class="item-status status-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
    </div>
    <div class="item-details">
        <div class="item-details-row"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></div>
        <div class="item-details-row">
            <i class="fas fa-calendar-alt"></i> Filed: <?php echo date("M j", strtotime($row['filed_date'])); ?> | Resolved: <?php echo date("M j, Y", strtotime($row['resolved_date'])); ?>
        </div>
        <div class="item-details-row"><i class="fas fa-user"></i> Citizen: <?php echo htmlspecialchars($row['citizen_name']); ?></div>
    </div>
    <button class="view-details-button">View Details</button>
</div>

<?php
    } 
} else {
    echo "<p style='color:#6c757d;'>You have no completed work.</p>";
}
mysqli_stmt_close($stmt);
?>