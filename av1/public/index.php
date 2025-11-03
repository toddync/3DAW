<?php
require_once __DIR__ . '/../includes/auth.php';

if (!isAuthenticated()) {
    header('Location: login.html');
    exit();
} else {
    header('Location: dashboard.html');
    exit();
}
?>