<?php
require '../../includes/session_check.php';
include '../../config/config.php';

$user_id = $_SESSION['user_id'];
$visitor = $conn->query("SELECT visitor_id FROM visitors WHERE user_id = $user_id")->fetch_assoc();
$visitor_id = $visitor['visitor_id'];

$query = $_GET['q'] ?? '';
$query = $conn->real_escape_string($query);

$result = $conn->query("
    SELECT i.inmate_id, i.first_name, i.last_name, i.photo_path, COALESCE(v.relationship, 'No relationship') as relationship
    FROM inmates i
    LEFT JOIN visitations vis ON i.inmate_id = vis.inmate_id AND vis.visitor_id = $visitor_id
    LEFT JOIN visitors v ON v.visitor_id = vis.visitor_id AND v.user_id = $user_id
    WHERE i.first_name LIKE '%$query%' OR i.last_name LIKE '%$query%'
    GROUP BY i.inmate_id
    LIMIT 10
");

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

header('Content-Type: application/json');
echo json_encode($data);
?>
