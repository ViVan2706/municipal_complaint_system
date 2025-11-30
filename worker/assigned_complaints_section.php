<h1 class="page-title">Assigned Complaints</h1>

<?php
$sql = "SELECT c.complaint_id, c.category, c.severity, c.status, c.location, c.filed_date, 
               cit.name AS citizen_name, cit.phone_no AS citizen_phone
        FROM complaint c
        JOIN citizen cit ON c.citizen_id = cit.citizen_id
        WHERE c.worker_id = ? AND c.status IN ('pending', 'in_progress')
        ORDER BY c.filed_date DESC";

$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, "i", $worker_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        
        // CSS classes for badges
        $priority_class = strtolower($row['severity']); // low, medium, high, critical
        $status_class = str_replace('_', '-', strtolower($row['status']));

        $status_text = ucwords(str_replace('_', ' ', $row['status']));
?>

<div class="list-item">
    <div class="item-header">
        <div class="item-title">
            <?php echo htmlspecialchars($row['category']); ?> 
            <br><span style="font-size: 0.9rem; color: #6c757d; font-weight: normal;">ID: C<?php echo str_pad($row['complaint_id'], 3, '0', STR_PAD_LEFT); ?></span>
        </div>
        <div style="text-align: right;">
            <span class="item-status status-<?php echo $status_class; ?>"><?php echo $status_text; ?></span><br>
            <span class="priority-badge priority-<?php echo $priority_class; ?>"><?php echo htmlspecialchars($row['severity']); ?> Priority</span>
        </div>
    </div>
    <div class="item-details">
        <div class="item-details-row"><i class="fas fa-map-marker-alt"></i> <?php echo htmlspecialchars($row['location']); ?></div>
        <div class="item-details-row"><i class="fas fa-calendar-alt"></i> Filed: <?php echo date("F j, Y", strtotime($row['filed_date'])); ?></div>
        <div class="item-details-row"><i class="fas fa-user"></i> Citizen: <?php echo htmlspecialchars($row['citizen_name']); ?> (<?php echo htmlspecialchars($row['citizen_phone']); ?>)</div>
    </div>
    <button class="view-details-button">View Details</button>
</div>

<?php
    } 
} else {
    echo "<p style='color:#6c757d;'>You have no assigned complaints.</p>";
}
mysqli_stmt_close($stmt);
?>