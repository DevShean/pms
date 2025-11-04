<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

// Map role_id → folder name
$role_folder = [
    1 => 'admin',
    2 => 'officer',
    3 => 'medical',
    4 => 'rehab',
    5 => 'visitor'
];

// Detect actual directory user is trying to access
$current_folder = basename(dirname($_SERVER['PHP_SELF']));

// Check if the folder matches the user's role
if (!isset($role_folder[$_SESSION['role_id']]) || $role_folder[$_SESSION['role_id']] !== $current_folder) {
    echo "<script>alert('Access Denied! Unauthorized access.'); window.location='../../auth/login.php';</script>";
    exit;
}
?>
