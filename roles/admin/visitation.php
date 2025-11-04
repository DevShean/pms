<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

/* ====== PAGINATION LOGIC ====== */
$limit = 10; // Number of records per page
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int) $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Count total records
$total_sql = "SELECT COUNT(*) AS total FROM visitations";
$total_result = $conn->query($total_sql);
$total_rows = $total_result->fetch_assoc()['total'];
$total_pages = ceil($total_rows / $limit);

/* ====== FETCH VISITATION RECORDS (Paginated) ====== */
$sql = "SELECT v.*, 
               i.first_name AS inmate_first_name, 
               i.last_name AS inmate_last_name, 
               u.full_name AS visitor_full_name
        FROM visitations v
        JOIN inmates i ON v.inmate_id = i.inmate_id
        JOIN visitors vis ON v.visitor_id = vis.visitor_id
        JOIN users u ON vis.user_id = u.user_id
        ORDER BY v.scheduled_date DESC
        LIMIT $limit OFFSET $offset";

$result = $conn->query($sql);
?>

<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
  <div class="max-w-7xl mx-auto">
    <h2 class="text-3xl font-bold text-slate-800 mb-6">Visitation Requests</h2>

    <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Inmate</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Visitor</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Visit Date</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Status</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50 transition">
                  <td class="px-6 py-4 whitespace-nowrap text-slate-800 font-medium">
                    <?= htmlspecialchars($row['inmate_first_name'] . ' ' . $row['inmate_last_name']) ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                    <?= htmlspecialchars($row['visitor_full_name']) ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-slate-700">
                    <?= htmlspecialchars(date('M d, Y', strtotime($row['scheduled_date']))) ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <?php
                      $status = htmlspecialchars($row['status']);
                      $badgeClass = match ($status) {
                        'Approved' => 'bg-green-100 text-green-700',
                        'Denied' => 'bg-red-100 text-red-700',
                        default => 'bg-yellow-100 text-yellow-700',
                      };
                    ?>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full <?= $badgeClass ?>">
                      <?= $status ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm">
                    <a href="visitation_status.php?id=<?= $row['visit_id'] ?>&status=Approved" 
                       class="text-green-600 hover:text-green-800 font-medium">Approve</a>
                    <a href="visitation_status.php?id=<?= $row['visit_id'] ?>&status=Denied" 
                       class="text-red-600 hover:text-red-800 font-medium ml-4">Deny</a>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr>
                <td colspan="5" class="px-6 py-12 text-center text-slate-500">No visitation requests found.</td>
              </tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <?php if ($total_pages > 1): ?>
        <div class="flex justify-between items-center px-6 py-4 bg-slate-50 border-t border-slate-200">
          <p class="text-sm text-slate-500">Page <?= $page ?> of <?= $total_pages ?></p>
          <div class="flex items-center space-x-2">
            <?php if ($page > 1): ?>
              <a href="?page=<?= $page - 1 ?>" class="px-3 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100">
                ← Previous
              </a>
            <?php endif; ?>

            <?php if ($page < $total_pages): ?>
              <a href="?page=<?= $page + 1 ?>" class="px-3 py-1 bg-white border border-slate-300 rounded hover:bg-slate-100">
                Next →
              </a>
            <?php endif; ?>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</main>

<?php
$conn->close();
include '../../partials/footer.php';
?>
