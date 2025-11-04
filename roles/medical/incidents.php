<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $inmate_id = $_POST['inmate_id'];
    $incident_date = $_POST['incident_date'];
    $description = $_POST['description'];
    $incident_type = $_POST['incident_type'];
    $severity_level = $_POST['severity_level'];
    $location = $_POST['location'];
    $reported_by = $_POST['reported_by'];
    $witnesses = $_POST['witnesses'];
    $action_taken = $_POST['action_taken'];
    $remarks = $_POST['remarks'];
    $staff_id = $_SESSION['user_id'];

    $sql_insert = "INSERT INTO incidents (inmate_id, staff_id, incident_date, description, incident_type, severity_level, location, reported_by, witnesses, action_taken, remarks) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt_insert = $conn->prepare($sql_insert);
    $stmt_insert->bind_param("iisssssssss", $inmate_id, $staff_id, $incident_date, $description, $incident_type, $severity_level, $location, $reported_by, $witnesses, $action_taken, $remarks);
    
    if ($stmt_insert->execute()) {
        echo "<p class='text-green-500'>Incident logged successfully.</p>";
    } else {
        echo "<p class='text-red-500'>Error logging incident: " . $conn->error . "</p>";
    }
    $stmt_insert->close();
}

// Fetch all inmates for dropdown
$sql_inmates = "SELECT inmate_id, first_name, last_name FROM inmates ORDER BY last_name, first_name";
$result_inmates = $conn->query($sql_inmates);

// Fetch all incidents
$sql_incidents = "SELECT inc.*, i.first_name, i.last_name 
                  FROM incidents inc
                  JOIN inmates i ON inc.inmate_id = i.inmate_id
                  ORDER BY inc.incident_date DESC";
$result_incidents = $conn->query($sql_incidents);
?>

<main class="flex-1 p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Medical Incidents</h1>
                    <p class="text-sm text-slate-500 mt-1">Log and track medical incidents and emergencies</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Log Incident Form -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">Log Medical Incident</h2>
                    <p class="text-sm text-slate-500 mt-1">Record a new medical incident or emergency</p>
                </div>
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <form method="POST" action="" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="inmate_id" class="block text-sm font-medium text-slate-700 mb-2">Inmate</label>
                        <select id="inmate_id" name="inmate_id" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            <option value="">Select Inmate</option>
                            <?php while($inmate = $result_inmates->fetch_assoc()): ?>
                                <option value="<?php echo $inmate['inmate_id']; ?>"><?php echo htmlspecialchars($inmate['last_name'] . ', ' . $inmate['first_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label for="incident_date" class="block text-sm font-medium text-slate-700 mb-2">Incident Date</label>
                        <input type="date" id="incident_date" name="incident_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="incident_type" class="block text-sm font-medium text-slate-700 mb-2">Incident Type</label>
                        <select id="incident_type" name="incident_type" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            <option value="Violence">Violence</option>
                            <option value="Contraband">Contraband</option>
                            <option value="Escape Attempt">Escape Attempt</option>
                            <option value="Health Emergency">Health Emergency</option>
                            <option value="Other" selected>Other</option>
                        </select>
                    </div>
                    <div>
                        <label for="severity_level" class="block text-sm font-medium text-slate-700 mb-2">Severity Level</label>
                        <select id="severity_level" name="severity_level" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" required>
                            <option value="Low" selected>Low</option>
                            <option value="Medium">Medium</option>
                            <option value="High">High</option>
                            <option value="Critical">Critical</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="location" class="block text-sm font-medium text-slate-700 mb-2">Location</label>
                        <input type="text" id="location" name="location" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Enter incident location">
                    </div>
                    <div>
                        <label for="reported_by" class="block text-sm font-medium text-slate-700 mb-2">Reported By</label>
                        <input type="text" id="reported_by" name="reported_by" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" placeholder="Enter reporter name">
                    </div>
                </div>
                <div>
                    <label for="witnesses" class="block text-sm font-medium text-slate-700 mb-2">Witnesses</label>
                    <textarea id="witnesses" name="witnesses" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none" placeholder="List any witnesses..."></textarea>
                </div>
                <div>
                    <label for="description" class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                    <textarea id="description" name="description" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none" placeholder="Provide detailed description of the incident..." required></textarea>
                </div>
                <div>
                    <label for="action_taken" class="block text-sm font-medium text-slate-700 mb-2">Action Taken</label>
                    <textarea id="action_taken" name="action_taken" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none" placeholder="Describe actions taken in response..."></textarea>
                </div>
                <div>
                    <label for="remarks" class="block text-sm font-medium text-slate-700 mb-2">Remarks</label>
                    <textarea id="remarks" name="remarks" rows="2" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none" placeholder="Additional remarks or notes..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Log Incident
                    </button>
                </div>
            </form>
        </div>

        <!-- Incident Log Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">Incident Log</h2>
                        <p class="text-sm text-slate-500 mt-1">Complete history of medical incidents</p>
                    </div>
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Inmate</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Severity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Location</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Description</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Reported By</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Witnesses</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Action Taken</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Remarks</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php if ($result_incidents->num_rows > 0): ?>
                            <?php while($row = $result_incidents->fetch_assoc()): ?>
                                <tr class="hover:bg-slate-50 transition-colors duration-150">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo htmlspecialchars($row['incident_date']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php
                                            switch ($row['incident_type']) {
                                                case 'Violence': echo 'bg-red-100 text-red-800'; break;
                                                case 'Contraband': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'Escape Attempt': echo 'bg-orange-100 text-orange-800'; break;
                                                case 'Health Emergency': echo 'bg-purple-100 text-purple-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo htmlspecialchars($row['incident_type']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php
                                            switch ($row['severity_level']) {
                                                case 'Low': echo 'bg-green-100 text-green-800'; break;
                                                case 'Medium': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'High': echo 'bg-orange-100 text-orange-800'; break;
                                                case 'Critical': echo 'bg-red-100 text-red-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo htmlspecialchars($row['severity_level']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                            <?php
                                            switch ($row['status']) {
                                                case 'Resolved': echo 'bg-green-100 text-green-800'; break;
                                                case 'Under Investigation': echo 'bg-yellow-100 text-yellow-800'; break;
                                                case 'Reported': echo 'bg-blue-100 text-blue-800'; break;
                                                case 'Dismissed': echo 'bg-gray-100 text-gray-800'; break;
                                                default: echo 'bg-gray-100 text-gray-800';
                                            }
                                            ?>">
                                            <?php echo htmlspecialchars($row['status']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['description']); ?>"><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo htmlspecialchars($row['reported_by']); ?></td>
                                    <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['witnesses']); ?>"><?php echo htmlspecialchars($row['witnesses']); ?></td>
                                    <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['action_taken']); ?>"><?php echo htmlspecialchars($row['action_taken']); ?></td>
                                    <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="<?php echo htmlspecialchars($row['remarks']); ?>"><?php echo htmlspecialchars($row['remarks']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="11" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center">
                                        <svg class="w-12 h-12 text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                        </svg>
                                        <p class="text-slate-500 text-sm">No incidents found.</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php
$conn->close();
include '../../partials/footer.php';
?>
