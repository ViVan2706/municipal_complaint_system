<?php
session_start();
include '../db.php';
$error_message = ""; 
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_from_form = $_POST['email']; 
    $password_from_form = $_POST['password'];
    $role = $_POST['role'];
    $table_name = "";
    $id_column = "";
    $redirect_page = "";
    switch ($role) {
        case 'citizen':
            $table_name = "citizen";
            $id_column = "citizen_id";
            $redirect_page = "../citizen/home.php"; 
            break;
        case 'worker':
            $table_name = "worker";
            $id_column = "worker_id";
            $redirect_page = "../worker/worker_dashboard.php"; 
            break;
        case 'admin':
            $table_name = "admin";
            $id_column = "admin_id";
            $redirect_page = "../admin/home.php"; 
            break;
        default:
            $error_message = "An invalid role was selected.";
    }
    if (empty($error_message)) {
        $sql = "SELECT $id_column, name, email, password FROM $table_name WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email_from_form);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            $password_from_db = $row['password'];
            if ($password_from_form == $password_from_db) {
                $_SESSION[$id_column] = $row[$id_column];
                $_SESSION['name'] = $row['name'];
                $_SESSION['email'] = $row['email'];
                $_SESSION['role'] = $role;
                header("Location: $redirect_page");
                exit; 
            } else {
                $error_message = "Invalid credentials. Please try again.";
            }
        } else {
            $error_message = "User not found. Please check your email.";
        }
        $stmt->close();
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipal Corporation Login</title>
    <link rel="stylesheet" href="login.css">
    <style>
        .error-message {
            color: #D8000C;
            background-color: #FFD2D2;
            border: 1px solid #D8000C;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="#007bff">
                <path
                    d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z" />
            </svg>
        </div>
        <h1>Municipal Corporation</h1>
        <p class="subtitle">Community Complaint Tracking System</p>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="role">Select Role</label>
                <select id="role" name="role" required>
                    <option value="" disabled selected>Choose your role</option>
                    <option value="citizen">Citizen</option>
                    <option value="worker">Worker</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php endif; ?>
            <button type_submit class="login-button">Login</button>
        </form>
        <div class="signup-link">
            Don't have an account? <a href="role_handler.html">Create new account</a>
        </div>
    </div>
</body>
</html>