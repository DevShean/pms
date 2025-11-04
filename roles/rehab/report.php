<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle report generation
if (isset($_POST['generate_report'])) {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="inmate_progress_report.csv"');

    $output = fopen('php://output', 'w');

    // CSV headers
    fputcsv($output, ['Inmate Name', 'Program Name', 'Start Date', 'End Date', 'Progress', 'Behavior Rating', 'Notes']);

    // Fetch data
    $report_data = $conn->query("
        SELECT i.first_name, i.last_name, p.program_name, ip.start_date, ip.end_date, ip.progress, bl.behavior_rating, bl.notes
        FROM inmate_programs ip
        INNER JOIN inmates i ON ip.inmate_id = i.inmate_id
        INNER JOIN programs p ON ip.program_id = p.program_id
        LEFT JOIN behavior_logs bl ON bl.inmate_id = i.inmate_id AND bl.log_date >= ip.start_date
        ORDER BY i.last_name, i.first_name, ip.start_date
    ");

    while ($row = $report_data->fetch_assoc()) {
        fputcsv($output, [
            $row['first_name'] . ' ' . $row['last_name'],
            $row['program_name'],
            $row['start_date'],
            $row['end_date'] ?: 'N/A',
            $row['progress'],
            $row['behavior_rating'] ?: 'N/A',
            $row['notes'] ?: 'N/A'
        ]);
    }

    fclose($output);
    exit();
}

include '../../partials/header.php';
include '../../partials/sidebar.php';

// Fetch summary data for display
$summary = $conn->query("
    SELECT 
        COUNT(DISTINCT ip.inmate_id) as total_inmates_in_programs,
        COUNT(CASE WHEN ip.progress = 'Completed' THEN 1 END) as completed_programs,
        COUNT(CASE WHEN ip.progress = 'Ongoing' THEN 1 END) as ongoing_programs,
        COUNT(CASE WHEN ip.progress = 'Dropped' THEN 1 END) as dropped_programs,
        AVG(CASE WHEN bl.behavior_rating = 'Excellent' THEN 4 WHEN bl.behavior_rating = 'Good' THEN 3 WHEN bl.behavior_rating = 'Fair' THEN 2 WHEN bl.behavior_rating = 'Poor' THEN 1 END) as avg_behavior_rating
    FROM inmate_programs ip
    LEFT JOIN behavior_logs bl ON ip.inmate_id = bl.inmate_id
");
$summary_data = $summary->fetch_assoc();
?>
<main class="flex-1 p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Reports & Analytics</h1>
                    <p class="text-sm text-slate-500 mt-1">Generate comprehensive reports on rehabilitation program progress</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Total Inmates in Programs</h3>
                        <p class="text-3xl font-bold text-indigo-600"><?php echo $summary_data['total_inmates_in_programs'] ?: 0; ?></p>
                    </div>
                    <div class="p-3 bg-indigo-50 rounded-lg">
                        <svg class="w-8 h-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Completed Programs</h3>
                        <p class="text-3xl font-bold text-green-600"><?php echo $summary_data['completed_programs'] ?: 0; ?></p>
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
                        <p class="text-3xl font-bold text-yellow-600"><?php echo $summary_data['ongoing_programs'] ?: 0; ?></p>
                    </div>
                    <div class="p-3 bg-yellow-50 rounded-lg">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-slate-800 mb-2">Average Behavior Rating</h3>
                        <p class="text-3xl font-bold text-blue-600"><?php echo number_format($summary_data['avg_behavior_rating'] ?: 0, 1); ?>/4</p>
                    </div>
                    <div class="p-3 bg-blue-50 rounded-lg">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Generation -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xl font-semibold text-slate-800">Generate Inmate Progress Report</h2>
                    <p class="text-sm text-slate-500 mt-1">Download a comprehensive CSV report of inmate program progress and behavior evaluations</p>
                </div>
                <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 mb-6">
                <div class="flex items-start space-x-3">
                    <svg class="w-5 h-5 text-slate-500 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <div>
                        <h4 class="text-sm font-medium text-slate-800 mb-1">Report Contents</h4>
                        <p class="text-sm text-slate-600">The report includes inmate names, program details, progress status, behavior ratings, and evaluation notes in CSV format for easy analysis.</p>
                    </div>
                </div>
            </div>
            <form method="post" class="flex justify-start">
                <button type="submit" name="generate_report" class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Download CSV Report
                </button>
            </form>
        </div>
    </div>
</main>

<?php include '../../partials/footer.php'; ?>
