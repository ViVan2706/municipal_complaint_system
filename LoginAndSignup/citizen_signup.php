<?php
session_start();
include '../db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_no = $_POST['phone_no'];
    $address = $_POST['address'];

    $sql = "INSERT INTO citizen (name, email, password, phone_no, address, date_registered) 
            VALUES (?, ?, ?, ?, ?, CURDATE())";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $password, $phone_no, $address);
    
    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.html?success=registered");
    } else {
        if (mysqli_errno($conn) == 1062) {
            header("Location: citizen_signup.html?error=email_exists");
        } else {
            header("Location: citizen_signup.html?error=signup_failed");
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    header("Location: citizen_signup.html");
}
exit;
?>