<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM transfers WHERE transfer_id = $id");
    // Log the action
    $action = "Deleted transfer";
    $details = "Transfer ID $id deleted.";
    $user_id = $_SESSION['user_id'] ?? null;
    $conn->query("INSERT INTO system_logs (action, details, user_id) VALUES ('$action', '$details', $user_id)");
    header("Location: transfers.php");
    exit();
}

// Handle add transfer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_transfer'])) {
    $inmate_id = intval($_POST['inmate_id']);
    $from_block = $conn->real_escape_string($_POST['from_block']);
    $to_block = $conn->real_escape_string($_POST['to_block']);
    $transfer_date = $_POST['transfer_date'];
    $approved_by = intval($_POST['approved_by']);

    $sql = "INSERT INTO transfers (inmate_id, from_block, to_block, transfer_date, approved_by) VALUES ($inmate_id, '$from_block', '$to_block', '$transfer_date', $approved_by)";
    $conn->query($sql);
    // Log the action
    $action = "Added transfer";
    $details = "Transfer added for inmate ID $inmate_id from $from_block to $to_block.";
    $user_id = $_SESSION['user_id'] ?? null;
    $conn->query("INSERT INTO system_logs (action, details, user_id) VALUES ('$action', '$details', $user_id)");
    header("Location: transfers.php");
    exit();
}

// Handle edit transfer
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_transfer'])) {
    $id = intval($_POST['edit_transfer_id']);
    $inmate_id = intval($_POST['edit_inmate_id']);
    $from_block = $conn->real_escape_string($_POST['edit_from_block']);
    $to_block = $conn->real_escape_string($_POST['edit_to_block']);
    $transfer_date = $_POST['edit_transfer_date'];
    $approved_by = intval($_POST['edit_approved_by']);

    $sql = "UPDATE transfers SET inmate_id=$inmate_id, from_block='$from_block', to_block='$to_block', transfer_date='$transfer_date', approved_by=$approved_by WHERE transfer_id=$id";
    $conn->query($sql);
    // Log the action
    $action = "Updated transfer";
    $details = "Transfer ID $id updated.";
    $user_id = $_SESSION['user_id'] ?? null;
    $conn->query("INSERT INTO system_logs (action, details, user_id) VALUES ('$action', '$details', $user_id)");
    header("Location: transfers.php");
    exit();
}

// Fetch transfers with inmate and approver names
$result = $conn->query("
    SELECT t.*, i.first_name, i.last_name, u.full_name as approver_name
    FROM transfers t
    INNER JOIN inmates i ON t.inmate_id = i.inmate_id
    LEFT JOIN users u ON t.approved_by = u.user_id
    ORDER BY t.transfer_date DESC
");

// Fetch inmates for dropdown
$inmates_result = $conn->query("SELECT inmate_id, first_name, last_name FROM inmates");

// Fetch users for approver dropdown
$users_result = $conn->query("SELECT user_id, full_name FROM users");
?>
