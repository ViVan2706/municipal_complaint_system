<?php
session_start();

$SECRET_CITIZEN = "Welcomecit";
$SECRET_ADMIN   = "AdminBoss";
$SECRET_WORKER  = "WorkForUs";

if (isset($_POST['submit'])) {
    
    $role = $_POST['role'] ?? ''; 
    $input_code = $_POST['access_code'] ?? '';

    $redirect_url = "";
    $authorized = false;
    
    if ($role === 'citizen') {
        if ($input_code === $SECRET_CITIZEN) {
            $authorized = true;
            $redirect_url = "citizen_signup.php"; 
        }
    }
    elseif ($role === 'admin') {
        if ($input_code === $SECRET_ADMIN) {
            $authorized = true;
            $redirect_url = "admin_signup.php"; 
        }
    }
    elseif ($role === 'worker') {
        if ($input_code === $SECRET_WORKER) {
            $authorized = true;
            $redirect_url = "worker_signup.php"; 
        }
    }

    if ($authorized) {
        header("Location: " . $redirect_url);
        exit(); 
    } else {
        echo "<script>
            alert('ACCESS DENIED: The security code you entered is incorrect for the selected role.');
            window.location.href = 'role_handler.html'; 
        </script>";
        exit();
    }

} else {
    header("Location: index.html");
    exit();
}
?>