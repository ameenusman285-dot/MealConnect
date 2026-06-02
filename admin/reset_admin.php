<?php
// One-time admin password reset script for MealConnect
// Usage: open in browser with ?token=THE_TOKEN then delete this file immediately

// CHANGE NOTHING BELOW UNLESS YOU KNOW WHAT YOU'RE DOING

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/db_connect.php';

// One-time token to authorize reset (keep this secret)
// If you want a different token, edit this file before running.
$EXPECTED_TOKEN = 'reset-token-20260602-b4f8c9e3';

if (!isset($_GET['token']) || $_GET['token'] !== $EXPECTED_TOKEN) {
    http_response_code(403);
    echo 'Unauthorized. Provide the correct token as ?token=...';
    exit;
}

// Generate a secure random password
function generate_password($length = 12) {
    $chars = 'ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz23456789!@#$%&*()-_=+';
    $max = strlen($chars) - 1;
    $pass = '';
    for ($i = 0; $i < $length; $i++) {
        $pass .= $chars[random_int(0, $max)];
    }
    return $pass;
}

$newPassword = generate_password(12);
$hash = password_hash($newPassword, PASSWORD_DEFAULT);

$username = 'admin';
$stmt = $conn->prepare('UPDATE admin SET password = ? WHERE username = ?');
if (!$stmt) {
    echo 'Database error: could not prepare statement.';
    exit;
}
$stmt->bind_param('ss', $hash, $username);
if ($stmt->execute()) {
    echo '<h2>Password reset successful</h2>';
    echo '<p>Username: <strong>' . htmlspecialchars($username) . '</strong></p>';
    echo '<p>New password: <strong>' . htmlspecialchars($newPassword) . '</strong></p>';
    echo '<p><em>Important:</em> Delete this file now: <code>admin/reset_admin.php</code></p>';
} else {
    echo 'Failed to reset password. Please check database connection and admin user.';
}

// Close statement and connection
$stmt->close();
$conn->close();

?>
