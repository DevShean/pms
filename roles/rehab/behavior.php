<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle behavior log submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_behavior_log'])) {
    $inmate_id = intval($_POST['inmate_id']);
    $notes = $conn->real_escape_string($_POST['notes']);
    $behavior_rating = $_POST['behavior_rating'];
    $staff_id = $_SESSION['user_id'];

    // Check for cooldown: 10 days since last rating by this staff for this inmate
    $recent_log = $conn->query("SELECT * FROM behavior_logs WHERE inmate_id = $inmate_id AND staff_id = $staff_id AND log_date >= DATE_SUB(CURDATE(), INTERVAL 10 DAY) ORDER BY log_date DESC LIMIT 1");

    if ($recent_log->num_rows > 0) {
        $_SESSION['error'] = "You can only rate this inmate once every 10 days.";
        header("Location: behavior.php");
        exit();
    }

    $sql = "INSERT INTO behavior_logs (inmate_id, staff_id, notes, behavior_rating) VALUES ($inmate_id, $staff_id, '$notes', '$behavior_rating')";
    $conn->query($sql);
    header("Location: behavior.php");
    exit();
}

include '../../partials/header.php';
include '../../partials/sidebar.php';

// Fetch inmates for dropdown
$inmates = $conn->query("SELECT inmate_id, first_name, last_name FROM inmates WHERE status='Active'");

// Fetch behavior logs
$behavior_logs = $conn->query("
    SELECT bl.*, i.first_name, i.last_name, u.full_name as staff_name
    FROM behavior_logs bl
    INNER JOIN inmates i ON bl.inmate_id = i.inmate_id
    LEFT JOIN users u ON bl.staff_id = u.user_id
    ORDER BY bl.log_date DESC
");
?>
<main class="flex-1 p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Behavior Evaluation</h1>
                    <p class="text-sm text-slate-500 mt-1">Monitor and record inmate behavior assessments</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Add Behavior Log -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">Record Behavior Evaluation</h2>
                    <p class="text-sm text-slate-500 mt-1">Add a new behavior assessment for an inmate</p>
                </div>
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
            </div>
            <form method="post" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="inmate_id" class="block text-sm font-medium text-slate-700 mb-2">Select Inmate</label>
                        <select name="inmate_id" id="inmate_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="">Select Inmate</option>
                            <?php while ($inmate = $inmates->fetch_assoc()): ?>
                            <option value="<?php echo $inmate['inmate_id']; ?>"><?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label for="behavior_rating" class="block text-sm font-medium text-slate-700 mb-2">Behavior Rating</label>
                        <select name="behavior_rating" id="behavior_rating" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="Excellent">Excellent</option>
                            <option value="Good">Good</option>
                            <option value="Fair">Fair</option>
                            <option value="Poor">Poor</option>
                        </select>
                    </div>
                </div>
                <div>
                    <label for="notes" class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                    <textarea name="notes" id="notes" rows="4" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors resize-none" placeholder="Enter detailed notes about the inmate's behavior..."></textarea>
                </div>
                <div class="flex justify-end">
                    <button type="submit" name="add_behavior_log" class="inline-flex items-center px-6 py-2.5 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Record Evaluation
                    </button>
                </div>
            </form>
        </div>

        <!-- Behavior Logs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold text-slate-800">Behavior Evaluation History</h2>
                        <p class="text-sm text-slate-500 mt-1">Complete history of behavior assessments</p>
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
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Staff</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php while ($log = $behavior_logs->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($log['first_name'] . ' ' . $log['last_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo $log['log_date']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php
                                    switch ($log['behavior_rating']) {
                                        case 'Excellent': echo 'bg-green-100 text-green-800'; break;
                                        case 'Good': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'Fair': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'Poor': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo $log['behavior_rating']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 max-w-xs truncate" title="<?php echo htmlspecialchars($log['notes']); ?>"><?php echo htmlspecialchars($log['notes']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo htmlspecialchars($log['staff_name'] ?: 'N/A'); ?></td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<?php include '../../partials/footer.php'; ?>
