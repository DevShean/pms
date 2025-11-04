<?php
require '../../includes/session_check.php';
include '../../config/config.php';

$user_id = $_SESSION['user_id'];

// Get recent notifications from notifications table
$notifications = $conn->query("
    SELECT title, message, created_at
    FROM notifications
    WHERE user_id = $user_id
    ORDER BY created_at DESC
    LIMIT 10
");

$result = [];
while ($row = $notifications->fetch_assoc()) {
    $result[] = $row;
}

header('Content-Type: application/json');
echo json_encode(['notifications' => $result]);
?>
