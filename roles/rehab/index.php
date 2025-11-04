<?php
require '../../includes/session_check.php';
include '../../config/config.php'; // ensure DB connection

// Fetch dashboard statistics for rehab staff
$total_programs = $conn->query("SELECT COUNT(*) AS total FROM programs WHERE assigned_staff_id = {$_SESSION['user_id']} OR assigned_staff_id IS NULL")->fetch_assoc()['total'];
$total_inmates_in_programs = $conn->query("SELECT COUNT(DISTINCT inmate_id) AS total FROM inmate_programs WHERE staff_id = {$_SESSION['user_id']}")->fetch_assoc()['total'];
$ongoing_programs = $conn->query("SELECT COUNT(*) AS total FROM inmate_programs WHERE staff_id = {$_SESSION['user_id']} AND progress = 'Ongoing'")->fetch_assoc()['total'];
$completed_programs = $conn->query("SELECT COUNT(*) AS total FROM inmate_programs WHERE staff_id = {$_SESSION['user_id']} AND progress = 'Completed'")->fetch_assoc()['total'];
$behavior_logs_today = $conn->query("SELECT COUNT(*) AS total FROM behavior_logs WHERE staff_id = {$_SESSION['user_id']} AND log_date = CURDATE()")->fetch_assoc()['total'];

/* Page title for <head> (header.php reads this) */
$page_title = 'Rehabilitation Dashboard';

include '../../partials/header.php';
include '../../partials/sidebar.php';

$full_name = htmlspecialchars($_SESSION['full_name'] ?? 'Guest', ENT_QUOTES, 'UTF-8');
$role_id   = $_SESSION['role_id'] ?? null; // role_label() is defined in header.php
?>

<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
  <div class="max-w-7xl mx-auto space-y-8">
    <!-- Page header -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Rehabilitation Dashboard</h1>
          <p class="text-sm text-slate-500 mt-1">
            Welcome back, <span class="font-medium text-slate-700"><?= $full_name; ?></span>
            <span class="hidden sm:inline">— <?= role_label($role_id); ?></span>
          </p>
        </div>
        <div class="hidden sm:block">
          <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Total Programs</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$total_programs ?></p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Inmates in Programs</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$total_inmates_in_programs ?></p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Ongoing Programs</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$ongoing_programs ?></p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Completed Programs</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$completed_programs ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <a href="programs.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-blue-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-blue-600">Manage Programs</h2>
        </div>
        <p class="text-slate-600 mb-4">Create, edit, and assign rehabilitation programs.</p>
        <span class="inline-flex items-center text-sm font-medium text-blue-700 group-hover:text-blue-800 group-hover:underline">
          Go to Programs
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>

      <a href="progress.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-green-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-green-600">Inmate Progress</h2>
        </div>
        <p class="text-slate-600 mb-4">Track and update inmate program progress.</p>
        <span class="inline-flex items-center text-sm font-medium text-green-700 group-hover:text-green-800 group-hover:underline">
          Go to Progress
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>

      <a href="behavior.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-purple-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-purple-600">Behavior Logs</h2>
        </div>
        <p class="text-slate-600 mb-4">Record and monitor inmate behavior.</p>
        <span class="inline-flex items-center text-sm font-medium text-purple-700 group-hover:text-purple-800 group-hover:underline">
          Go to Behavior Logs
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>

      <a href="report.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-red-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-red-600">Reports</h2>
        </div>
        <p class="text-slate-600 mb-4">Generate rehabilitation reports and analytics.</p>
        <span class="inline-flex items-center text-sm font-medium text-red-700 group-hover:text-red-800 group-hover:underline">
          Go to Reports
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>
    </div>
  </div>
</main>

<?php include '../../partials/footer.php'; ?>
