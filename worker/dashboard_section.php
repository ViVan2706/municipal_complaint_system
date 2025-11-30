<?php
$sql_assigned = "SELECT COUNT(*) AS count FROM complaint WHERE worker_id = ? AND status IN ('pending', 'in_progress')";
$stmt_assigned = mysqli_prepare($conn, $sql_assigned);
mysqli_stmt_bind_param($stmt_assigned, "i", $worker_id);
mysqli_stmt_execute($stmt_assigned);
$count_assigned = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_assigned))['count'];
mysqli_stmt_close($stmt_assigned);

$sql_progress = "SELECT COUNT(*) AS count FROM complaint WHERE worker_id = ? AND status = 'in_progress'";
$stmt_progress = mysqli_prepare($conn, $sql_progress);
mysqli_stmt_bind_param($stmt_progress, "i", $worker_id);
mysqli_stmt_execute($stmt_progress);
$count_progress = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_progress))['count'];
mysqli_stmt_close($stmt_progress);

$sql_completed = "SELECT COUNT(*) AS count FROM complaint WHERE worker_id = ? AND status IN ('resolved', 'closed')";
$stmt_completed = mysqli_prepare($conn, $sql_completed);
mysqli_stmt_bind_param($stmt_completed, "i", $worker_id);
mysqli_stmt_execute($stmt_completed);
$count_completed = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt_completed))['count'];
mysqli_stmt_close($stmt_completed);

?>

<h1 class="page-title">Welcome Back, <?php echo htmlspecialchars($worker_name); ?>!</h1>
<p class="page-description">Manage your assigned complaints and track progress</p>

<div class="dashboard-cards">
    <div class="card">
        <div class="value"><?php echo $count_assigned; ?></div>
        <div class="label">Pending Assigned</div>
    </div>
    <div class="card completed">
        <div class="value"><?php echo $count_completed; ?></div>
        <div class="label">Resolved Complaints</div>
    </div>
    <div class="card in-progress">
        <div class="value"><?php echo $count_progress; ?></div>
        <div class="label">In Progress</div>
    </div>
</div>

<div style="display: flex; gap: 20px;">
    <a href="worker_dashboard.php?section=assigned" class="card" style="text-decoration: none; border-left: 5px solid var(--primary-blue); cursor: pointer;">
        <div style="color: var(--primary-blue); font-size: 1.5rem; margin-bottom: 10px;">+</div>
        <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-dark);">View Assigned Complaints</div>
        <div class="label">Check tasks assigned to you</div>
    </a>
    
    <a href="worker_dashboard.php?section=completed" class="card" style="text-decoration: none; border-left: 5px solid var(--success-green); cursor: pointer;">
        <div style="color: var(--success-green); font-size: 1.5rem; margin-bottom: 10px;">📄</div>
        <div style="font-weight: 700; font-size: 1.1rem; color: var(--text-dark);">View Completed Work</div>
        <div class="label">Track your resolved history</div>
    </a>
</div>