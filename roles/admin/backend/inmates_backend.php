<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle delete action
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    $conn->query("DELETE FROM inmates WHERE inmate_id = $id");
    header("Location: inmates.php");
    exit();
}

// Handle add inmate
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_inmate'])) {
    $first_name = $conn->real_escape_string($_POST['first_name']);
    $last_name = $conn->real_escape_string($_POST['last_name']);
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];
    $crime = $conn->real_escape_string($_POST['crime']);
    $sentence_years = intval($_POST['sentence_years']);
    $court_details = $conn->real_escape_string($_POST['court_details']);
    $cell_block = $conn->real_escape_string($_POST['cell_block']);
    $admission_date = $_POST['admission_date'];
    $release_date = $_POST['release_date'];
    $status = $_POST['status'];

    $photo_path = '';
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $target_dir = "../../assets/uploads/";
        $target_file = $target_dir . basename($_FILES["photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["photo"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) { 
                $photo_path = $target_file;
            }
        }
    }

    $sql = "INSERT INTO inmates (first_name, last_name, birthdate, gender, crime, sentence_years, court_details, cell_block, admission_date, release_date, status, photo_path) VALUES ('$first_name', '$last_name', '$birthdate', '$gender', '$crime', $sentence_years, '$court_details', '$cell_block', '$admission_date', '$release_date', '$status', '$photo_path')";
    $conn->query($sql);
    header("Location: inmates.php");
    exit();
}

// Handle edit inmate
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_inmate'])) {
    $id = intval($_POST['edit_inmate_id']);
    $first_name = $conn->real_escape_string($_POST['edit_first_name']);
    $last_name = $conn->real_escape_string($_POST['edit_last_name']);
    $birthdate = $_POST['edit_birthdate'];
    $gender = $_POST['edit_gender'];
    $crime = $conn->real_escape_string($_POST['edit_crime']);
    $sentence_years = intval($_POST['edit_sentence_years']);
    $court_details = $conn->real_escape_string($_POST['edit_court_details']);
    $cell_block = $conn->real_escape_string($_POST['edit_cell_block']);
    $admission_date = $_POST['edit_admission_date'];
    $release_date = $_POST['edit_release_date'];
    $status = $_POST['edit_status'];

    $photo_path = $_POST['existing_photo'];
    if (isset($_FILES['edit_photo']) && $_FILES['edit_photo']['error'] == 0) {
        $target_dir = "../../assets/uploads/";
        $target_file = $target_dir . basename($_FILES["edit_photo"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a actual image or fake image
        $check = getimagesize($_FILES["edit_photo"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["edit_photo"]["tmp_name"], $target_file)) {
                $photo_path = $target_file;
            }
        }
    }

    $sql = "UPDATE inmates SET first_name='$first_name', last_name='$last_name', birthdate='$birthdate', gender='$gender', crime='$crime', sentence_years=$sentence_years, court_details='$court_details', cell_block='$cell_block', admission_date='$admission_date', release_date='$release_date', status='$status', photo_path='$photo_path' WHERE inmate_id=$id";
    $conn->query($sql);
    header("Location: inmates.php");
    exit();
}

// Fetch inmates
$result = $conn->query("SELECT * FROM inmates");

// Handle add medical record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_medical_record'])) {
    $inmate_id = intval($_POST['inmate_id']);
    $staff_id = intval($_POST['staff_id']);
    $visit_type = $conn->real_escape_string($_POST['visit_type']);
    $record_date = $_POST['record_date'];

    $sql = "INSERT INTO medical_records (inmate_id, staff_id, visit_type, record_date) VALUES ($inmate_id, $staff_id, '$visit_type', '$record_date')";
    $conn->query($sql);
    header("Location: inmates.php");
    exit();
}

// Fetch statistics
$total_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates")->fetch_assoc()['count'];
$active_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates WHERE status = 'Active'")->fetch_assoc()['count'];
$released_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates WHERE status = 'Released'")->fetch_assoc()['count'];
$transferred_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates WHERE status = 'Transferred'")->fetch_assoc()['count'];
?>
