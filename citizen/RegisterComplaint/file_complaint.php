<?php
session_start();
include '../../db.php'; 

if (!isset($_SESSION['citizen_id'])) {
    header("Location: ../LoginAndSignup/login.html");
    exit();
}

$citizen_id = $_SESSION['citizen_id'];
$stmt = $conn->prepare("SELECT COUNT(*) AS unread FROM citizen_notification WHERE citizen_id = ? AND status = 'unread'");
$stmt->bind_param("i", $citizen_id);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();
$notification_count = $res['unread'] ?? 0;
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Register Complaint | MCCCTS</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
:root {
    --primary-blue:#007bff;
    --success-green:#28a745;
    --background-light:#f8f9fa;
    --text-dark:#212529;
    --border-color:#dee2e6;
    --card-bg:#ffffff;
}
body {
    background-color: #f8fafc;
    font-family: 'Segoe UI', sans-serif;
}

.header {
    background-color: #ffffff;
    border-bottom: 1px solid #dee2e6;
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.logo {
    font-size: 1.25rem;
    font-weight: 700;
    color: #212529;
    text-decoration: none;
}
.nav-links {
    display: flex;
    align-items: center;
}
.nav-link {
    text-decoration: none;
    color: #212529;
    margin-left: 20px;
    padding: 6px 12px;
    display: flex;
    align-items: center;
    font-size: 0.95rem;
    border-radius: 6px;
    transition: background-color 0.2s, color 0.2s;
}
.nav-link:hover {
    color: #007bff;
}
.nav-link.active {
    background-color: #007bff;
    color: #ffffff;
}
.icon {
    display: inline-block;
    margin-right: 6px;
}
.icon-register::before { content: '+'; font-weight: bold; }
.icon-my-complaints::before { content: '📄'; }
.icon-notifications::before { content: '🔔'; color: #ffb300; }
.icon-profile::before { content: '👤'; color: #4b0082; }
.notifications-badge {
    background-color: #dc3545;
    color: white;
    border-radius: 50%;
    padding: 2px 6px;
    font-size: 0.7rem;
    margin-left: 4px;
}


.container {
    max-width: 700px;
    margin-top: 60px;
}
.card {
    border: none;
    box-shadow: 0 6px 18px rgba(0,0,0,0.08);
    border-radius: 12px;
}
.btn-primary {
    background-color: #007bff;
    border: none;
}
.btn-primary:hover {
    background-color: #0056b3;
}
</style>
</head>
<body>

<header class="header">
    <a href="../home.php" class="logo">MCCCTS</a>
    <nav class="nav-links">
        <a href="../RegisterComplaint/file_complaint.php" class="nav-link active">
            <span class="icon icon-register"></span> Register Complaint
        </a>
        <a href="../MyComplaints/MyComplaintsPend.php" class="nav-link">
            <span class="icon icon-my-complaints"></span> My Complaints
        </a>
        <a href="../NotificationsCitizen/Notifications.php" class="nav-link">
            <span class="icon icon-notifications"></span> Notifications
            <?php if (isset($notification_count) && $notification_count > 0): ?>
                <span class="notifications-badge"><?php echo htmlspecialchars($notification_count); ?></span>
            <?php endif; ?>
        </a>
        <a href="../profile/profile.php" class="nav-link">
            <span class="icon icon-profile"></span> Profile
        </a>
    </nav>
</header>


<div class="container">
  <div class="card p-4">
    <h3 class="mb-3">Register New Complaint</h3>
    <p class="text-muted">Submit a complaint for municipal issues in your area.</p>

    <form method="POST" action="complaint_handler.php" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Category</label>
        <select name="category" class="form-select" required>
          <option value="" selected disabled>Select category</option>
          <option>Road Damage</option>
          <option>Street Light</option>
          <option>Water Leakage</option>
          <option>Garbage Collection</option>
          <option>Drain Blockage</option>
          <option>Noise Complaint</option>
          <option>Other</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Severity Level</label>
        <select name="severity" class="form-select" required>
          <option value="" selected disabled>Select severity</option>
          <option value="low">Low</option>
          <option value="medium">Medium</option>
          <option value="high">High</option>
          <option value="critical">Critical</option>
        </select>
      </div>

      <div class="mb-3">
        <label class="form-label">Location</label>
        <input type="text" name="location" class="form-control" placeholder="Enter location address" required>
      </div>

      <div class="mb-3">
        <label class="form-label">Description</label>
        <textarea name="description" class="form-control" rows="4" placeholder="Describe the issue..." required></textarea>
      </div>

      <div class="mb-3">
        <label class="form-label">Upload Photo/Video (Optional)</label>
        <input type="file" name="attachment" class="form-control">
      </div>

      <button type="submit" class="btn btn-primary w-100">Submit Complaint</button>
    </form>
  </div>
</div>

</body>
</html>
