<?php
include '../../../config/config.php';

header('Content-Type: application/json; charset=utf-8');

if (!isset($_GET['q'])) {
    echo json_encode([]);
    exit;
}

$q = trim($_GET['q']);
if ($q === '') {
    echo json_encode([]);
    exit;
}

$like = "%" . $q . "%";
$stmt = $conn->prepare("SELECT inmate_id, first_name, last_name FROM inmates WHERE first_name LIKE ? OR last_name LIKE ? ORDER BY last_name, first_name LIMIT 15");
$stmt->bind_param('ss', $like, $like);
$stmt->execute();
$res = $stmt->get_result();
$out = [];
while ($row = $res->fetch_assoc()) {
    $out[] = $row;
}

echo json_encode($out);

$stmt->close();
$conn->close();

?>
