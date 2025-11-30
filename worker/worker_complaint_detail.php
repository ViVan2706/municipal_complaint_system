<?php
session_start();
include '../db.php';

if (!isset($_SESSION['worker_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$worker_id = $_SESSION['worker_id'];
$message = "";
$error = "";

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("<div class='container' style='margin-top:50px;'><h3>Error: Invalid ID.</h3><a href='worker_dashboard.php'>Return Home</a></div>");
}

$complaint_id = intval($_GET['id']);


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_status'])) {
    $new_status = $_POST['status'];
    
    $update_sql = "UPDATE complaint SET status = ?";
    if ($new_status == 'resolved') {
        $update_sql .= ", resolved_date = NOW()";
    }
    $update_sql .= " WHERE complaint_id = ? AND worker_id = ?";
    
    $stmt = $conn->prepare($update_sql);
    $stmt->bind_param("sii", $new_status, $complaint_id, $worker_id);
    
    if ($stmt->execute()) {
        $message = "Status updated successfully to " . ucwords(str_replace('_', ' ', $new_status));
        
        $cit_sql = "SELECT citizen_id, category FROM complaint WHERE complaint_id = ?";
        $cit_stmt = $conn->prepare($cit_sql);
        $cit_stmt->bind_param("i", $complaint_id);
        $cit_stmt->execute();
        $cit_res = $cit_stmt->get_result()->fetch_assoc();
        
        if ($cit_res) {
            $citizen_id = $cit_res['citizen_id'];
            $category = $cit_res['category'];
            $notif_msg = "Status update for complaint '$category': " . ucwords(str_replace('_', ' ', $new_status));
            
            $n_stmt = $conn->prepare("INSERT INTO citizen_notification (citizen_id, complaint_id, message, date_time, status) VALUES (?, ?, ?, NOW(), 'unread')");
            $n_stmt->bind_param("iis", $citizen_id, $complaint_id, $notif_msg);
            $n_stmt->execute();
        }
    } else {
        $error = "Failed to update status.";
    }
}

$sql = "SELECT c.*, cit.name AS citizen_name, cit.phone_no AS citizen_phone, cit.email AS citizen_email
        FROM complaint c
        JOIN citizen cit ON c.citizen_id = cit.citizen_id
        WHERE c.complaint_id = ? AND c.worker_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $complaint_id, $worker_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("<div class='container'><h3>Complaint not found or not assigned to you.</h3><a href='worker_dashboard.php'>Return</a></div>");
}

$row = $result->fetch_assoc();
$status_class = str_replace('_', '-', strtolower($row['status']));
$priority_class = strtolower($row['severity']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complaint Details | MCCCTS Worker</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        .details-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-bottom: 30px; }
        .section-heading { font-size: 1.1rem; color: var(--primary-blue); border-bottom: 2px solid #f0f2f5; padding-bottom: 10px; margin-bottom: 20px; font-weight: 600; }
        .info-label { font-size: 0.9rem; color: var(--text-muted); font-weight: 600; margin-bottom: 4px; }
        .info-value { font-size: 1rem; color: var(--text-dark); margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
        .description-box { background-color: #f8f9fa; border: 1px solid var(--border-color); border-radius: 6px; padding: 15px; color: var(--text-dark); line-height: 1.6; }
    </style>
</head>
<body>
    <header class="header">
        <a href="worker_dashboard.php" class="logo"><i class="fas fa-hard-hat"></i> MCCCTS - Worker</a>
        <nav class="nav-links">
            <a href="worker_dashboard.php" class="nav-link">Home</a>
            <a href="worker_dashboard.php?section=assigned" class="nav-link">Assigned</a>
            <a href="worker_dashboard.php?section=completed" class="nav-link">Completed</a>
            <a href="profile.php" class="nav-link">Profile</a>
        </nav>
    </header>
    <main class="container">
        <div style="display: flex; margin-bottom: 20px;">
            <a href="worker_dashboard.php?section=assigned" class="btn-outline"><i class="fas fa-arrow-left"></i> Back to List</a>
        </div>

        <?php if ($message): ?>
            <div style="padding: 15px; background: #d1e7dd; color: #0f5132; border-radius: 6px; margin-bottom: 20px; border: 1px solid #badbcc;">
                <i class="fas fa-check-circle"></i> <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <div class="form-card" style="max-width: 900px;">
            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 30px;">
                <div>
                    <h1 class="page-title" style="margin-bottom: 5px;"><?php echo htmlspecialchars($row['category']); ?></h1>
                    <span style="color: var(--text-muted);">ID: C<?php echo str_pad($row['complaint_id'], 3, '0', STR_PAD_LEFT); ?></span>
                </div>
                <div style="text-align: right;">
                    <span class="item-status status-<?php echo $status_class; ?>" style="font-size: 0.9rem; padding: 8px 16px;"><?php echo ucwords(str_replace('_', ' ', $row['status'])); ?></span>
                    <div style="margin-top: 10px;">
                        <span class="priority-badge priority-<?php echo $priority_class; ?>"><?php echo htmlspecialchars($row['severity']); ?> Priority</span>
                    </div>
                </div>
            </div>

            <div class="details-grid">
                <div>
                    <div class="section-heading">General Information</div>
                    <div class="info-label">Location</div>
                    <div class="info-value"><i class="fas fa-map-marker-alt" style="color: var(--primary-blue);"></i> <?php echo htmlspecialchars($row['location']); ?></div>
                    <div class="info-label">Filed Date</div>
                    <div class="info-value"><i class="fas fa-calendar-alt" style="color: var(--primary-blue);"></i> <?php echo date("F j, Y, g:i a", strtotime($row['filed_date'])); ?></div>
                </div>
                <div>
                    <div class="section-heading">Citizen Details</div>
                    <div class="info-label">Name</div>
                    <div class="info-value"><i class="fas fa-user" style="color: var(--text-muted);"></i> <?php echo htmlspecialchars($row['citizen_name']); ?></div>
                    <div class="info-label">Phone Number</div>
                    <div class="info-value"><i class="fas fa-phone" style="color: var(--text-muted);"></i> <?php echo htmlspecialchars($row['citizen_phone']); ?></div>
                </div>
            </div>

            <div style="margin-bottom: 30px;">
                <div class="section-heading">Full Description</div>
                <div class="description-box"><?php echo nl2br(htmlspecialchars($row['description'])); ?></div>
            </div>

            <div style="background-color: #eef2ff; padding: 25px; border-radius: 8px; border: 1px solid #dae0f5;">
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 15px;">Update Work Status</h3>
                <form method="POST">
                    <div class="form-group">
                        <label for="status">Select New Status</label>
                        <select name="status" id="status" style="background-color: white;">
                            <option value="pending" <?php if($row['status'] == 'pending') echo 'selected'; ?>>Pending</option>
                            <option value="in_progress" <?php if($row['status'] == 'in_progress') echo 'selected'; ?>>In Progress</option>
                            <option value="resolved" <?php if($row['status'] == 'resolved') echo 'selected'; ?>>Resolved</option>
                            <option value="rejected" <?php if($row['status'] == 'rejected') echo 'selected'; ?>>Rejected (Cannot Complete)</option>
                        </select>
                    </div>
                    <button type="submit" name="update_status" class="btn-primary" style="width: auto; padding-left: 30px; padding-right: 30px;">Update Status</button>
                </form>
            </div>
        </div>
    </main>
</body>
</html>