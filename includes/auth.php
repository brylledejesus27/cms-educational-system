<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['admin_id']);
}

function requireLogin_root() {
    if (!isLoggedIn()) {
        header("Location: /cms-educational-system/admin/login.php");
        exit();
    }
}