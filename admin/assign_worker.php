<?php
require_once "db.php";
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

$complaint_id = isset($_GET['complaint_id']) ? (int)$_GET['complaint_id'] : 0;

// Fetch complaint details
$stmt = $conn->prepare("SELECT * FROM complaint WHERE complaint_id = ? LIMIT 1");
$stmt->bind_param("i", $complaint_id);
$stmt->execute();
$complaint = $stmt->get_result()->fetch_assoc();

if (!$complaint) {
    die("Invalid complaint ID");
}

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $worker_id = (int) $_POST['worker_id'];

    // Step 1: Update complaint
    $status = "in_progress";
    $update = $conn->prepare("
        UPDATE complaint 
        SET worker_id = ?, admin_id = ?, status = ? 
        WHERE complaint_id = ?
    ");
    $update->bind_param("iisi", $worker_id, $_SESSION['admin_id'], $status, $complaint_id);
    $update->execute();

    // Step 2: Insert worker notification
    $message = "You have been assigned complaint #$complaint_id (" . $complaint['category'] . ").";
    $not1 = $conn->prepare("
        INSERT INTO worker_notification (worker_id, complaint_id, message, date_time, status)
        VALUES (?, ?, ?, NOW(), 'unread')
    ");
    $not1->bind_param("iis", $worker_id, $complaint_id, $message);
    $not1->execute();

    // Step 3: Insert citizen notification
    $msg2 = "Your complaint #$complaint_id has been assigned to a worker.";
    $not2 = $conn->prepare("
        INSERT INTO citizen_notification (citizen_id, complaint_id, message, date_time, status)
        VALUES (?, ?, ?, NOW(), 'unread')
    ");
    $not2->bind_param("iis", $complaint['citizen_id'], $complaint_id, $msg2);
    $not2->execute();

    header("Location: view_complaints.php?view=$complaint_id");
    exit();
}

// Fetch all workers for selection
$workers = $conn->query("SELECT * FROM worker ORDER BY department, name");

require "admin_header.php";
?>

<h3>Assign Worker (Complaint #<?= $complaint_id ?>)</h3>

<p><strong>Category:</strong> <?= htmlspecialchars($complaint['category']) ?></p>
<p><strong>Description:</strong> <?= htmlspecialchars($complaint['description']) ?></p>

<form method="post" class="mt-3">

    <div class="mb-3">
        <label class="form-label">Select Worker</label>
        <select name="worker_id" class="form-select" required>
            <option value="">Choose Worker</option>

            <?php while ($w = $workers->fetch_assoc()): ?>
            <option value="<?= $w['worker_id'] ?>">
                <?= $w['name'] ?> (<?= $w['department'] ?> - <?= $w['availability_status'] ?>)
            </option>
            <?php endwhile; ?>
        </select>
    </div>

    <button class="btn btn-primary">Assign Worker</button>
</form>

<?php require "admin_footer.php"; ?>
