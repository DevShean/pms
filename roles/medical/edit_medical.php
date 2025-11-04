<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

$record_id = $_GET['record_id'];

// Fetch medical record
$sql_medical = "SELECT mr.*, i.first_name, i.last_name 
                FROM medical_records mr
                JOIN inmates i ON mr.inmate_id = i.inmate_id
                WHERE mr.record_id = ?";
$stmt_medical = $conn->prepare($sql_medical);
$stmt_medical->bind_param("i", $record_id);
$stmt_medical->execute();
$result_medical = $stmt_medical->get_result();
$medical_record = $result_medical->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $visit_type = $_POST['visit_type'];
    $diagnosis = $_POST['diagnosis'];
    $treatment = $_POST['treatment'];
    $medication = $_POST['medication'];
    $allergies = $_POST['allergies'];
    $remarks = $_POST['remarks'];

    $sql_update = "UPDATE medical_records SET visit_type = ?, diagnosis = ?, treatment = ?, medication = ?, allergies = ?, remarks = ? WHERE record_id = ?";
    $stmt_update = $conn->prepare($sql_update);
    $stmt_update->bind_param("ssssssi", $visit_type, $diagnosis, $treatment, $medication, $allergies, $remarks, $record_id);
    
    if ($stmt_update->execute()) {
        echo "<p class='text-green-500'>Medical record updated successfully.</p>";
        // Refresh data
        $stmt_medical->execute();
        $result_medical = $stmt_medical->get_result();
        $medical_record = $result_medical->fetch_assoc();
    } else {
        echo "<p class='text-red-500'>Error updating medical record: " . $conn->error . "</p>";
    }
    $stmt_update->close();
}
?>

<div class="container mx-auto px-6 py-8">
    <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Medical Record for <?php echo htmlspecialchars($medical_record['first_name'] . ' ' . $medical_record['last_name']); ?></h2>
    
    <div class="bg-white shadow-lg rounded-lg p-6">
        <form method="POST" action="">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="visit_type" class="block text-sm font-medium text-gray-700 mb-1">Visit Type</label>
                    <select id="visit_type" name="visit_type" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="Follow-up" <?php if($medical_record['visit_type'] == 'Follow-up') echo 'selected'; ?>>Follow-up</option>
                        <option value="Emergency" <?php if($medical_record['visit_type'] == 'Emergency') echo 'selected'; ?>>Emergency</option>
                        <option value="Checkup" <?php if($medical_record['visit_type'] == 'Checkup') echo 'selected'; ?>>Checkup</option>
                    </select>
                </div>
            </div>
            <div class="mt-6">
                <label for="diagnosis" class="block text-sm font-medium text-gray-700 mb-1">Diagnosis</label>
                <textarea id="diagnosis" name="diagnosis" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($medical_record['diagnosis']); ?></textarea>
            </div>
            <div class="mt-6">
                <label for="treatment" class="block text-sm font-medium text-gray-700 mb-1">Treatment</label>
                <textarea id="treatment" name="treatment" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($medical_record['treatment']); ?></textarea>
            </div>
            <div class="mt-6">
                <label for="medication" class="block text-sm font-medium text-gray-700 mb-1">Medication</label>
                <textarea id="medication" name="medication" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($medical_record['medication']); ?></textarea>
            </div>
            <div class="mt-6">
                <label for="allergies" class="block text-sm font-medium text-gray-700 mb-1">Allergies</label>
                <textarea id="allergies" name="allergies" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($medical_record['allergies']); ?></textarea>
            </div>
            <div class="mt-6">
                <label for="remarks" class="block text-sm font-medium text-gray-700 mb-1">Remarks</label>
                <textarea id="remarks" name="remarks" rows="4" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500"><?php echo htmlspecialchars($medical_record['remarks']); ?></textarea>
            </div>
            <div class="mt-6 text-right">
                <button type="submit" class="bg-blue-600 text-white py-2 px-6 rounded-lg hover:bg-blue-700 transition duration-300">Update Record</button>
            </div>
        </form>
    </div>
</div>

<?php
$stmt_medical->close();
$conn->close();
include '../../partials/footer.php';
?>
