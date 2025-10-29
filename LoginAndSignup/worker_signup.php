<?php
include '../db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone_no = $_POST['phone_no'];
    $department = $_POST['department'];
    
    $supervisor_id = !empty($_POST['supervisor_worker_id']) ? (int)$_POST['supervisor_worker_id'] : NULL;

    $sql = "INSERT INTO worker (name, email, password, phone_no, department, supervisor_worker_id) 
            VALUES (?, ?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sssssi", $name, $email, $password, $phone_no, $department, $supervisor_id);

    if (mysqli_stmt_execute($stmt)) {
        header("Location: login.html?success=worker_registered");
    } else {
        if (mysqli_errno($conn) == 1062) {
            header("Location: worker_signup.html?error=email_exists");
        } else {
            header("Location: worker_signup.html?error=signup_failed");
        }
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    header("Location: worker_signup.html");
}
exit;

//foreign key constraint not added in workers table
?>

