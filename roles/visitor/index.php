<?php
require '../../includes/session_check.php';
include '../../config/config.php'; // ensure DB connection

// Fetch visitor-specific dashboard statistics
$user_id = $_SESSION['user_id'];
$visitor = $conn->query("SELECT visitor_id FROM visitors WHERE user_id = $user_id")->fetch_assoc();
$visitor_id = $visitor['visitor_id'];
$upcoming_visits = $conn->query("SELECT COUNT(*) AS total FROM visitations WHERE visitor_id = $visitor_id AND scheduled_date >= CURDATE() AND status = 'Approved'")->fetch_assoc()['total'];
$pending_requests = $conn->query("SELECT COUNT(*) AS total FROM visitations WHERE visitor_id = $visitor_id AND status = 'Pending'")->fetch_assoc()['total'];
$completed_visits = $conn->query("SELECT COUNT(*) AS total FROM visitations WHERE visitor_id = $visitor_id AND scheduled_date < CURDATE() AND status = 'Approved'")->fetch_assoc()['total'];
$rejected_requests = $conn->query("SELECT COUNT(*) AS total FROM visitations WHERE visitor_id = $visitor_id AND status = 'Rejected'")->fetch_assoc()['total'];

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
          <h1 class="text-3xl font-bold text-slate-900">Visitor Dashboard</h1>
          <p class="text-sm text-slate-500 mt-1">
            Welcome back, <span class="font-medium text-slate-700"><?= $full_name; ?></span>
            <span class="hidden sm:inline">— <?= role_label($role_id); ?></span>
          </p>
        </div>
        <div class="hidden sm:block">
          <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
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
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Upcoming Visits</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$upcoming_visits ?></p>
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
            <p class="text-sm font-medium text-slate-500">Pending Requests</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$pending_requests ?></p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Completed Visits</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$completed_visits ?></p>
          </div>
        </div>
      </div>
      <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm font-medium text-slate-500">Rejected Requests</p>
            <p class="text-2xl font-bold text-slate-900"><?= (int)$rejected_requests ?></p>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Actions Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <a href="schedule.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-blue-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-blue-600">Schedule Visit</h2>
        </div>
        <p class="text-slate-600 mb-4">Request a new visitation with an inmate.</p>
        <span class="inline-flex items-center text-sm font-medium text-blue-700 group-hover:text-blue-800 group-hover:underline">
          Schedule Visit
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>

      <a href="history.php" class="group block rounded-xl border border-slate-200 bg-white p-6 shadow-sm transition-all duration-200 hover:-translate-y-1 hover:shadow-lg hover:border-green-300">
        <div class="flex items-center mb-4">
          <div class="flex-shrink-0">
            <svg class="w-10 h-10 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
          </div>
          <h2 class="ml-4 text-xl font-semibold text-slate-800 group-hover:text-green-600">Visit History</h2>
        </div>
        <p class="text-slate-600 mb-4">View your past and upcoming visits.</p>
        <span class="inline-flex items-center text-sm font-medium text-green-700 group-hover:text-green-800 group-hover:underline">
          View History
          <svg class="ml-1 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
          </svg>
        </span>
      </a>
    </div>

    <!-- Recent Activity Section -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
      <div class="flex items-center justify-between mb-6">
        <div>
          <h2 class="text-xl font-semibold text-slate-800">Recent Activity</h2>
          <p class="text-sm text-slate-500 mt-1">Your latest visit requests and updates</p>
        </div>
        <svg class="w-6 h-6 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
      </div>
      <div class="space-y-4">
        <?php
        // Fetch recent visitations for this visitor
        $recent_visits = $conn->query("
            SELECT v.*, i.first_name, i.last_name
            FROM visitations v
            JOIN inmates i ON v.inmate_id = i.inmate_id
            WHERE v.visitor_id = $visitor_id
            ORDER BY v.scheduled_date DESC
            LIMIT 5
        ");

        if ($recent_visits->num_rows > 0) {
            while ($visit = $recent_visits->fetch_assoc()) {
                $status_color = match($visit['status']) {
                    'Approved' => 'text-green-600 bg-green-50',
                    'Pending' => 'text-yellow-600 bg-yellow-50',
                    'Rejected' => 'text-red-600 bg-red-50',
                    default => 'text-slate-600 bg-slate-50'
                };
                ?>
                <div class="flex items-center justify-between p-4 bg-slate-50 rounded-lg">
                  <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                      <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-slate-900">
                        Visit with <?php echo htmlspecialchars($visit['first_name'] . ' ' . $visit['last_name']); ?>
                      </p>
                      <p class="text-sm text-slate-500">
                        <?php echo date('M j, Y', strtotime($visit['scheduled_date'])); ?>
                      </p>
                    </div>
                  </div>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $status_color; ?>">
                    <?php echo htmlspecialchars($visit['status']); ?>
                  </span>
                </div>
                <?php
            }
        } else {
            ?>
            <div class="text-center py-8">
              <svg class="w-12 h-12 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
              </svg>
              <p class="text-slate-500">No recent activity found.</p>
              <p class="text-sm text-slate-400 mt-1">Schedule your first visit to get started.</p>
            </div>
            <?php
        }
        ?>
      </div>
    </div>
  </div>
</main>

<?php include '../../partials/footer.php'; ?>
