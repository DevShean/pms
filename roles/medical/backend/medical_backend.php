<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle update medical record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_medical_record'])) {
    $record_id = intval($_POST['record_id']);
    $inmate_id = intval($_POST['inmate_id']);
    $diagnosis = $conn->real_escape_string($_POST['diagnosis']);
    $treatment = $conn->real_escape_string($_POST['treatment']);
    $medication = $conn->real_escape_string($_POST['medication']);
    $record_date = $_POST['record_date'];

    $sql = "UPDATE medical_records SET diagnosis='$diagnosis', treatment='$treatment', medication='$medication', record_date='$record_date' WHERE record_id=$record_id";
    $conn->query($sql);
    header("Location: ../medical/inmates.php");
    exit();
}

// Handle update full medical record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_full_medical_record'])) {
    $record_id = intval($_POST['record_id']);
    $inmate_id = intval($_POST['inmate_id']);
    $visit_type = $conn->real_escape_string($_POST['visit_type']);
    $diagnosis = $conn->real_escape_string($_POST['diagnosis']);
    $vital_signs = $conn->real_escape_string($_POST['vital_signs']);
    $blood_pressure = $conn->real_escape_string($_POST['blood_pressure']);
    $temperature_c = $_POST['temperature_c'] ? floatval($_POST['temperature_c']) : NULL;
    $pulse_rate = $_POST['pulse_rate'] ? intval($_POST['pulse_rate']) : NULL;
    $respiratory_rate = $_POST['respiratory_rate'] ? intval($_POST['respiratory_rate']) : NULL;
    $treatment = $conn->real_escape_string($_POST['treatment']);
    $medication = $conn->real_escape_string($_POST['medication']);
    $medical_condition = $conn->real_escape_string($_POST['medical_condition']);
    $allergies = $conn->real_escape_string($_POST['allergies']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $next_checkup_date = $_POST['next_checkup_date'] ?: NULL;
    $hospital_referred = $conn->real_escape_string($_POST['hospital_referred']);
    $record_date = $_POST['record_date'] ?: date('Y-m-d');

    // Handle file upload
    $attachment_path = '';
    if (isset($_FILES['attachment_path']) && $_FILES['attachment_path']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../../uploads/medical/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $file_name = basename($_FILES['attachment_path']['name']);
        $file_path = $upload_dir . $file_name;
        if (move_uploaded_file($_FILES['attachment_path']['tmp_name'], $file_path)) {
            $attachment_path = $file_path;
        }
    } else {
        // If no new file, keep existing
        $attachment_path = $conn->real_escape_string($_POST['existing_attachment_path'] ?? '');
    }

    $sql = "UPDATE medical_records SET visit_type='$visit_type', diagnosis='$diagnosis', vital_signs='$vital_signs', blood_pressure='$blood_pressure', temperature_c=" . ($temperature_c !== NULL ? $temperature_c : 'NULL') . ", pulse_rate=" . ($pulse_rate !== NULL ? $pulse_rate : 'NULL') . ", respiratory_rate=" . ($respiratory_rate !== NULL ? $respiratory_rate : 'NULL') . ", treatment='$treatment', medication='$medication', medical_condition='$medical_condition', allergies='$allergies', remarks='$remarks', next_checkup_date=" . ($next_checkup_date ? "'$next_checkup_date'" : 'NULL') . ", hospital_referred='$hospital_referred', attachment_path='$attachment_path', record_date='$record_date' WHERE record_id=$record_id";
    $conn->query($sql);
    // Log the action
    $action = "Updated medical record";
    $details = "Medical record updated for inmate ID $inmate_id.";
    $user_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO system_logs (action, details, user_id) VALUES ('$action', '$details', $user_id)");
    header("Location: medical.php");
    exit();
}

// Handle add medical record
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_medical_record'])) {
    $inmate_id = intval($_POST['inmate_id']);
    $staff_id = $_SESSION['user_id']; // Assuming staff_id is stored in session
    $visit_type = $conn->real_escape_string($_POST['visit_type']);
    $diagnosis = $conn->real_escape_string($_POST['diagnosis']);
    $vital_signs = $conn->real_escape_string($_POST['vital_signs']);
    $blood_pressure = $conn->real_escape_string($_POST['blood_pressure']);
    $temperature_c = $_POST['temperature_c'] ? floatval($_POST['temperature_c']) : NULL;
    $pulse_rate = $_POST['pulse_rate'] ? intval($_POST['pulse_rate']) : NULL;
    $respiratory_rate = $_POST['respiratory_rate'] ? intval($_POST['respiratory_rate']) : NULL;
    $treatment = $conn->real_escape_string($_POST['treatment']);
    $medication = $conn->real_escape_string($_POST['medication']);
    $medical_condition = $conn->real_escape_string($_POST['medical_condition']);
    $allergies = $conn->real_escape_string($_POST['allergies']);
    $remarks = $conn->real_escape_string($_POST['remarks']);
    $next_checkup_date = $_POST['next_checkup_date'] ?: NULL;
    $hospital_referred = $conn->real_escape_string($_POST['hospital_referred']);
    $attachment_path = $conn->real_escape_string($_POST['attachment_path']);
    $record_date = $_POST['record_date'] ?: date('Y-m-d');

    $sql = "INSERT INTO medical_records (inmate_id, staff_id, visit_type, diagnosis, vital_signs, blood_pressure, temperature_c, pulse_rate, respiratory_rate, treatment, medication, medical_condition, allergies, remarks, next_checkup_date, hospital_referred, attachment_path, record_date) VALUES ($inmate_id, $staff_id, '$visit_type', '$diagnosis', '$vital_signs', '$blood_pressure', " . ($temperature_c !== NULL ? $temperature_c : 'NULL') . ", " . ($pulse_rate !== NULL ? $pulse_rate : 'NULL') . ", " . ($respiratory_rate !== NULL ? $respiratory_rate : 'NULL') . ", '$treatment', '$medication', '$medical_condition', '$allergies', '$remarks', " . ($next_checkup_date ? "'$next_checkup_date'" : 'NULL') . ", '$hospital_referred', '$attachment_path', '$record_date')";
    $conn->query($sql);

    // Get inmate name
    $inmate_result = $conn->query("SELECT first_name, last_name FROM inmates WHERE inmate_id = $inmate_id");
    $inmate = $inmate_result->fetch_assoc();
    $inmate_name = $inmate['first_name'] . ' ' . $inmate['last_name'];

    // Log the action
    $action = "Added medical record";
    $details = "Medical record added for inmate $inmate_name.";
    $user_id = $_SESSION['user_id'];
    $conn->query("INSERT INTO system_logs (action, details, user_id) VALUES ('$action', '$details', $user_id)");

    header("Location: ../medical/inmates.php");
    exit();
}
?>
