<?php
function checkLogin() {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header("Location: /mpusbaru/Views/auth/login.php");
        exit;
    }
}

function checkAdmin() {
    session_start();
    if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        header("Location: /mpusbaru/Views/auth/login.php");
        exit;
    }
}
?> 