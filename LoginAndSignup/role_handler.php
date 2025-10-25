<?php

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (isset($_POST['role'])) {
        $role = $_POST['role'];
        
        switch ($role) {
            case 'citizen':
                header("Location: citizen_signup.html");
                exit; 

            case 'admin':
                header("Location: admin_signup.html");
                exit;
                
            case 'worker':
                header("Location: worker_signup.html");
                exit;
        }
    }
}
?>