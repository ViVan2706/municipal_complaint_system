<?php
session_start();
include '../../db.php';

if (!isset($_SESSION['admin_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../LoginAndSignup/login.html");
    exit();
}

$complaint_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch complaint details
$stmt = $conn->prepare("SELECT * FROM complaint WHERE complaint_id = ? LIMIT 1");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();

if (!$complaint) {
    echo "<p>Invalid complaint id.</p>";
    exit();
}

// On submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $worker_id = (int) $_POST['worker_id'];
    $status = "in_progress";

    // Update complaint
    $update = $conn->prepare("
        UPDATE complaint 
        SET worker_id = ?, admin_id = ?, status = ?
        WHERE complaint_id = ?
    ");
    $update->bind_param("iisi", $worker_id, $_SESSION['admin_id'], $status, $complaint_id);
    $update->execute();

    // Worker notification
    $msg_worker = "You have been assigned Complaint #$complaint_id (" . $complaint['category'] . ").";
    $w_not = $conn->prepare("
        INSERT INTO worker_notification (worker_id, complaint_id, message, date_time, status)
        VALUES (?, ?, ?, NOW(), 'unread')
    ");
    $w_not->bind_param("iis", $worker_id, $complaint_id, $msg_worker);
    $w_not->execute();

    // Citizen notification
    $msg_citizen = "Your complaint #$complaint_id has been assigned to a worker.";
    $c_not = $conn->prepare("
        INSERT INTO citizen_notification (citizen_id, complaint_id, message, date_time, status)
        VALUES (?, ?, ?, NOW(), 'unread')
    ");
    $c_not->bind_param("iis", $complaint['citizen_id'], $complaint_id, $msg_citizen);
    $c_not->execute();

    header("Location: ../admin_dashboard.php?section=complaints");
    exit();
}

// Fetch workers list
$workers = $conn->query("SELECT * FROM worker ORDER BY department, name");

?>

<h1 class="page-title">Assign Worker</h1>

<div class="list-item">
    <div class="item-header">
        <div class="item-title">
            Complaint #<?= $complaint_id ?> — <?= htmlspecialchars($complaint['category']) ?>
        </div>
    </div>

    <div class="item-details">
        <div class="item-details-row">
            <?= htmlspecialchars($complaint['description']) ?>
        </div>
    </div>

    <form method="POST" style="margin-top:20px;">
        
        <label><strong>Select Worker</strong></label>
        <select name="worker_id" class="form-select" required
                style="padding:10px; border:1px solid #ddd; width:100%; border-radius:5px;">
            <option value="">Choose Worker</option>

            <?php while ($w = $workers->fetch_assoc()): ?>
                <option value="<?= $w['worker_id'] ?>">
                    <?= $w['name'] ?> (<?= $w['department'] ?> — <?= $w['availability_status'] ?>)
                </option>
            <?php endwhile; ?>
        </select>

        <button class="view-details-button" 
                style="margin-top:15px; width:100%; font-size:15px;">
            Assign Worker
        </button>
    </form>
</div>
