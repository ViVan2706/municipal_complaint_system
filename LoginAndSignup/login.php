<?php
session_start();
include '../db.php'; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username_from_form = $_POST['username'];
    $password_from_form = $_POST['password'];
    $role = $_POST['role'];

    $table_name = "";
    $id_column = "";
    $redirect_page = "";

    switch ($role) {
        case 'citizen':
            $table_name = "citizen";
            $id_column = "citizen_id";
            $redirect_page = "citizen_dashboard.php";
            break;
        case 'worker':
            $table_name = "worker";
            $id_column = "worker_id";
            $redirect_page = "worker_dashboard.php";
            break;
        case 'admin':
            $table_name = "admin";
            $id_column = "admin_id";
            $redirect_page = "admin_dashboard.php";
            break;
        default:
            header("Location: login.html?error=invalid_role");
            exit;
    }

    $sql = "SELECT $id_column, name, password FROM $table_name WHERE name = ?";
    
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $username_from_form);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if (mysqli_num_rows($result) == 1) {
        $row = mysqli_fetch_assoc($result);
        
        $password_from_db = $row['password'];

        if ($password_from_form == $password_from_db) {
            
            $_SESSION['user_id'] = $row[$id_column];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = $role;
            
            header("Location: $redirect_page");
            exit;

        } else {
            header("Location: login.html?error=invalid_credentials");
            exit;
        }
    } else {
        header("Location: login.html?error=user_not_found");
        exit;
    }

    mysqli_stmt_close($stmt);
    mysqli_close($conn);

} else {
    header("Location: login.html");
    exit;
}
?>