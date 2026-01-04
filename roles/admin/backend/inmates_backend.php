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
    $place_of_birth = $conn->real_escape_string($_POST['place_of_birth'] ?? '');
    $gender = $_POST['gender'];
    $marital_status = $conn->real_escape_string($_POST['marital_status'] ?? '');
    $height = $conn->real_escape_string($_POST['height'] ?? '');
    $weight = $conn->real_escape_string($_POST['weight'] ?? '');
    $hair_description = $conn->real_escape_string($_POST['hair_description'] ?? '');
    $complexion = $conn->real_escape_string($_POST['complexion'] ?? '');
    $eyes_description = $conn->real_escape_string($_POST['eyes_description'] ?? '');
    $citizenship = $conn->real_escape_string($_POST['citizenship'] ?? '');
    $religion = $conn->real_escape_string($_POST['religion'] ?? '');
    $race = $conn->real_escape_string($_POST['race'] ?? '');
    $occupation = $conn->real_escape_string($_POST['occupation'] ?? '');
    $educational_attainment = $conn->real_escape_string($_POST['educational_attainment'] ?? '');
    $course = $conn->real_escape_string($_POST['course'] ?? '');
    $school_attended = $conn->real_escape_string($_POST['school_attended'] ?? '');
    $permanent_address = $conn->real_escape_string($_POST['permanent_address'] ?? '');
    $provincial_address = $conn->real_escape_string($_POST['provincial_address'] ?? '');
    $no_of_children = isset($_POST['no_of_children']) && $_POST['no_of_children'] !== '' ? intval($_POST['no_of_children']) : null;
    $father_name = $conn->real_escape_string($_POST['father_name'] ?? '');
    $father_address = $conn->real_escape_string($_POST['father_address'] ?? '');
    $mother_name = $conn->real_escape_string($_POST['mother_name'] ?? '');
    $mother_address = $conn->real_escape_string($_POST['mother_address'] ?? '');
    $wife_clw_name = $conn->real_escape_string($_POST['wife_clw_name'] ?? '');
    $wife_clw_address = $conn->real_escape_string($_POST['wife_clw_address'] ?? '');
    $relative_name = $conn->real_escape_string($_POST['relative_name'] ?? '');
    $relative_address = $conn->real_escape_string($_POST['relative_address'] ?? '');
    $crime = $conn->real_escape_string($_POST['crime']);
    $sentence_years = intval($_POST['sentence_years']);
    $court_details = $conn->real_escape_string($_POST['court_details']);
    $cell_block = $conn->real_escape_string($_POST['cell_block']);
    $admission_date = $_POST['admission_date'];
    $release_date = $_POST['release_date'] ?? null;
    $status = $_POST['status'];
    $contact_number = $conn->real_escape_string($_POST['contact_number'] ?? '');
    $return_rate = $conn->real_escape_string($_POST['return_rate'] ?? '');
    $date_time_received = $_POST['date_time_received'] ?? null;
    $turned_over_by = $conn->real_escape_string($_POST['turned_over_by'] ?? '');
    $receiving_duty_officer = $conn->real_escape_string($_POST['receiving_duty_officer'] ?? '');
    $offense_charged = $conn->real_escape_string($_POST['offense_charged'] ?? '');
    $criminal_case_number = $conn->real_escape_string($_POST['criminal_case_number'] ?? '');
    $case_court = $conn->real_escape_string($_POST['case_court'] ?? '');
    $case_status = $conn->real_escape_string($_POST['case_status'] ?? '');
    $prisoner_property = $conn->real_escape_string($_POST['prisoner_property'] ?? '');
    $property_receipt_number = $conn->real_escape_string($_POST['property_receipt_number'] ?? '');

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

    // Handle NULL values properly in SQL
    $no_of_children_sql = is_null($no_of_children) ? 'NULL' : $no_of_children;
    $release_date_sql = is_null($release_date) || $release_date === '' ? 'NULL' : "'$release_date'";
    $date_time_received_sql = is_null($date_time_received) || $date_time_received === '' ? 'NULL' : "'$date_time_received'";

    $sql = "INSERT INTO inmates (first_name, last_name, birthdate, place_of_birth, gender, marital_status, height, weight, hair_description, complexion, eyes_description, citizenship, religion, race, occupation, educational_attainment, course, school_attended, permanent_address, provincial_address, no_of_children, father_name, father_address, mother_name, mother_address, wife_clw_name, wife_clw_address, relative_name, relative_address, crime, sentence_years, court_details, cell_block, admission_date, release_date, status, photo_path, contact_number, return_rate, date_time_received, turned_over_by, receiving_duty_officer, offense_charged, criminal_case_number, case_court, case_status, prisoner_property, property_receipt_number) 
    VALUES ('$first_name', '$last_name', '$birthdate', '$place_of_birth', '$gender', '$marital_status', '$height', '$weight', '$hair_description', '$complexion', '$eyes_description', '$citizenship', '$religion', '$race', '$occupation', '$educational_attainment', '$course', '$school_attended', '$permanent_address', '$provincial_address', $no_of_children_sql, '$father_name', '$father_address', '$mother_name', '$mother_address', '$wife_clw_name', '$wife_clw_address', '$relative_name', '$relative_address', '$crime', $sentence_years, '$court_details', '$cell_block', '$admission_date', $release_date_sql, '$status', '$photo_path', '$contact_number', '$return_rate', $date_time_received_sql, '$turned_over_by', '$receiving_duty_officer', '$offense_charged', '$criminal_case_number', '$case_court', '$case_status', '$prisoner_property', '$property_receipt_number')";
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
    $place_of_birth = $conn->real_escape_string($_POST['edit_place_of_birth'] ?? '');
    $gender = $_POST['edit_gender'];
    $marital_status = $conn->real_escape_string($_POST['edit_marital_status'] ?? '');
    $height = $conn->real_escape_string($_POST['edit_height'] ?? '');
    $weight = $conn->real_escape_string($_POST['edit_weight'] ?? '');
    $hair_description = $conn->real_escape_string($_POST['edit_hair_description'] ?? '');
    $complexion = $conn->real_escape_string($_POST['edit_complexion'] ?? '');
    $eyes_description = $conn->real_escape_string($_POST['edit_eyes_description'] ?? '');
    $citizenship = $conn->real_escape_string($_POST['edit_citizenship'] ?? '');
    $religion = $conn->real_escape_string($_POST['edit_religion'] ?? '');
    $race = $conn->real_escape_string($_POST['edit_race'] ?? '');
    $occupation = $conn->real_escape_string($_POST['edit_occupation'] ?? '');
    $educational_attainment = $conn->real_escape_string($_POST['edit_educational_attainment'] ?? '');
    $course = $conn->real_escape_string($_POST['edit_course'] ?? '');
    $school_attended = $conn->real_escape_string($_POST['edit_school_attended'] ?? '');
    $permanent_address = $conn->real_escape_string($_POST['edit_permanent_address'] ?? '');
    $provincial_address = $conn->real_escape_string($_POST['edit_provincial_address'] ?? '');
    $no_of_children = isset($_POST['edit_no_of_children']) && $_POST['edit_no_of_children'] !== '' ? intval($_POST['edit_no_of_children']) : null;
    $father_name = $conn->real_escape_string($_POST['edit_father_name'] ?? '');
    $father_address = $conn->real_escape_string($_POST['edit_father_address'] ?? '');
    $mother_name = $conn->real_escape_string($_POST['edit_mother_name'] ?? '');
    $mother_address = $conn->real_escape_string($_POST['edit_mother_address'] ?? '');
    $wife_clw_name = $conn->real_escape_string($_POST['edit_wife_clw_name'] ?? '');
    $wife_clw_address = $conn->real_escape_string($_POST['edit_wife_clw_address'] ?? '');
    $relative_name = $conn->real_escape_string($_POST['edit_relative_name'] ?? '');
    $relative_address = $conn->real_escape_string($_POST['edit_relative_address'] ?? '');
    $crime = $conn->real_escape_string($_POST['edit_crime']);
    $sentence_years = intval($_POST['edit_sentence_years']);
    $court_details = $conn->real_escape_string($_POST['edit_court_details']);
    $cell_block = $conn->real_escape_string($_POST['edit_cell_block']);
    $admission_date = $_POST['edit_admission_date'];
    $release_date = $_POST['edit_release_date'] ?? null;
    $status = $_POST['edit_status'];
    $contact_number = $conn->real_escape_string($_POST['edit_contact_number'] ?? '');
    $return_rate = $conn->real_escape_string($_POST['edit_return_rate'] ?? '');
    $date_time_received = $_POST['edit_date_time_received'] ?? null;
    $turned_over_by = $conn->real_escape_string($_POST['edit_turned_over_by'] ?? '');
    $receiving_duty_officer = $conn->real_escape_string($_POST['edit_receiving_duty_officer'] ?? '');
    $offense_charged = $conn->real_escape_string($_POST['edit_offense_charged'] ?? '');
    $criminal_case_number = $conn->real_escape_string($_POST['edit_criminal_case_number'] ?? '');
    $case_court = $conn->real_escape_string($_POST['edit_case_court'] ?? '');
    $case_status = $conn->real_escape_string($_POST['edit_case_status'] ?? '');
    $prisoner_property = $conn->real_escape_string($_POST['edit_prisoner_property'] ?? '');
    $property_receipt_number = $conn->real_escape_string($_POST['edit_property_receipt_number'] ?? '');

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

    // Handle NULL values properly in SQL
    $no_of_children_sql = is_null($no_of_children) ? 'NULL' : $no_of_children;
    $release_date_sql = is_null($release_date) || $release_date === '' ? 'NULL' : "'$release_date'";
    $date_time_received_sql = is_null($date_time_received) || $date_time_received === '' ? 'NULL' : "'$date_time_received'";

    $sql = "UPDATE inmates SET first_name='$first_name', last_name='$last_name', birthdate='$birthdate', place_of_birth='$place_of_birth', gender='$gender', marital_status='$marital_status', height='$height', weight='$weight', hair_description='$hair_description', complexion='$complexion', eyes_description='$eyes_description', citizenship='$citizenship', religion='$religion', race='$race', occupation='$occupation', educational_attainment='$educational_attainment', course='$course', school_attended='$school_attended', permanent_address='$permanent_address', provincial_address='$provincial_address', no_of_children=$no_of_children_sql, father_name='$father_name', father_address='$father_address', mother_name='$mother_name', mother_address='$mother_address', wife_clw_name='$wife_clw_name', wife_clw_address='$wife_clw_address', relative_name='$relative_name', relative_address='$relative_address', crime='$crime', sentence_years=$sentence_years, court_details='$court_details', cell_block='$cell_block', admission_date='$admission_date', release_date=$release_date_sql, status='$status', photo_path='$photo_path', contact_number='$contact_number', return_rate='$return_rate', date_time_received=$date_time_received_sql, turned_over_by='$turned_over_by', receiving_duty_officer='$receiving_duty_officer', offense_charged='$offense_charged', criminal_case_number='$criminal_case_number', case_court='$case_court', case_status='$case_status', prisoner_property='$prisoner_property', property_receipt_number='$property_receipt_number' WHERE inmate_id=$id";
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
