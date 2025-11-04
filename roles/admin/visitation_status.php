<?php
require '../../includes/session_check.php';
require '../../config/config.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $visit_id = $_GET['id'];
    $status = $_GET['status'];

    // Update the status in the database
    $sql = "UPDATE visitations SET status = ? WHERE visit_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $status, $visit_id);

    if ($stmt->execute()) {
        header("Location: visitation.php");
    } else {
        echo "Error updating record: " . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>
