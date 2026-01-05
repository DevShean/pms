<?php
require '../../includes/session_check.php';
include '../../config/config.php'; // ensure DB connection

// Fetch dashboard statistics
$total_inmates = $conn->query("SELECT COUNT(*) AS total FROM inmates")->fetch_assoc()['total'];
$active_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$reports_generated = $conn->query("SELECT COUNT(*) AS total FROM incidents")->fetch_assoc()['total'];
$pending_visitations = $conn->query("SELECT COUNT(*) AS total FROM visitations WHERE status = 'Pending'")->fetch_assoc()['total'];

/* Page title for <head> (header.php reads this) */
$page_title = 'Dashboard';

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
          <h1 class="text-3xl font-bold text-slate-900">Dashboard</h1>
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

    <!-- Quick Stats (Optional - Add if you have data) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Total Inmates</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$total_inmates ?></p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Active Users</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$active_users ?></p> <!-- Replace with dynamic data -->
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Pending Visitations</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$pending_visitations ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <a href="inmates.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-blue-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-blue-600">Manage Inmates</h2>
        </div>
        <p class="text-slate-600 mb-4">View and manage inmate profiles, transfers, and releases.</p>
        <span class="inline-flex items-center text-sm font-medium text-blue-700 group-hover:text-blue-800 group-hover:underline">
          Go to Inmates
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>

      <a href="manage_users.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-green-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-green-600">Manage Users</h2>
        </div>
        <p class="text-slate-600 mb-4">Add, edit, or remove system users and roles.</p>
        <span class="inline-flex items-center text-sm font-medium text-green-700 group-hover:text-green-800 group-hover:underline">
          Go to Users
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>

      <a href="medical.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-red-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-red-600">Medical Records</h2>
        </div>
        <p class="text-slate-600 mb-4">View all inmate medical data.</p>
        <span class="inline-flex items-center text-sm font-medium text-red-700 group-hover:text-red-800 group-hover:underline">
          Go to Medical Records
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>

      <a href="visitation.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-yellow-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-yellow-600">Visitations</h2>
        </div>
        <p class="text-slate-600 mb-4">Approve, deny, and monitor visit requests.</p>
        <span class="inline-flex items-center text-sm font-medium text-yellow-700 group-hover:text-yellow-800 group-hover:underline">
          Go to Visitations
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>
    </div>
  </div>
</main>

<?php include '../../partials/footer.php'; ?>
