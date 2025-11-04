<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$inmate_id = $_GET['inmate_id'];

// Fetch inmate details
$sql_inmate = "SELECT * FROM inmates WHERE inmate_id = ?";
$stmt_inmate = $conn->prepare($sql_inmate);
$stmt_inmate->bind_param("i", $inmate_id);
$stmt_inmate->execute();
$result_inmate = $stmt_inmate->get_result();
$inmate = $result_inmate->fetch_assoc();

// Fetch latest medical record
$sql_medical = "SELECT * FROM medical_records WHERE inmate_id = ? ORDER BY record_date DESC LIMIT 1";
$stmt_medical = $conn->prepare($sql_medical);
$stmt_medical->bind_param("i", $inmate_id);
$stmt_medical->execute();
$result_medical = $stmt_medical->get_result();
$medical_record = $result_medical->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $record_id = $_POST['record_id'];
    $visit_type = $_POST['visit_type'];
    $diagnosis = $_POST['diagnosis'];
    $vital_signs = $_POST['vital_signs'];
    $blood_pressure = $_POST['blood_pressure'];
    $temperature_c = $_POST['temperature_c'];
    $pulse_rate = $_POST['pulse_rate'];
    $respiratory_rate = $_POST['respiratory_rate'];
    $treatment = $_POST['treatment'];
    $medication = $_POST['medication'];
    $medical_condition = $_POST['medical_condition'];
    $allergies = $_POST['allergies'];
    $remarks = $_POST['remarks'];
    $next_checkup_date = $_POST['next_checkup_date'];
    $hospital_referred = $_POST['hospital_referred'];
    $staff_id = $_SESSION['user_id'];

    $attachment_path = $medical_record['attachment_path'] ?? null;
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] == UPLOAD_ERR_OK) {
        $upload_dir = '../../assets/uploads/';
        $attachment_path = $upload_dir . basename($_FILES['attachment']['name']);
        move_uploaded_file($_FILES['attachment']['tmp_name'], $attachment_path);
    }

    if ($record_id) {
        $sql_update = "UPDATE medical_records SET visit_type = ?, diagnosis = ?, vital_signs = ?, blood_pressure = ?, temperature_c = ?, pulse_rate = ?, respiratory_rate = ?, treatment = ?, medication = ?, medical_condition = ?, allergies = ?, remarks = ?, next_checkup_date = ?, hospital_referred = ?, attachment_path = ?, staff_id = ? WHERE record_id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("sssssssssssssssii", $visit_type, $diagnosis, $vital_signs, $blood_pressure, $temperature_c, $pulse_rate, $respiratory_rate, $treatment, $medication, $medical_condition, $allergies, $remarks, $next_checkup_date, $hospital_referred, $attachment_path, $staff_id, $record_id);
    } else {
        $record_date = date('Y-m-d');
        $sql_update = "INSERT INTO medical_records (inmate_id, staff_id, visit_type, diagnosis, vital_signs, blood_pressure, temperature_c, pulse_rate, respiratory_rate, treatment, medication, medical_condition, allergies, remarks, next_checkup_date, hospital_referred, attachment_path, record_date) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param("iissssssssssssssss", $inmate_id, $staff_id, $visit_type, $diagnosis, $vital_signs, $blood_pressure, $temperature_c, $pulse_rate, $respiratory_rate, $treatment, $medication, $medical_condition, $allergies, $remarks, $next_checkup_date, $hospital_referred, $attachment_path, $record_date);
    }
    
    if ($stmt_update->execute()) {
        echo "<p class='text-green-500'>Medical record updated successfully.</p>";
    } else {
        echo "<p class='text-red-500'>Error updating medical record: " . $conn->error . "</p>";
    }
    $stmt_update->close();
}
?>

<div class="max-w-6xl mx-auto px-6 py-10">
    <!-- Header -->
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between mb-10">
        <div>
            <h2 class="text-4xl font-extrabold text-gray-800">Update Medical Record</h2>
            <p class="text-gray-500 mt-1">
                Inmate: 
                <span class="font-semibold text-gray-800">
                    <?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?>
                </span>
            </p>
        </div>
        <a href="../medical/medical_records.php" 
           class="mt-4 md:mt-0 inline-flex items-center gap-2 bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm font-medium px-4 py-2 rounded-lg transition-all duration-300">
           ← Back to Records
        </a>
    </div>

    <!-- Form Card -->
    <div class="bg-white border border-gray-100 shadow-xl rounded-2xl p-8">
        <form method="POST" enctype="multipart/form-data" class="space-y-10">
            <input type="hidden" name="record_id" value="<?php echo $medical_record['record_id'] ?? ''; ?>">

            <!-- Visit Info -->
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Visit Type</label>
                    <div class="relative">
                        <select name="visit_type" 
                            class="w-full appearance-none bg-gray-50 border border-gray-300 rounded-xl py-3 px-4 pr-10 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            <?php
                            $types = ['Routine Checkup', 'Emergency', 'Follow-up', 'Mental Health', 'Other'];
                            foreach ($types as $type) {
                                $selected = ($medical_record && $medical_record['visit_type'] == $type) ? 'selected' : '';
                                echo "<option value='$type' $selected>$type</option>";
                            }
                            ?>
                        </select>
                        <svg class="w-5 h-5 text-gray-500 absolute right-3 top-3 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                </div>

                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Medical Condition</label>
                    <div class="relative">
                        <input type="text" name="medical_condition" value="<?php echo htmlspecialchars($medical_record['medical_condition'] ?? ''); ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded-xl py-3 px-4 pl-10 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        <svg class="w-5 h-5 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 20h9" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16 4a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Vital Signs -->
            <div class="bg-gray-50 border border-gray-200 rounded-xl p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Vital Signs
                </h3>

                <div class="flex flex-wrap gap-6">
                    <?php
                    $vitals = [
                        'Blood Pressure' => 'blood_pressure',
                        'Temperature (°C)' => 'temperature_c',
                        'Pulse Rate' => 'pulse_rate',
                        'Respiratory Rate' => 'respiratory_rate'
                    ];
                    foreach ($vitals as $label => $field) {
                        echo '
                        <div class="flex-1 min-w-[200px]">
                            <label class="block text-sm font-medium text-gray-700 mb-1">'.$label.'</label>
                            <input type="text" name="'.$field.'" value="'.htmlspecialchars($medical_record[$field] ?? '').'" 
                                class="w-full bg-white border border-gray-300 rounded-lg py-2.5 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>';
                    }
                    ?>
                </div>

                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Additional Notes</label>
                    <textarea name="vital_signs" rows="2" 
                        class="w-full bg-white border border-gray-300 rounded-lg py-2.5 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo htmlspecialchars($medical_record['vital_signs'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Diagnosis & Treatment -->
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Diagnosis</label>
                    <textarea name="diagnosis" rows="4" 
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-3 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo htmlspecialchars($medical_record['diagnosis'] ?? ''); ?></textarea>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Treatment</label>
                    <textarea name="treatment" rows="4" 
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-3 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo htmlspecialchars($medical_record['treatment'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Medication & Allergies -->
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Medication</label>
                    <textarea name="medication" rows="3" 
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-3 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo htmlspecialchars($medical_record['medication'] ?? ''); ?></textarea>
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Allergies</label>
                    <textarea name="allergies" rows="3" 
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-3 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo htmlspecialchars($medical_record['allergies'] ?? ''); ?></textarea>
                </div>
            </div>

            <!-- Remarks + Dates -->
            <div class="flex flex-col md:flex-row gap-6">
                <div class="flex-1">
                    <label class="block text-sm font-semibold text-gray-700 mb-2">Remarks</label>
                    <textarea name="remarks" rows="3" 
                        class="w-full bg-gray-50 border border-gray-300 rounded-xl py-3 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"><?php echo htmlspecialchars($medical_record['remarks'] ?? ''); ?></textarea>
                </div>

                <div class="flex-1 flex flex-col gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Next Checkup Date</label>
                        <input type="date" name="next_checkup_date" value="<?php echo htmlspecialchars($medical_record['next_checkup_date'] ?? ''); ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2.5 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Hospital Referred</label>
                        <input type="text" name="hospital_referred" value="<?php echo htmlspecialchars($medical_record['hospital_referred'] ?? ''); ?>" 
                            class="w-full bg-gray-50 border border-gray-300 rounded-xl py-2.5 px-4 text-gray-700 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                    </div>
                </div>
            </div>

            <!-- Attachment -->
            <div class="flex flex-col gap-2 border-t border-gray-200 pt-6">
                <label class="block text-sm font-semibold text-gray-700">Attachment</label>
                <input type="file" name="attachment" 
                    class="block w-full text-sm text-gray-700 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-600 hover:file:bg-blue-100 transition">
                <?php if (!empty($medical_record['attachment_path'])): ?>
                    <p class="text-sm text-gray-500 mt-2">
                        Current file: 
                        <a href="<?php echo htmlspecialchars($medical_record['attachment_path']); ?>" target="_blank" class="text-blue-600 hover:underline">View Attachment</a>
                    </p>
                <?php endif; ?>
            </div>

            <!-- Submit -->
            <div class="flex justify-end pt-6 border-t border-gray-200">
                <button type="submit" 
                    class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-8 py-3 rounded-xl shadow transition-all duration-300 transform hover:scale-[1.02]">
                    💾 Save Record
                </button>
            </div>
        </form>
    </div>
</div>



<?php
$stmt_inmate->close();
$stmt_medical->close();
$conn->close();
include '../../partials/footer.php';
?>
