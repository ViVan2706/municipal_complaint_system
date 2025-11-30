<?php
// This file assumes: $conn is available
// Admin login is already verified in admin_dashboard.php

$sql = "SELECT citizen_id, name, email, phone_no, address, date_registered 
        FROM citizen 
        ORDER BY date_registered DESC";

$result = $conn->query($sql);
?>

<h1 class="page-title">Citizens List</h1>
<p class="page-description">Registered users of the MCCCTS platform</p>

<div class="list-container">

    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
        
        <div class="list-item">
            <div class="item-header">
                <div class="item-title">
                    <i class="fas fa-user"></i>
                    <span><?= htmlspecialchars($row['name']) ?></span>
                </div>
                <span class="item-status status-medium-priority">
                    ID: <?= $row['citizen_id'] ?>
                </span>
            </div>

            <div class="item-details item-details-full-width">
                <div class="item-details-row">
                    <i class="fas fa-envelope"></i> <?= htmlspecialchars($row['email']) ?>
                </div>
                <div class="item-details-row">
                    <i class="fas fa-phone"></i> <?= htmlspecialchars($row['phone_no']) ?>
                </div>
                <div class="item-details-row">
                    <i class="fas fa-map-marker"></i> <?= htmlspecialchars($row['address']) ?>
                </div>
                <div class="item-details-row">
                    <i class="fas fa-calendar"></i> Registered: <?= $row['date_registered'] ?>
                </div>
            </div>
        </div>

        <?php endwhile; ?>
    <?php else: ?>
        <p>No citizens found.</p>
    <?php endif; ?>

</div>
