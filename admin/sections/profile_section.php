<?php
// Admin is already logged in and $conn is available

$admin_id = $_SESSION['admin_id'];

// Fetch admin details
$stmt = $conn->prepare("
    SELECT name, email, phone_no, designation, department 
    FROM admin 
    WHERE admin_id = ?
");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$admin = $stmt->get_result()->fetch_assoc();

// Message after update
$message = "";

// Update profile
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $designation = $_POST['designation'];
    $department = $_POST['department'];

    $update = $conn->prepare("
        UPDATE admin 
        SET name=?, phone_no=?, designation=?, department=? 
        WHERE admin_id=?
    ");
    $update->bind_param("ssssi", $name, $phone, $designation, $department, $admin_id);

    if ($update->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile.";
    }
}

?>

<h1 class="page-title">Admin Profile</h1>
<p class="page-description">Manage your admin account details</p>

<div class="profile-container">

    <?php if ($message): ?>
        <div class="alert"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">

        <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name"
                   value="<?= htmlspecialchars($admin['name']) ?>" required>
        </div>

        <div class="form-group">
            <label>Phone Number</label>
            <input type="text" name="phone"
                   value="<?= htmlspecialchars($admin['phone_no']) ?>" required>
        </div>

        <div class="form-group">
            <label>Designation</label>
            <input type="text" name="designation"
                   value="<?= htmlspecialchars($admin['designation']) ?>" required>
        </div>

        <div class="form-group">
            <label>Department</label>
            <input type="text" name="department"
                   value="<?= htmlspecialchars($admin['department']) ?>" required>
        </div>

        <div class="form-group">
            <label>Email (read-only)</label>
            <input type="email" value="<?= htmlspecialchars($admin['email']) ?>" readonly>
        </div>

        <button type="submit" class="btn-save">Save Changes</button>

    </form>
</div>
