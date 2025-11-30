<?php
// Ensure DB connection
if (!isset($conn)) {
    include "../../db.php";
}
if (!isset($_SESSION)) {
    session_start();
}

// Fetch workers + their supervisors
$sql = "
    SELECT 
        w.*, 
        s.name AS supervisor_name
    FROM worker w
    LEFT JOIN worker s 
        ON w.supervisor_worker_id = s.worker_id
    ORDER BY w.department, w.name
";
$result = $conn->query($sql);
?>

<h1 class="page-title">Workers List</h1>
<p class="page-description">All workers across Civil, Electrical, and Water departments</p>

<style>
    /* Card container */
    .worker-card {
        background: linear-gradient(135deg, #e1f5fe, #ffffff);
        padding: 20px;
        margin-bottom: 18px;
        border-radius: 14px;
        border: 2px solid #0288d1;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        transition: 0.25s ease-in-out;
    }

    /* Hover effect */
    .worker-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.20);
    }

    /* Name */
    .worker-name {
        font-size: 22px;
        font-weight: 700;
        color: #01579b;
        margin-bottom: 6px;
    }

    /* Text fields */
    .worker-field {
        margin: 6px 0;
        font-size: 15px;
        color: #333;
        display: flex;
        align-items: center;
    }

    /* Icons */
    .worker-field i {
        margin-right: 10px;
        color: #0277bd;
        font-size: 16px;
    }

    /* Status colors */
    .status-available {
        color: #1b5e20;
        font-weight: bold;
    }

    .status-busy {
        color: #c62828;
        font-weight: bold;
    }

    .status-offline {
        color: #6a1b9a;
        font-weight: bold;
    }

</style>

<div class="worker-list">

<?php while ($row = $result->fetch_assoc()): ?>

    <div class="worker-card">

        <div class="worker-name">
            <i class="fas fa-user-cog"></i> <?= htmlspecialchars($row['name']) ?>
        </div>

        <div class="worker-field">
            <i class="fas fa-hammer"></i> Department: <?= htmlspecialchars($row['department']) ?>
        </div>

        <div class="worker-field">
            <i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email']) ?>
        </div>

        <div class="worker-field">
            <i class="fas fa-phone-alt"></i> <?= htmlspecialchars($row['phone_no']) ?>
        </div>

        <div class="worker-field">
            <i class="fas fa-user-tie"></i> Supervisor: 
            <?= htmlspecialchars($row['supervisor_name'] ?? "None") ?>
        </div>

        <div class="worker-field">
            <i class="fas fa-circle"></i> 
            Status: 
            <?php
            if ($row['availability_status'] === 'available') {
                echo "<span class='status-available'>Available</span>";
            } elseif ($row['availability_status'] === 'busy') {
                echo "<span class='status-busy'>Busy</span>";
            } else {
                echo "<span class='status-offline'>Offline</span>";
            }
            ?>
        </div>

    </div>

<?php endwhile; ?>

</div>
