<?php
// 1. Session Check: Start only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. Database Connection Check
if (!isset($conn)) {
    if (file_exists('../db.php')) {
        include '../db.php';
    } elseif (file_exists('../../db.php')) {
        include '../../db.php';
    } else {
        die("Database connection file not found.");
    }
}

// 3. Get Worker ID from Session
$worker_id = $_SESSION['worker_id'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assigned Complaints</title>
    
    <!-- FontAwesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- ✅ THIS IS THE LINE TO ATTACH YOUR CSS ✅ -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <div class="container" style="margin-top: 30px;">
        <h1 class="page-title">Assigned Complaints</h1>
        <p class="page-description">View and manage complaints currently assigned to you.</p>

        <?php
        // 4. SQL QUERY
        $sql = "SELECT c.complaint_id, c.category, c.severity, c.status, c.location, c.filed_date, 
                       cit.name AS citizen_name, cit.phone_no AS citizen_phone
                FROM complaint c
                JOIN citizen cit ON c.citizen_id = cit.citizen_id
                WHERE c.worker_id = ? AND c.status IN ('pending', 'in_progress')
                ORDER BY c.filed_date DESC";

        $stmt = mysqli_prepare($conn, $sql);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $worker_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    
                    // 5. CSS Class Logic
                    $status_class = 'status-' . str_replace('_', '-', strtolower($row['status'])); 
                    $status_text = ucwords(str_replace('_', ' ', $row['status']));
                    $priority_class = 'priority-' . strtolower($row['severity']);
        ?>

            <div class="list-item">
                <div class="item-header">
                    <div>
                        <div class="item-title">
                            <?php echo htmlspecialchars($row['category']); ?>
                        </div>
                        <small style="color: #6c757d;">ID: #<?php echo str_pad($row['complaint_id'], 3, '0', STR_PAD_LEFT); ?></small>
                    </div>
                    
                    <div style="text-align: right;">
                        <span class="item-status <?php echo $status_class; ?>">
                            <?php echo $status_text; ?>
                        </span>
                        <br>
                        <span class="priority-badge <?php echo $priority_class; ?>">
                            <?php echo ucfirst($row['severity']); ?> Priority
                        </span>
                    </div>
                </div>

                <div class="item-details">
                    <div class="item-details-row">
                        <i class="fas fa-map-marker-alt"></i> 
                        <strong>Location:</strong> &nbsp; <?php echo htmlspecialchars($row['location']); ?>
                    </div>
                    <div class="item-details-row">
                        <i class="fas fa-calendar-alt"></i> 
                        <strong>Filed Date:</strong> &nbsp; <?php echo date("d M Y", strtotime($row['filed_date'])); ?>
                    </div>
                    <div class="item-details-row">
                        <i class="fas fa-user"></i> 
                        <strong>Citizen:</strong> &nbsp; <?php echo htmlspecialchars($row['citizen_name']); ?> 
                        <span style="color: var(--primary-blue); margin-left: 5px;">
                            (<?php echo htmlspecialchars($row['citizen_phone']); ?>)
                        </span>
                    </div>
                </div>
                
                <a href="worker_complaint_detail.php?id=<?php echo $row['complaint_id']; ?>" class="btn-outline">
                    View Full Details & Update
                </a>
            </div>

        <?php
                } 
            } else {
                echo "<div style='text-align:center; padding: 50px; background: white; border-radius: 8px; border: 1px solid #dee2e6;'>
                        <i class='fas fa-check-circle' style='font-size: 3rem; color: #28a745; margin-bottom: 15px;'></i>
                        <h3>All Caught Up!</h3>
                        <p style='color: #6c757d;'>You have no pending complaints assigned to you.</p>
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