<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle POST requests before any HTML output
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['cancel_visit'])) {
        $visit_id = intval($_POST['cancel_visit']);
        $user_id = $_SESSION['user_id'];
        $visitor = $conn->query("SELECT visitor_id FROM visitors WHERE user_id = $user_id")->fetch_assoc();
        $visitor_id = $visitor['visitor_id'];

        // Only allow canceling if status is Pending
        $conn->query("UPDATE visitations SET status = 'Cancelled' WHERE visit_id = $visit_id AND visitor_id = $visitor_id AND status = 'Pending'");
        header("Location: schedule.php");
        exit();
    }

    if (isset($_POST['schedule_visit'])) {
        $inmate_id = intval($_POST['inmate_id']);
        $scheduled_date = $_POST['scheduled_date'];
        $notes = $conn->real_escape_string($_POST['notes']);
        $user_id = $_SESSION['user_id'];
        $visitor = $conn->query("SELECT visitor_id FROM visitors WHERE user_id = $user_id")->fetch_assoc();
        $visitor_id = $visitor['visitor_id'];

        // Validate inmate exists
        $inmate_check = $conn->query("SELECT inmate_id FROM inmates WHERE inmate_id = $inmate_id");
        if ($inmate_check->num_rows == 0) {
            $_SESSION['error'] = "Invalid inmate selected.";
            header("Location: schedule.php");
            exit();
        }

        $relationship = $conn->real_escape_string($_POST['relationship']);
        $visit_type = $conn->real_escape_string($_POST['visit_type']);
        $sql = "INSERT INTO visitations (inmate_id, visitor_id, visit_type, scheduled_date, status, relationship, notes) VALUES ($inmate_id, $visitor_id, '$visit_type', '$scheduled_date', 'Pending', '$relationship', '$notes')";
        if ($conn->query($sql)) {
            // Add a notification for the request
            $notification_sql = "INSERT INTO notifications (user_id, title, message, type, created_at) VALUES ($user_id, 'Visitation Request Sent', 'Your visitation request has been submitted and is pending approval.', 'visitation', NOW())";
            $conn->query($notification_sql);
            $_SESSION['success'] = "Visitation request submitted successfully.";
        } else {
            $_SESSION['error'] = "Failed to submit visitation request.";
        }
        header("Location: schedule.php");
        exit();
    }
}

include '../../partials/header.php';
include '../../partials/sidebar.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Get visitor's related inmates
$user_id = $_SESSION['user_id'];
$visitor = $conn->query("SELECT visitor_id FROM visitors WHERE user_id = $user_id")->fetch_assoc();
$visitor_id = $visitor['visitor_id'];
$related_inmates = $conn->query("
    SELECT i.*, v.relationship
    FROM inmates i
    INNER JOIN visitors v ON v.user_id = $user_id
    WHERE i.inmate_id IN (
        SELECT inmate_id FROM visitations WHERE visitor_id = $visitor_id
    )
    GROUP BY i.inmate_id
");

// Fetch all inmates for dropdown
$all_inmates = $conn->query("SELECT inmate_id, first_name, last_name FROM inmates WHERE status='Active' ORDER BY first_name, last_name");

// Fetch visitor's visitation history
$visitations = $conn->query("
    SELECT v.*, i.first_name, i.last_name
    FROM visitations v
    INNER JOIN inmates i ON v.inmate_id = i.inmate_id
    WHERE v.visitor_id = $visitor_id
    ORDER BY v.scheduled_date DESC
");
?>

<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
  <div class="max-w-7xl mx-auto space-y-8">
    <!-- Page header -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-3xl font-bold text-slate-900">Schedule Visitation</h1>
          <p class="text-sm text-slate-500 mt-1">
            Request and manage your visitation appointments
          </p>
        </div>
        <div class="hidden sm:block">
          <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
          </svg>
        </div>
      </div>
    </div>

    <!-- Success/Error Messages -->
    <?php if (isset($_SESSION['success'])): ?>
      <div class="bg-green-50 border border-green-200 rounded-xl p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-green-800 font-medium"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></p>
        </div>
      </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
      <div class="bg-red-50 border border-red-200 rounded-xl p-4">
        <div class="flex items-center">
          <svg class="w-5 h-5 text-red-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
          </svg>
          <p class="text-red-800 font-medium"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></p>
        </div>
      </div>
    <?php endif; ?>

    <div class="flex flex-col lg:flex-row gap-8">
      <!-- Schedule New Visit Card -->
      <div class="flex-1">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="bg-gradient-to-r from-blue-600 to-blue-700 px-6 py-4">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
              </svg>
              <h2 class="text-xl font-semibold text-white">Request New Visitation</h2>
            </div>
          </div>
          <form method="post" class="p-6 space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Inmate Selection -->
              <div class="md:col-span-2">
                <label for="inmate_id" class="block text-sm font-semibold text-slate-700 mb-2">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                  </svg>
                  Select Inmate
                </label>
                <div class="relative">
                  <input type="text" id="inmate-search" placeholder="Search and select an inmate..." class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                  <input type="hidden" name="inmate_id" id="inmate_id" required>
                  <div id="inmate-dropdown" class="absolute z-10 w-full bg-white border border-slate-300 rounded-lg shadow-lg mt-1 max-h-60 overflow-y-auto hidden">
                    <div id="inmate-options" class="py-2">
                      <?php $all_inmates->data_seek(0); while ($inmate = $all_inmates->fetch_assoc()): ?>
                      <div class="px-4 py-3 hover:bg-slate-50 cursor-pointer inmate-option transition-colors" data-value="<?php echo $inmate['inmate_id']; ?>" data-text="<?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?>">
                        <div class="flex items-center">
                          <svg class="w-4 h-4 text-slate-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                          </svg>
                          <?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?>
                        </div>
                      </div>
                      <?php endwhile; ?>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Scheduled Date & Time -->
              <div>
                <label for="scheduled_date" class="block text-sm font-semibold text-slate-700 mb-2">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                  </svg>
                  Scheduled Date & Time
                </label>
                <input type="datetime-local" name="scheduled_date" id="scheduled_date" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
              </div>

              <!-- Visit Type -->
              <div>
                <label for="visit_type" class="block text-sm font-semibold text-slate-700 mb-2">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                  </svg>
                  Visit Type
                </label>
                <select name="visit_type" id="visit_type" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                  <option value="">Select Visit Type</option>
                  <option value="Conjugal Visit">Conjugal Visit</option>
                  <option value="Paduhol Visit">Paduhol Visit</option>
                  <option value="Visit to the Inmate">Visit to the Inmate</option>
                </select>
              </div>

              <!-- Relationship -->
              <div>
                <label for="relationship" class="block text-sm font-semibold text-slate-700 mb-2">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                  </svg>
                  Relationship
                </label>
                <select name="relationship" id="relationship" required class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                  <option value="">Select Relationship</option>
                  <option value="Family">Family</option>
                  <option value="Friend">Friend</option>
                  <option value="Legal Representative">Legal Representative</option>
                  <option value="Other">Other</option>
                </select>
              </div>

              <!-- Notes -->
              <div class="md:col-span-2">
                <label for="notes" class="block text-sm font-semibold text-slate-700 mb-2">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                  </svg>
                  Notes (Optional)
                </label>
                <textarea name="notes" id="notes" rows="4" class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none" placeholder="Add any additional notes or special requests..."></textarea>
              </div>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-end pt-4 border-t border-slate-200">
              <button type="submit" name="schedule_visit" class="bg-gradient-to-r from-blue-600 to-blue-700 text-white px-8 py-3 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all duration-200 font-semibold shadow-sm hover:shadow-md transform hover:-translate-y-0.5">
                <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                </svg>
                Submit Visitation Request
              </button>
            </div>
          </form>
        </div>
      </div>

      <!-- Visitation History -->
      <div class="flex-1">
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
          <div class="bg-gradient-to-r from-slate-600 to-slate-700 px-6 py-4">
            <div class="flex items-center">
              <svg class="w-6 h-6 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
              </svg>
              <h2 class="text-xl font-semibold text-white">Your Visitation Requests</h2>
            </div>
          </div>

          <div class="overflow-x-auto max-h-96 overflow-y-auto">
            <table class="min-w-full divide-y divide-slate-200">
              <thead class="bg-slate-50 sticky top-0">
                <tr>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Inmate</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Scheduled Date</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Type</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Notes</th>
                  <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-slate-200">
                <?php while ($visit = $visitations->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50 transition-colors">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-semibold text-slate-900">
                          <?php echo htmlspecialchars($visit['first_name'] . ' ' . $visit['last_name']); ?>
                        </div>
                        <div class="text-sm text-slate-500">
                          <?php echo htmlspecialchars($visit['relationship']); ?>
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="text-sm text-slate-900">
                      <?php echo date('M j, Y', strtotime($visit['scheduled_date'])); ?>
                    </div>
                    <div class="text-sm text-slate-500">
                      <?php echo date('g:i A', strtotime($visit['scheduled_date'])); ?>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-900">
                    <?php echo htmlspecialchars($visit['visit_type']); ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                      <?php
                      switch ($visit['status']) {
                        case 'Pending': echo 'bg-yellow-100 text-yellow-800'; break;
                        case 'Approved': echo 'bg-green-100 text-green-800'; break;
                        case 'Denied': echo 'bg-red-100 text-red-800'; break;
                        case 'Completed': echo 'bg-blue-100 text-blue-800'; break;
                        case 'Cancelled': echo 'bg-slate-100 text-slate-800'; break;
                        default: echo 'bg-slate-100 text-slate-800';
                      }
                      ?>">
                      <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <?php
                        switch ($visit['status']) {
                          case 'Pending': echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>'; break;
                          case 'Approved': echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'; break;
                          case 'Denied': echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'; break;
                          case 'Completed': echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>'; break;
                          case 'Cancelled': echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'; break;
                          default: echo '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>';
                        }
                        ?>
                      </svg>
                      <?php echo $visit['status']; ?>
                    </span>
                  </td>
                  <td class="px-6 py-4 text-sm text-slate-900 max-w-xs truncate">
                    <?php echo htmlspecialchars($visit['notes'] ?? 'No notes'); ?>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <?php if ($visit['status'] == 'Pending'): ?>
                      <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to cancel this visitation request?');">
                        <input type="hidden" name="cancel_visit" value="<?php echo $visit['visit_id']; ?>">
                        <button type="submit" class="text-red-600 hover:text-red-800 transition-colors font-semibold">
                          <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                          </svg>
                          Cancel
                        </button>
                      </form>
                    <?php else: ?>
                      <span class="text-slate-400">—</span>
                    <?php endif; ?>
                  </td>
                </tr>
                <?php endwhile; ?>
              </tbody>
            </table>
          </div>

          <?php if ($visitations->num_rows == 0): ?>
          <div class="text-center py-12">
            <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
            </svg>
            <h3 class="text-lg font-medium text-slate-900 mb-2">No visitation requests yet</h3>
            <p class="text-slate-500">Your visitation request history will appear here once you submit your first request.</p>
          </div>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
  const searchInput = document.getElementById('inmate-search');
  const inmateIdInput = document.getElementById('inmate_id');
  const dropdown = document.getElementById('inmate-dropdown');
  const options = document.querySelectorAll('.inmate-option');

  searchInput.addEventListener('focus', function() {
    dropdown.classList.remove('hidden');
    filterOptions('');
  });

  searchInput.addEventListener('input', function() {
    const query = this.value.toLowerCase().trim();
    filterOptions(query);
    dropdown.classList.remove('hidden');
  });

  function filterOptions(query) {
    options.forEach(option => {
      const text = option.textContent.toLowerCase();
      if (text.includes(query)) {
        option.style.display = 'block';
      } else {
        option.style.display = 'none';
      }
    });
  }

  options.forEach(option => {
    option.addEventListener('click', function() {
      searchInput.value = this.getAttribute('data-text');
      inmateIdInput.value = this.getAttribute('data-value');
      dropdown.classList.add('hidden');
    });
  });

  // Hide dropdown when clicking outside
  document.addEventListener('click', function(e) {
    if (!searchInput.contains(e.target) && !dropdown.contains(e.target)) {
      dropdown.classList.add('hidden');
    }
  });
</script>

<?php include '../../partials/footer.php'; ?>
