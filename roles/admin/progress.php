<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle progress update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_progress'])) {
    $inmate_program_id = intval($_POST['inmate_program_id']);
    $progress = $_POST['progress'];
    $end_date = $_POST['end_date'] ?: NULL;

    // Automatically set end_date to current date if progress is Completed and end_date is not provided
    if ($progress == 'Completed' && !$end_date) {
        $end_date = date('Y-m-d');
    }

    $sql = "UPDATE inmate_programs SET progress='$progress', end_date=" . ($end_date ? "'$end_date'" : "NULL") . " WHERE inmate_program_id=$inmate_program_id";
    $conn->query($sql);
    header("Location: progress.php");
    exit();
}

include '../../partials/header.php';
include '../../partials/sidebar.php';

// Fetch inmate programs with progress
$inmate_programs = $conn->query("
    SELECT ip.*, i.first_name, i.last_name, p.program_name, u.full_name as staff_name
    FROM inmate_programs ip
    INNER JOIN inmates i ON ip.inmate_id = i.inmate_id
    INNER JOIN programs p ON ip.program_id = p.program_id
    LEFT JOIN users u ON ip.staff_id = u.user_id
    ORDER BY ip.start_date DESC
");

// Fetch progress statistics for charts
$progress_stats = $conn->query("
    SELECT progress, COUNT(*) as count
    FROM inmate_programs
    GROUP BY progress
");

// Fetch monthly completion data for line chart
$monthly_completions = $conn->query("
    SELECT DATE_FORMAT(end_date, '%Y-%m') as month, COUNT(*) as completed
    FROM inmate_programs
    WHERE progress = 'Completed' AND end_date IS NOT NULL
    GROUP BY DATE_FORMAT(end_date, '%Y-%m')
    ORDER BY month
");
?>
<main class="flex-1 p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Program Progress Monitoring</h1>
                    <p class="text-sm text-slate-500 mt-1">Track and update rehabilitation program progress</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Progress Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <?php
            $total_programs = 0;
            $stats = [];
            while ($stat = $progress_stats->fetch_assoc()) {
                $stats[$stat['progress']] = $stat['count'];
                $total_programs += $stat['count'];
            }
            ?>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Total Programs</h3>
                        <p class="text-3xl font-bold text-indigo-600"><?php echo $total_programs; ?></p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Completed Programs</h3>
                        <p class="text-3xl font-bold text-green-600"><?php echo $stats['Completed'] ?? 0; ?></p>
                    </div>
                    <div class="p-3 bg-green-50 rounded-lg">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Ongoing Programs</h3>
                        <p class="text-3xl font-bold text-yellow-600"><?php echo $stats['Ongoing'] ?? 0; ?></p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Progress Distribution Pie Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-xl font-semibold text-slate-800 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                    Progress Distribution
                </h3>
                <div class="h-64">
                    <canvas id="progressChart"></canvas>
                </div>
            </div>

            <!-- Monthly Completions Line Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <h3 class="text-xl font-semibold text-slate-800 mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                    Monthly Completions
                </h3>
                <div class="h-64">
                    <canvas id="monthlyChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Inmate Programs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Inmate</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Program</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">End Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Progress</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Staff</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php while ($ip = $inmate_programs->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($ip['first_name'] . ' ' . $ip['last_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo htmlspecialchars($ip['program_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo $ip['start_date']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo $ip['end_date'] ?: 'N/A'; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php
                                    switch ($ip['progress']) {
                                        case 'Ongoing': echo 'bg-yellow-100 text-yellow-800'; break;
                                        case 'Completed': echo 'bg-green-100 text-green-800'; break;
                                        case 'Dropped': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo $ip['progress']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo htmlspecialchars($ip['staff_name'] ?: 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button onclick="updateProgress(<?php echo $ip['inmate_program_id']; ?>)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-md hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Update Progress
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Update Progress Modal -->
<div id="progressModal" class="fixed inset-0 bg-slate-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-slate-900">Update Program Progress</h3>
                    <button id="closeProgressModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="inmate_program_id" id="inmate_program_id">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Progress Status</label>
                        <select name="progress" id="progress" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="Ongoing">Ongoing</option>
                            <option value="Completed">Completed</option>
                            <option value="Dropped">Dropped</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">End Date (Optional)</label>
                        <input type="date" name="end_date" id="end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                        <button type="button" id="cancelProgressModal" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">Cancel</button>
                        <button type="submit" name="update_progress" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Update Progress</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const progressModal = document.getElementById('progressModal');
    const closeProgressModal = document.getElementById('closeProgressModal');
    const cancelProgressModal = document.getElementById('cancelProgressModal');

    closeProgressModal.onclick = function() {
        progressModal.classList.add('hidden');
    }

    cancelProgressModal.onclick = function() {
        progressModal.classList.add('hidden');
    }

    window.onclick = function(event) {
        if (event.target == progressModal) {
            progressModal.classList.add('hidden');
        }
    }

    function updateProgress(inmateProgramId) {
        document.getElementById('inmate_program_id').value = inmateProgramId;
        progressModal.classList.remove('hidden');
    }

    // Progress Distribution Pie Chart
    const progressCtx = document.getElementById('progressChart').getContext('2d');
    const progressChart = new Chart(progressCtx, {
        type: 'pie',
        data: {
            labels: ['Ongoing', 'Completed', 'Dropped'],
            datasets: [{
                data: [
                    <?php echo $stats['Ongoing'] ?? 0; ?>,
                    <?php echo $stats['Completed'] ?? 0; ?>,
                    <?php echo $stats['Dropped'] ?? 0; ?>
                ],
                backgroundColor: [
                    '#fbbf24', // yellow for ongoing
                    '#10b981', // green for completed
                    '#ef4444'  // red for dropped
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                }
            }
        }
    });

    // Monthly Completions Line Chart
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: [
                <?php
                $months = [];
                $completions = [];
                while ($row = $monthly_completions->fetch_assoc()) {
                    $months[] = "'" . $row['month'] . "'";
                    $completions[] = $row['completed'];
                }
                echo implode(',', $months);
                ?>
            ],
            datasets: [{
                label: 'Completions',
                data: [<?php echo implode(',', $completions); ?>],
                borderColor: '#3b82f6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>

<?php include '../../partials/footer.php'; ?>
