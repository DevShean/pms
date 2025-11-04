<?php
include '../../../config/config.php';

if (isset($_GET['id'])) {
    $program_id = intval($_GET['id']);
    $result = $conn->query("SELECT * FROM programs WHERE program_id = $program_id");
    if ($result->num_rows > 0) {
        echo json_encode($result->fetch_assoc());
    } else {
        echo json_encode(['error' => 'Program not found']);
    }
} else {
    echo json_encode(['error' => 'No program ID provided']);
}
?>
