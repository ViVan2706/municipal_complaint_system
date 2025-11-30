<?php
// Ensure DB connection exists
if (!isset($conn)) {
    include "../../db.php";
}
if (!isset($_SESSION)) {
    session_start();
}

// Fetch all citizens
$sql = "SELECT * FROM citizen ORDER BY date_registered DESC";
$result = $conn->query($sql);
?>

<h1 class="page-title">Citizens List</h1>
<p class="page-description">Registered users of the MCCCTS platform</p>

<style>
    /* Card container */
    .citizen-card {
        background: linear-gradient(135deg, #e3f2fd, #ffffff);
        padding: 20px;
        margin-bottom: 18px;
        border-radius: 14px;
        border: 2px solid #2196f3;
        box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        transition: 0.25s ease-in-out;
    }

    /* Hover effect */
    .citizen-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 6px 16px rgba(0,0,0,0.20);
    }

    /* Name */
    .citizen-name {
        font-size: 22px;
        font-weight: 700;
        color: #0d47a1;
        margin-bottom: 6px;
    }

    /* Text fields */
    .citizen-field {
        margin: 6px 0;
        font-size: 15px;
        color: #333;
        display: flex;
        align-items: center;
    }

    /* Icons */
    .citizen-field i {
        margin-right: 10px;
        color: #0d47a1;
        font-size: 16px;
    }

    /* Registered date special color */
    .registered-date {
        color: #1b5e20;
        font-weight: 600;
    }
</style>

<div class="citizen-list">

<?php while ($row = $result->fetch_assoc()): ?>

    <div class="citizen-card">

        <div class="citizen-name">
            <i class="fas fa-user-circle"></i> <?= htmlspecialchars($row['name']) ?>
        </div>

        <div class="citizen-field">
            <i class="fas fa-id-card-alt"></i> ID: <?= $row['citizen_id'] ?>
        </div>

        <div class="citizen-field">
            <i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email']) ?>
        </div>

        <div class="citizen-field">
            <i class="fas fa-phone-alt"></i> <?= htmlspecialchars($row['phone_no']) ?>
        </div>

        <div class="citizen-field">
            <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($row['address']) ?>
        </div>

        <div class="citizen-field registered-date">
            <i class="fas fa-calendar-check"></i> Registered: <?= $row['date_registered'] ?>
        </div>

    </div>

<?php endwhile; ?>

</div>
