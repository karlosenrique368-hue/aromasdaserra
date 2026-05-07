<?php require __DIR__ . '/bootstrap.php'; session_destroy(); header('Location: ' . admin_url('login.php')); exit;
