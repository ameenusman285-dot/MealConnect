<?php
require_once __DIR__ . '/config.php';

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:2rem;color:#E74C3C;background:#1A1A1A;min-height:100vh;display:flex;align-items:center;justify-content:center;">'
        . '<div style="text-align:center"><h2>Database Connection Failed</h2><p>Please ensure MySQL is running and the database "' . DB_NAME . '" exists.</p><small>' . $conn->connect_error . '</small></div></div>');
}

$conn->set_charset("utf8");

function sanitize($conn, $data) {
    return $conn->real_escape_string(htmlspecialchars(trim($data)));
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['admin_id']);
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function getCartCount($conn) {
    if (!isLoggedIn()) return 0;
    $uid = $_SESSION['user_id'];
    $res = $conn->query("SELECT SUM(quantity) as total FROM cart WHERE user_id=$uid");
    $row = $res->fetch_assoc();
    return $row['total'] ?? 0;
}
