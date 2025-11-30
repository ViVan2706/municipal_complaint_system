<?php
// 1. Session Check
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Database Connection Check
include '../db.php';

// 3. Get Worker ID
$worker_id = $_SESSION['worker_id'] ?? 0;

// Get Notification count for badge
$sql_notif = "SELECT COUNT(*) as unread FROM worker_notification WHERE worker_id = ? AND status = 'unread'";
$stmt_n = $conn->prepare($sql_notif);
$stmt_n->bind_param("i", $worker_id);
$stmt_n->execute();
$notif_count = $stmt_n->get_result()->fetch_assoc()['unread'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Completed Work | MCCCTS</title>
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ✅ CSS FILE ATTACHED HERE ✅ -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- NAVBAR -->
    

    <!-- MAIN CONTENT -->
    <div class="container" style="margin-top: 30px;">
        <h1 class="page-title">Completed Work</h1>
        <p class="page-description">History of complaints you have successfully resolved.</p>

        <?php
        $sql = "SELECT c.complaint_id, c.category, c.status, c.location, c.filed_date, c.resolved_date,
                       cit.name AS citizen_name
                FROM complaint c
                JOIN citizen cit ON c.citizen_id = cit.citizen_id
                WHERE c.worker_id = ? AND c.status IN ('resolved', 'closed')
                ORDER BY c.resolved_date DESC";

        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
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
                <div>
                    <div class="item-title">
                        <?php echo htmlspecialchars($row['category']); ?> 
                    </div>
                    <small style="color: #6c757d;">ID: #<?php echo str_pad($row['complaint_id'], 3, '0', STR_PAD_LEFT); ?></small>
                </div>
                <span class="item-status status-<?php echo $status_class; ?>"><?php echo $status_text; ?></span>
            </div>

            <div class="item-details">
                <div class="item-details-row">
                    <i class="fas fa-map-marker-alt"></i> 
                    <strong>Location:</strong> &nbsp; <?php echo htmlspecialchars($row['location']); ?>
                </div>
                <div class="item-details-row">
                    <i class="fas fa-calendar-alt"></i> 
                    <span>
                        <strong>Filed:</strong> <?php echo date("M j", strtotime($row['filed_date'])); ?> 
                        &nbsp;|&nbsp; 
                        <strong>Resolved:</strong> <?php echo date("M j, Y", strtotime($row['resolved_date'])); ?>
                    </span>
                </div>
                <div class="item-details-row">
                    <i class="fas fa-user"></i> 
                    <strong>Citizen:</strong> &nbsp; <?php echo htmlspecialchars($row['citizen_name']); ?>
                </div>
            </div>

        </div>

        <?php
                } 
            } else {
                echo "<div style='text-align:center; padding: 50px; background: white; border-radius: 8px; border: 1px solid #dee2e6;'>
                        <i class='fas fa-clipboard-check' style='font-size: 3rem; color: #28a745; margin-bottom: 15px;'></i>
                        <h3>No Completed Work Yet</h3>
                        <p style='color: #6c757d;'>Complaints you resolve will appear here.</p>
                      </div>";
            }
            mysqli_stmt_close($stmt);
        } else {
            echo "<p style='color:red;'>Error preparing query: " . mysqli_error($conn) . "</p>";
        }
        ?>
    </div>

</body>
</html>