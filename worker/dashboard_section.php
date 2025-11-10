<?php
// This file assumes $conn and $worker_id are available from worker_dashboard.php

// 1. Get Assigned Complaints Count
$sql_assigned = "SELECT COUNT(*) AS count FROM complaint WHERE worker_id = ? AND status IN ('pending', 'in_progress')";
$stmt_assigned = mysqli_prepare($conn, $sql_assigned);
mysqli_stmt_bind_param($stmt_assigned, "i", $worker_id);
mysqli_stmt_execute($stmt_assigned);
$count_assigned = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_assigned))['count'];
mysqli_stmt_close($stmt_assigned);

// 2. Get In-Progress Complaints Count
$sql_progress = "SELECT COUNT(*) AS count FROM complaint WHERE worker_id = ? AND status = 'in_progress'";
$stmt_progress = mysqli_prepare($conn, $sql_progress);
mysqli_stmt_bind_param($stmt_progress, "i", $worker_id);
mysqli_stmt_execute($stmt_progress);
$count_progress = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_progress))['count'];
mysqli_stmt_close($stmt_progress);

// 3. Get Completed Complaints Count
$sql_completed = "SELECT COUNT(*) AS count FROM complaint WHERE worker_id = ? AND status IN ('resolved', 'closed')";
$stmt_completed = mysqli_prepare($conn, $sql_completed);
mysqli_stmt_bind_param($stmt_completed, "i", $worker_id);
mysqli_stmt_execute($stmt_completed);
$count_completed = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_completed))['count'];
mysqli_stmt_close($stmt_completed);

?>

<h1 class="page-title">Welcome, <?php echo htmlspecialchars($worker_name); ?>!</h1>
<p class="page-description">Manage and resolve assigned complaints</p>

<div class="dashboard-cards">
    <div class="card">
        <i class="fas fa-clock icon"></i>
        <div class="value"><?php echo $count_assigned; ?></div>
        <div class="label">Assigned Complaints</div>
    </div>
    <div class="card in-progress">
        <i class="fas fa-clipboard-list icon"></i>
        <div class="value"><?php echo $count_progress; ?></div>
        <div class="label">In Progress</div>
    </div>
    <div class="card completed">
        <i class="fas fa-check-circle icon"></i>
        <div class="value"><?php echo $count_completed; ?></div>
        <div class="label">Completed</div>
    </div>
</div>

<div class="action-buttons">
    <a href="worker_dashboard.php?section=assigned" class="action-button">
        <i class="fas fa-clipboard-list"></i> 
        <div class="action-button-content">
            <span class="action-button-title">View Assigned Complaints</span>
            <span class="action-button-subtitle">Manage complaints assigned to you</span>
        </div>
    </a>
    <a href="worker_dashboard.php?section=completed" class="action-button completed-work">
        <i class="fas fa-check-circle"></i> 
        <div class="action-button-content">
            <span class="action-button-title">Completed Work</span>
            <span class="action-button-subtitle">Review your resolved complaints</span>
        </div>
    </a>
</div>