<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle live check
if (isset($_GET['live']) && isset($_GET['since'])) {
    $since = $conn->real_escape_string($_GET['since']);
    $new_logs_result = $conn->query("SELECT COUNT(*) as count FROM system_logs WHERE created_at > '$since'");
    $count = $new_logs_result->fetch_assoc()['count'];
    header('Content-Type: application/json');
    echo json_encode(['new_logs' => $count > 0]);
    exit();
}

// Fetch logs with user names
$logs_result = $conn->query("
    SELECT sl.*, u.full_name
    FROM system_logs sl
    LEFT JOIN users u ON sl.user_id = u.user_id
    ORDER BY sl.created_at DESC
");

// Mark all as read when viewing the page (for sidebar count)
$conn->query("UPDATE system_logs SET is_read = 1 WHERE is_read = 0");
?>
