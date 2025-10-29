<?php
include '../db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $designation = $_POST['designation'];
    $department = $_POST['department'];

    $sql = "INSERT INTO admin (name, email, password, designation, department) 
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssss", $name, $email, $password, $designation, $department);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.html?success=admin_registered");
    } else {
        if (mysqli_errno($conn) == 1062) {
            header("Location: admin_signup.html?error=email_exists");
        } else {
            header("Location: admin_signup.html?error=signup_failed");
        }
    }
    
    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    header("Location: admin_signup.html");
}
exit;
?>