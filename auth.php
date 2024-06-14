<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Your existing authentication logic
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
