<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db_connect.php';

function requireAdmin() {
    if (!isAdmin()) {
        redirect('login.php');
    }
}
