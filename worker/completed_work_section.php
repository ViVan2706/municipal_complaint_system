<h1 class="page-title">Completed Work</h1>

<?php
// This file assumes $conn and $worker_id are available from worker_dashboard.php

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
        
        // Format status text
        $status_text = ucwords(str_replace('_', ' ', $row['status']));

?>

<div class="list-item">
    <div class="item-header">
        <div class="item-title"><i class="fas fa-check-circle"></i> <span><?php echo htmlspecialchars($row['category']); ?></span> <br> ID: C<?php echo str_pad($row['complaint_id'], 3, '0', STR_PAD_LEFT); ?></div>
        <span class="item-status status-resolved"><?php echo $status_text; ?></span>
    </div>
    <div class="item-details">
        <div class="item-details-row item-details-full-width"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></div>
        <div class="item-details-row item-details-full-width">
            <i class="fas fa-calendar-alt"></i> Filed: <?php echo date("Y-m-d", strtotime($row['filed_date'])); ?> | Resolved: <?php echo date("Y-m-d", strtotime($row['resolved_date'])); ?>
        </div>
        <div class="item-details-row item-details-full-width"><i class="fas fa-user"></i> Citizen: <?php echo htmlspecialchars($row['citizen_name']); ?></div>
    </div>
</div>

<?php
    } // end while
} else {
    echo "<p>You have no completed work.</p>";
}
mysqli_stmt_close($stmt);
?>