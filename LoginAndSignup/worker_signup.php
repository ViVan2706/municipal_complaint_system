<?php
session_start();
$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require 'db_connect.php';

    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT worker_id, name, email, password FROM worker WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['user_id'] = $row['worker_id'];
            $_SESSION['name'] = $row['name'];
            $_SESSION['email'] = $row['email'];
            $_SESSION['role'] = 'worker';
            
            header("Location: worker_dashboard.php");
            exit;
        } else {
            $login_error = "Invalid email or password.";
        }
    } else {
        $login_error = "Invalid email or password.";
    }
    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Worker Login</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
            background: linear-gradient(to bottom, #f0f7ff 0%, #ffffff 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .login-container {
            background: #ffffff;
            padding: 2.5rem 3rem;
            border-radius: 12px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            width: 100%;
            max-width: 420px;
            text-align: center;
        }
        .icon-container {
            width: 60px; height: 60px; background: #e6f2ff;
            border-radius: 50%; display: flex; justify-content: center;
            align-items: center; margin: 0 auto 1rem auto;
        }
        h1 { font-size: 1.75rem; font-weight: 600; color: #1a253c; margin-bottom: 0.25rem; }
        .subtitle { font-size: 0.95rem; color: #5a6b8a; margin-bottom: 2rem; }
        form { text-align: left; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-size: 0.9rem; font-weight: 600; color: #333; margin-bottom: 0.5rem; }
        .form-group input {
            width: 100%; padding: 0.85rem 1rem; font-size: 1rem;
            border: 1px solid #ddd; border-radius: 8px;
        }
        .form-group input:focus {
            outline: none; border-color: #007bff;
            box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
        }
        .login-button {
            width: 100%; padding: 0.9rem; border: none; border-radius: 8px;
            background-color: #007bff; color: white; font-size: 1rem;
            font-weight: 600; cursor: pointer; transition: background-color 0.2s ease;
        }
        .login-button:hover { background-color: #0056b3; }
        .error-message {
            color: #d93025; background: #fbeae9; border: 1px solid #fbd0ce;
            border-radius: 8px; padding: 0.8rem; margin-bottom: 1.25rem;
            text-align: center; font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="icon-container">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="#007bff"><path d="M12 7V3H2v18h20V7H12zM6 19H4v-2h2v2zm0-4H4v-2h2v2zm0-4H4V9h2v2zm0-4H4V5h2v2zm4 12H8v-2h2v2zm0-4H8v-2h2v2zm0-4H8V9h2v2zm0-4H8V5h2v2zm10 12h-8v-2h2v-2h-2v-2h2v-2h-2V9h8v10zm-2-8h-2v2h2v-2zm0 4h-2v2h2v-2z"/></svg>
        </div>
        <h1>Worker Login</h1>
        <p class="subtitle">Municipal Corporation Portal</p>
        
        <form action="worker_login.php" method="POST">
            <?php if (!empty($login_error)): ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
            </div>
            
            <button type="submit" class="login-button">Login</button>
        </form>
    </div>
</body>
</html>