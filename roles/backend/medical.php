<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Fetch all medical records
$sql = "SELECT mr.*, i.first_name, i.last_name 
        FROM medical_records mr
        JOIN inmates i ON mr.inmate_id = i.inmate_id
        ORDER BY mr.record_date DESC";
$result = $conn->query($sql);
?>

<div class="container mx-auto px-6 py-10 md:ml-64">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-4xl font-extrabold text-gray-800 tracking-tight">Medical Records</h2>
        <a href="add_record.php" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
            + Add Record
        </a>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 hover:shadow-2xl shadow-md rounded-2xl p-6 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Record Date: <?php echo htmlspecialchars(date('F j, Y', strtotime($row['record_date']))); ?>
                            </p>
                        </div>
                        <div class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-semibold uppercase">
                            <?php echo htmlspecialchars($row['visit_type']); ?>
                        </div>
                    </div>

                    <div class="space-y-3 text-gray-700">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <p><span class="font-semibold text-gray-800">Diagnosis:</span> <?php echo htmlspecialchars($row['diagnosis']); ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <p><span class="font-semibold text-gray-800">Treatment:</span> <?php echo htmlspecialchars($row['treatment']); ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/></svg>
                            <p><span class="font-semibold text-gray-800">Medication:</span> <?php echo htmlspecialchars($row['medication']); ?></p>
                        </div>

                        <div class="border-t border-gray-300 pt-4 mt-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                                <span>Vital Signs</span>
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                    <p><span class="font-semibold text-blue-700">BP:</span> <?php echo htmlspecialchars($row['blood_pressure']); ?></p>
                                </div>
                                <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                                    <p><span class="font-semibold text-red-700">Temp:</span> <?php echo htmlspecialchars($row['temperature_c']); ?>°C</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                    <p><span class="font-semibold text-green-700">Pulse:</span> <?php echo htmlspecialchars($row['pulse_rate']); ?> bpm</p>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                                    <p><span class="font-semibold text-purple-700">Resp Rate:</span> <?php echo htmlspecialchars($row['respiratory_rate']); ?> bpm</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-300 pt-4 mt-4 space-y-2 text-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Condition:</span> <?php echo htmlspecialchars($row['medical_condition']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Allergies:</span> <?php echo htmlspecialchars($row['allergies']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Next Checkup:</span> <?php echo htmlspecialchars($row['next_checkup_date']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Referred To:</span> <?php echo htmlspecialchars($row['hospital_referred']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <a href="edit_record.php?id=<?php echo $row['record_id']; ?>" class="inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center text-gray-500 py-20 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm border border-gray-200">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-xl font-medium">No medical records found.</p>
            <p class="text-sm mt-2">Start by adding a new medical record.</p>
        </div>
    <?php endif; ?>
</div>


<?php
$conn->close();
include '../../partials/footer.php';
?>
