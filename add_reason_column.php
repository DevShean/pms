<?php
include 'config.php';

$sql = "ALTER TABLE transfers ADD COLUMN reason TEXT";

if ($conn->query($sql) === TRUE) {
    echo "Column 'reason' added successfully to transfers table.";
} else {
    echo "Error adding column: " . $conn->error;
}

$conn->close();
?>
