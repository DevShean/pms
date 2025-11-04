<?php
require '../../includes/session_check.php';
include '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Handle add behavior log
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_behavior'])) {
    $inmate_id = intval($_POST['inmate_id']);
    $log_date = $_POST['log_date'];
    $notes = $conn->real_escape_string($_POST['notes']);
    $behavior_rating = $_POST['behavior_rating'];
    $staff_id = $_SESSION['user_id']; // Assuming session has user_id

    $sql = "INSERT INTO behavior_logs (inmate_id, staff_id, log_date, notes, behavior_rating) VALUES ($inmate_id, $staff_id, '$log_date', '$notes', '$behavior_rating')";
    $conn->query($sql);
    header("Location: behavior.php");
    exit();
}

// Fetch behavior logs with inmate and staff names
$result = $conn->query("
    SELECT bl.*, i.first_name, i.last_name, i.photo_path, u.full_name as staff_name
    FROM behavior_logs bl
    INNER JOIN inmates i ON bl.inmate_id = i.inmate_id
    LEFT JOIN users u ON bl.staff_id = u.user_id
    ORDER BY bl.log_date DESC
");

// Fetch inmates for dropdown
$inmates_result = $conn->query("SELECT inmate_id, first_name, last_name FROM inmates");

// Fetch statistics
$total_logs = $conn->query("SELECT COUNT(*) as count FROM behavior_logs")->fetch_assoc()['count'];
$excellent_count = $conn->query("SELECT COUNT(*) as count FROM behavior_logs WHERE behavior_rating = 'Excellent'")->fetch_assoc()['count'];
$good_count = $conn->query("SELECT COUNT(*) as count FROM behavior_logs WHERE behavior_rating = 'Good'")->fetch_assoc()['count'];
$fair_count = $conn->query("SELECT COUNT(*) as count FROM behavior_logs WHERE behavior_rating = 'Fair'")->fetch_assoc()['count'];
$poor_count = $conn->query("SELECT COUNT(*) as count FROM behavior_logs WHERE behavior_rating = 'Poor'")->fetch_assoc()['count'];
?>
<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Behavior Logs Management</h1>
                    <p class="text-sm text-slate-500 mt-1">Monitor and track inmate behavior logs and ratings.</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500">Total Logs</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $total_logs; ?></p>
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
                        <p class="text-sm font-medium text-slate-500">Excellent</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $excellent_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500">Good</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $good_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500">Fair</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $fair_count; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500">Poor</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $poor_count; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-3 scale-95 origin-top-left">
            <!-- Behavior Rating Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-2">
                <h3 class="text-[12px] font-semibold text-slate-700 mb-2">Behavior Rating Distribution</h3>
                <div class="h-[350px]">
                <canvas id="behaviorChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Behavior Logs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-slate-800">Behavior Logs</h2>
                <button id="addBtn" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">Add New Log</button>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Log ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Inmate Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Staff Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Log Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Rating</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Notes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"><?php echo $row['log_id']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo htmlspecialchars($row['staff_name'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo $row['log_date']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $row['behavior_rating'] == 'Excellent' ? 'bg-green-100 text-green-800' : 
                                               ($row['behavior_rating'] == 'Good' ? 'bg-blue-100 text-blue-800' : 
                                               ($row['behavior_rating'] == 'Fair' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')); ?>">
                                    <?php echo $row['behavior_rating']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-500"><?php echo htmlspecialchars($row['notes'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="viewBtn text-blue-600 hover:text-blue-900"
                                        data-id="<?php echo $row['log_id']; ?>"
                                        data-photo="<?php echo htmlspecialchars($row['photo_path']); ?>"
                                        data-inmate="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                        data-staff="<?php echo htmlspecialchars($row['staff_name'] ?? 'N/A'); ?>"
                                        data-date="<?php echo $row['log_date']; ?>"
                                        data-rating="<?php echo $row['behavior_rating']; ?>"
                                        data-notes="<?php echo htmlspecialchars($row['notes'] ?? 'N/A'); ?>">View Details</button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-slate-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-xl rounded-xl bg-white">
        <div class="mt-3">
            <h3 class="text-xl font-semibold text-slate-900 text-center">Add New Behavior Log</h3>
            <form method="post" class="mt-6 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Select Inmate</label>
                    <select name="inmate_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="">Select Inmate</option>
                        <?php
                        $inmates_result->data_seek(0);
                        while ($inmate = $inmates_result->fetch_assoc()):
                        ?>
                        <option value="<?php echo $inmate['inmate_id']; ?>"><?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Log Date</label>
                    <input type="date" name="log_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Behavior Rating</label>
                    <select name="behavior_rating" required class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
                        <option value="Excellent">Excellent</option>
                        <option value="Good">Good</option>
                        <option value="Fair">Fair</option>
                        <option value="Poor">Poor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Notes</label>
                    <textarea name="notes" placeholder="Enter behavior notes..." rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                </div>
                <div class="flex items-center space-x-3 pt-4">
                    <button type="submit" name="add_behavior" class="flex-1 px-4 py-2 bg-indigo-600 text-white text-base font-medium rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors duration-200">Add Log</button>
                    <button type="button" id="closeModal" class="flex-1 px-4 py-2 bg-slate-300 text-slate-900 text-base font-medium rounded-lg shadow-sm hover:bg-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 transition-colors duration-200">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-slate-900 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-96 shadow-xl rounded-xl bg-white">
        <div class="mt-3">
            <h3 class="text-xl font-semibold text-slate-900 text-center">Behavior Log Details</h3>
            <div class="mt-6 space-y-4">
                <div class="flex justify-center">
                    <img id="view-photo" src="" alt="Inmate Photo" class="w-24 h-24 object-cover rounded-full border-4 border-slate-200">
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="font-medium text-slate-600">Log ID:</span>
                        <span id="view-id" class="text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="font-medium text-slate-600">Inmate Name:</span>
                        <span id="view-inmate" class="text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="font-medium text-slate-600">Staff Name:</span>
                        <span id="view-staff" class="text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="font-medium text-slate-600">Log Date:</span>
                        <span id="view-date" class="text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <span class="font-medium text-slate-600">Behavior Rating:</span>
                        <span id="view-rating" class="text-slate-900"></span>
                    </div>
                    <div class="py-2">
                        <span class="font-medium text-slate-600">Notes:</span>
                        <p id="view-notes" class="text-slate-900 mt-1"></p>
                    </div>
                </div>
            </div>
            <div class="flex justify-center pt-6">
                <button type="button" id="closeViewModal" class="px-6 py-2 bg-slate-300 text-slate-900 text-base font-medium rounded-lg shadow-sm hover:bg-slate-400 focus:outline-none focus:ring-2 focus:ring-slate-500 transition-colors duration-200">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const addBtn = document.getElementById('addBtn');
    const addModal = document.getElementById('addModal');
    const closeModal = document.getElementById('closeModal');

    addBtn.onclick = function() {
        addModal.classList.remove('hidden');
    }

    closeModal.onclick = function() {
        addModal.classList.add('hidden');
    }

    window.onclick = function(event) {
        if (event.target == addModal) {
            addModal.classList.add('hidden');
        }
    }

    // View modal functionality
    const viewBtns = document.querySelectorAll('.viewBtn');
    const viewModal = document.getElementById('viewModal');
    const closeViewModal = document.getElementById('closeViewModal');

    viewBtns.forEach(btn => {
        btn.onclick = function() {
            const photoSrc = this.getAttribute('data-photo');
            document.getElementById('view-photo').src = photoSrc ? photoSrc : 'https://via.placeholder.com/96x96?text=No+Photo';
            document.getElementById('view-id').textContent = this.getAttribute('data-id');
            document.getElementById('view-inmate').textContent = this.getAttribute('data-inmate');
            document.getElementById('view-staff').textContent = this.getAttribute('data-staff');
            document.getElementById('view-date').textContent = this.getAttribute('data-date');
            document.getElementById('view-rating').textContent = this.getAttribute('data-rating');
            document.getElementById('view-notes').textContent = this.getAttribute('data-notes');
            viewModal.classList.remove('hidden');
        }
    });

    closeViewModal.onclick = function() {
        viewModal.classList.add('hidden');
    }

    window.onclick = function(event) {
        if (event.target == viewModal) {
            viewModal.classList.add('hidden');
        }
    }

    // Behavior rating chart
    const behaviorCtx = document.getElementById('behaviorChart').getContext('2d');
    const behaviorChart = new Chart(behaviorCtx, {
        type: 'bar',
        data: {
            labels: ['Excellent', 'Good', 'Fair', 'Poor'],
            datasets: [{
                label: 'Behavior Logs',
                data: [<?php echo $excellent_count; ?>, <?php echo $good_count; ?>, <?php echo $fair_count; ?>, <?php echo $poor_count; ?>],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.6)',
                    'rgba(59, 130, 246, 0.6)',
                    'rgba(251, 146, 60, 0.6)',
                    'rgba(239, 68, 68, 0.6)'
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(59, 130, 246, 1)',
                    'rgba(251, 146, 60, 1)',
                    'rgba(239, 68, 68, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>

<?php include '../../partials/footer.php'; ?>
