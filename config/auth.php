<?php
session_start();

function checkAuth($roles = []) {
    if (!isset($_SESSION['user'])) {
        header("Location: /public/login.php");
        exit;
    }

    // Role validation
    if (!empty($roles) && !in_array($_SESSION['user']['role'], $roles)) {
        header("Location: /public/login.php");
        exit;
    }
}
?>