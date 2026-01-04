<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Fetch all medical records with inmate details
$sql = "SELECT mr.*, i.first_name, i.last_name, i.inmate_id
        FROM medical_records mr
        JOIN inmates i ON mr.inmate_id = i.inmate_id";

if (!empty($search)) {
    $sql .= " WHERE i.first_name LIKE '%$search%' OR i.last_name LIKE '%$search%' OR i.inmate_id LIKE '%$search%'";
}

$sql .= " ORDER BY mr.record_date DESC";
$result = $conn->query($sql);
?>

<div class="container mx-auto px-6 py-10 md:ml-64">
    <div class="flex items-center justify-between mb-10">
        <h2 class="text-4xl font-extrabold text-gray-900 tracking-tight">All Medical Records</h2>
        <div class="text-sm text-gray-500 bg-gray-100 px-4 py-2 rounded-full">
            Medical Staff Portal - Read Only
        </div>
    </div>

    <!-- Search Bar -->
    <div class="mb-6">
        <form method="GET" class="flex gap-2">
            <div class="flex-1 relative">
                <input type="text" name="search" placeholder="Search by inmate name or ID..." 
                    value="<?php echo htmlspecialchars($search); ?>"
                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent shadow-sm">
                <svg class="absolute right-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <button type="submit" class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition-colors font-medium">
                Search
            </button>
            <?php if (!empty($search)): ?>
                <a href="inmates.php" class="bg-gray-300 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-400 transition-colors font-medium">
                    Clear
                </a>
            <?php endif; ?>
        </form>
        <?php if (!empty($search)): ?>
            <p class="text-sm text-gray-600 mt-2">
                Showing results for: <strong><?php echo htmlspecialchars($search); ?></strong>
            </p>
        <?php endif; ?>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="bg-white shadow-lg rounded-2xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inmate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Record Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Visit Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Diagnosis</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Treatment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Medication</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Vital Signs</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php while($row = $result->fetch_assoc()): ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">
                                        <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        <?php echo htmlspecialchars(date('M j, Y', strtotime($row['record_date']))); ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-blue-100 text-blue-800">
                                        <?php echo htmlspecialchars($row['visit_type']); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        <?php echo !empty($row['diagnosis']) ? htmlspecialchars($row['diagnosis']) : 'N/A'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        <?php echo !empty($row['treatment']) ? htmlspecialchars($row['treatment']) : 'N/A'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm text-gray-900 max-w-xs truncate">
                                        <?php echo !empty($row['medication']) ? htmlspecialchars($row['medication']) : 'N/A'; ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">
                                        BP: <?php echo !empty($row['blood_pressure']) ? htmlspecialchars($row['blood_pressure']) : 'N/A'; ?><br>
                                        Temp: <?php echo !empty($row['temperature_c']) ? htmlspecialchars($row['temperature_c']) . '°C' : 'N/A'; ?><br>
                                        Pulse: <?php echo !empty($row['pulse_rate']) ? htmlspecialchars($row['pulse_rate']) . ' bpm' : 'N/A'; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php else: ?>
        <div class="text-center py-20 bg-gradient-to-br from-white to-gray-50 rounded-2xl border border-gray-200 shadow-sm">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-xl font-medium text-gray-500">No medical records found.</p>
            <p class="text-sm text-gray-400 mt-2">Medical records will appear here once added.</p>
        </div>
    <?php endif; ?>
</div>

<?php
$conn->close();
include '../../partials/footer.php';
?>
