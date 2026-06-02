<?php
$pageTitle = 'Logout';
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db_connect.php';

// Ensure session is active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Clear user session
unset($_SESSION['user_id'], $_SESSION['user_name']);

// Destroy session completely
session_destroy();

// Redirect to home
header('Location: index.php');
exit();

