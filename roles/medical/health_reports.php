<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Build filter conditions
$where_conditions = [];

if (!empty($_POST['filter_start_date'])) {
    $start_date = $conn->real_escape_string($_POST['filter_start_date']);
    $where_conditions[] = "record_date >= '$start_date'";
}

if (!empty($_POST['filter_end_date'])) {
    $end_date = $conn->real_escape_string($_POST['filter_end_date']);
    $where_conditions[] = "record_date <= '$end_date'";
}

if (!empty($_POST['filter_month'])) {
    $filter_month = $conn->real_escape_string($_POST['filter_month']);
    $where_conditions[] = "DATE_FORMAT(record_date, '%Y-%m') = '$filter_month'";
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Fetch visit type distribution
$visit_types_sql = "SELECT visit_type, COUNT(*) as count FROM medical_records $where_clause GROUP BY visit_type ORDER BY count DESC";
$visit_types_result = $conn->query($visit_types_sql);
$visit_types_labels = [];
$visit_types_data = [];
while ($row = $visit_types_result->fetch_assoc()) {
    $visit_types_labels[] = $row['visit_type'];
    $visit_types_data[] = (int)$row['count'];
}

// Fetch top diagnoses
if (!empty($where_clause)) {
    $diagnoses_sql = "SELECT diagnosis, COUNT(*) as count FROM medical_records $where_clause AND diagnosis != '' GROUP BY diagnosis ORDER BY count DESC LIMIT 10";
} else {
    $diagnoses_sql = "SELECT diagnosis, COUNT(*) as count FROM medical_records WHERE diagnosis != '' GROUP BY diagnosis ORDER BY count DESC LIMIT 10";
}
$diagnoses_result = $conn->query($diagnoses_sql);
$diagnoses_labels = [];
$diagnoses_data = [];
while ($row = $diagnoses_result->fetch_assoc()) {
    $diagnoses_labels[] = $row['diagnosis'];
    $diagnoses_data[] = (int)$row['count'];
}

// Fetch monthly visits
$monthly_sql = "SELECT DATE_FORMAT(record_date, '%Y-%m') as month, COUNT(*) as count FROM medical_records $where_clause GROUP BY month ORDER BY month ASC";
$monthly_result = $conn->query($monthly_sql);
$monthly_labels = [];
$monthly_data = [];
while ($row = $monthly_result->fetch_assoc()) {
    $monthly_labels[] = $row['month'];
    $monthly_data[] = (int)$row['count'];
}

$conn->close();
?>

<main class="flex-1 p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Health Reports & Analytics</h2>
                    <p class="text-sm text-slate-500 mt-1">Comprehensive medical data analysis and reporting</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Filter & Export Section -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">Filter & Export Medical Reports</h2>
                    <p class="text-sm text-slate-500 mt-1">Filter data and download comprehensive Excel reports of inmate medical records</p>
                </div>
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>

            <!-- Filter Section -->
            <form method="post" class="mb-6">
                <div class="bg-slate-50 rounded-lg p-4 mb-4">
                    <h3 class="text-sm font-semibold text-slate-800 mb-4">Filter Report By:</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Date Range Filter -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
                            <input type="date" name="filter_start_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?php echo htmlspecialchars($_POST['filter_start_date'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">End Date</label>
                            <input type="date" name="filter_end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?php echo htmlspecialchars($_POST['filter_end_date'] ?? ''); ?>">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Or Filter by Month</label>
                            <input type="month" name="filter_month" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500" value="<?php echo htmlspecialchars($_POST['filter_month'] ?? ''); ?>">
                        </div>
                    </div>
                    <p class="text-xs text-slate-500 mt-2">Tip: Use Start/End dates for inclusive date range filtering, or use the Month filter for a specific month</p>
                </div>

                <div class="flex justify-between items-center gap-3 mb-6">
                    <div class="flex gap-3">
                        <button type="submit" class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path>
                            </svg>
                            Apply Filters
                        </button>
                        <button type="button" onclick="document.querySelector('form').reset(); location.reload();" class="inline-flex items-center px-6 py-3 text-sm font-medium text-slate-700 bg-slate-200 rounded-lg hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                            Clear Filters
                        </button>
                    </div>
                </div>
            </form>

            <div class="bg-slate-50 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-slate-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-slate-800 mb-1">Report Contents</h4>
                        <p class="text-sm text-slate-600">The Excel report includes all medical records with inmate details, visit types, diagnoses, treatments, and timestamps for comprehensive analysis.</p>
                    </div>
                </div>
            </div>
            <div class="flex justify-start">
                <a href="generate_excel.php" class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Generate Excel Report
                </a>
            </div>
        </div>

        <!-- Charts Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Visit Types Pie Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800">Visit Types Distribution</h3>
                        <p class="text-sm text-slate-500 mt-1">Breakdown of medical visit categories</p>
                    </div>
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                    </svg>
                </div>
                <div class="h-80">
                    <canvas id="visitTypesChart"></canvas>
                </div>
            </div>

            <!-- Top Diagnoses Bar Chart -->
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-xl font-semibold text-slate-800">Top Diagnoses</h3>
                        <p class="text-sm text-slate-500 mt-1">Most common medical conditions</p>
                    </div>
                    <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="h-80">
                    <canvas id="diagnosesChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Monthly Visits Line Chart -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-semibold text-slate-800">Monthly Visits Trend</h3>
                    <p class="text-sm text-slate-500 mt-1">Medical visit patterns over time</p>
                </div>
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4"></path>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                </svg>
            </div>
            <div class="h-80">
                <canvas id="monthlyChart"></canvas>
            </div>
        </div>
    </div>
</main>

<!-- Chart.js Script -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Visit Types Pie Chart
    const visitTypesCtx = document.getElementById('visitTypesChart');
    if (visitTypesCtx) {
        new Chart(visitTypesCtx, {
            type: 'pie',
            data: {
                labels: <?php echo json_encode($visit_types_labels); ?>,
                datasets: [{
                    data: <?php echo json_encode($visit_types_data); ?>,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.6)',
                        'rgba(255, 99, 132, 0.6)',
                        'rgba(255, 205, 86, 0.6)',
                        'rgba(75, 192, 192, 0.6)',
                        'rgba(153, 102, 255, 0.6)'
                    ],
                    borderColor: [
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 205, 86, 1)',
                        'rgba(75, 192, 192, 1)',
                        'rgba(153, 102, 255, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom' }
                }
            }
        });
    }

    // Top Diagnoses Bar Chart
    const diagnosesCtx = document.getElementById('diagnosesChart');
    if (diagnosesCtx) {
        new Chart(diagnosesCtx, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($diagnoses_labels); ?>,
                datasets: [{
                    label: 'Number of Cases',
                    data: <?php echo json_encode($diagnoses_data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.6)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                },
                plugins: {
                    legend: { display: false }
                }
            }
        });
    }

    // Monthly Visits Line Chart
    const monthlyCtx = document.getElementById('monthlyChart');
    if (monthlyCtx) {
        new Chart(monthlyCtx, {
            type: 'line',
            data: {
                labels: <?php echo json_encode($monthly_labels); ?>,
                datasets: [{
                    label: 'Visits',
                    data: <?php echo json_encode($monthly_data); ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { precision: 0 }
                    }
                }
            }
        });
    }
</script>

<?php
include '../../partials/footer.php';
?>
