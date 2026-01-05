<?php
require 'backend/logs_backend.php';

/* Page title for head */
$page_title = 'System Logs';

include '../../partials/header.php';
include '../../partials/sidebar.php';
?>
<main id="content" class="p-6 bg-slate-50 min-h-[calc(100vh-var(--header-h))]">
  <div class="max-w-7xl mx-auto space-y-6">
    <input type="hidden" id="pageLoadTime" value="<?= date('Y-m-d H:i:s') ?>">
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">System Logs</h1>
        <p class="text-sm text-slate-500">View all system activities and notifications.</p>
      </div>
    </div>

    <!-- Table -->
    <div class="rounded-xl border border-slate-200 bg-white shadow-sm">
      <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
          <thead class="bg-slate-50 text-slate-600">
            <tr>
              <th class="px-6 py-3 text-left font-semibold">Action</th>
              <th class="px-6 py-3 text-left font-semibold">Details</th>
              <th class="px-6 py-3 text-left font-semibold">User</th>
              <th class="px-6 py-3 text-left font-semibold">Date</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-200">
            <?php if (isset($logs_result) && $logs_result->num_rows > 0): ?>
              <?php while ($log = $logs_result->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50">
                  <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($log['action']) ?></td>
                  <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($log['details']) ?></td>
                  <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($log['full_name'] ?? 'System') ?></td>
                  <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($log['created_at']) ?></td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="4" class="px-6 py-6 text-center text-slate-500">No logs found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
        <div id="pagination" class="flex justify-center mt-4 space-x-2"></div>
      </div>
    </div>
  </div>
</main>

<!-- Scripts -->
<script>
  // Live update logs every 10 seconds
  setInterval(() => {
    const pageLoadTime = document.getElementById('pageLoadTime').value;
    fetch(`backend/logs_backend.php?live=1&since=${encodeURIComponent(pageLoadTime)}`)
      .then(response => response.json())
      .then(data => {
        if (data.new_logs) {
          // Reload the page or update the table
          location.reload();
        }
      });
  }, 10000);
</script>

<?php include '../../partials/footer.php'; ?>
