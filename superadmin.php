<?php
include 'auth.php';

if ($_SESSION['role'] != 'superadmin') {
    echo "Access denied!";
    exit();
}
?>
