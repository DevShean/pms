<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Pagination settings
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$page = max(1, $page);
$offset = ($page - 1) * $limit;

// Fetch inmates with pagination
$result = $conn->query("SELECT * FROM inmates ORDER BY inmate_id LIMIT $limit OFFSET $offset");

// Fetch total count for pagination
$total_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates")->fetch_assoc()['count'];
$total_pages = ceil($total_inmates / $limit);

// Fetch statistics
$active_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates WHERE status = 'Active'")->fetch_assoc()['count'];
$released_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates WHERE status = 'Released'")->fetch_assoc()['count'];
$transferred_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates WHERE status = 'Transferred'")->fetch_assoc()['count'];

// Fetch cell block distribution
$cell_blocks = $conn->query("SELECT cell_block, COUNT(*) as count FROM inmates WHERE cell_block IS NOT NULL AND cell_block != '' GROUP BY cell_block ORDER BY count DESC");

/* Page title for <head> (header.php reads this) */
$page_title = 'Manage Inmates';

include '../../partials/header.php';
include '../../partials/sidebar.php';
?>
<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Manage Inmates</h1>
                    <p class="text-sm text-slate-500 mt-1">View and monitor inmate profiles and activities.</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Statistics Cards -->
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
                        <p class="text-2xl font-bold text-slate-900"><?php echo $total_inmates; ?></p>
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
                        <p class="text-sm font-medium text-slate-500">Active Inmates</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $active_inmates; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500">Released Inmates</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $released_inmates; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-slate-500">Transferred Inmates</p>
                        <p class="text-2xl font-bold text-slate-900"><?php echo $transferred_inmates; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-3 scale-95 origin-top-left">
            <!-- Status Distribution Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-2">
                <h3 class="text-[12px] font-semibold text-slate-700 mb-2">Inmate Status Distribution</h3>
                <div class="h-[350px]">
                <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Cell Block Distribution Chart -->
            <div class="bg-white rounded-lg shadow-sm border border-slate-200 p-2">
                <h3 class="text-[12px] font-semibold text-slate-700 mb-2">Cell Block Distribution</h3>
                <div class="h-[300px]">
                <canvas id="cellChart"></canvas>
                </div>
            </div>
        </div>


        <!-- Inmates Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200">
                <h2 class="text-xl font-semibold text-slate-800">Inmate Profiles</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Birthdate</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Gender</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Crime</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Cell Block</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Admission Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-slate-900"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo $row['birthdate']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo $row['gender']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo htmlspecialchars($row['crime']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo htmlspecialchars($row['cell_block'] ?? 'N/A'); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500"><?php echo $row['admission_date']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $row['status'] == 'Active' ? 'bg-green-100 text-green-800' : 
                                               ($row['status'] == 'Released' ? 'bg-orange-100 text-orange-800' : 'bg-purple-100 text-purple-800'); ?>">
                                    <?php echo $row['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <button class="viewBtn inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200"
                                        data-id="<?php echo $row['inmate_id']; ?>"
                                        data-photo="<?php echo htmlspecialchars($row['photo_path']); ?>"
                                        data-name="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                        data-birthdate="<?php echo $row['birthdate']; ?>"
                                        data-gender="<?php echo $row['gender']; ?>"
                                        data-crime="<?php echo htmlspecialchars($row['crime']); ?>"
                                        data-sentence="<?php echo $row['sentence_years']; ?>"
                                        data-court="<?php echo htmlspecialchars($row['court_details']); ?>"
                                        data-cell="<?php echo htmlspecialchars($row['cell_block']); ?>"
                                        data-admission="<?php echo $row['admission_date']; ?>"
                                        data-release="<?php echo $row['release_date']; ?>"
                                        data-status="<?php echo $row['status']; ?>">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View Details
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-slate-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                    <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors duration-200">Previous</a>
                    <?php endif; ?>
                    <?php if ($page < $total_pages): ?>
                    <a href="?page=<?php echo $page + 1; ?>" class="ml-3 relative inline-flex items-center px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-md hover:bg-slate-50 transition-colors duration-200">Next</a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-700">
                            Showing <span class="font-medium"><?php echo $offset + 1; ?></span> to <span class="font-medium"><?php echo min($offset + $limit, $total_inmates); ?></span> of <span class="font-medium"><?php echo $total_inmates; ?></span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors duration-200">
                                <span class="sr-only">Previous</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <?php endif; ?>

                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="relative inline-flex items-center px-4 py-2 border border-slate-300 bg-white text-sm font-medium <?php echo $i == $page ? 'text-blue-600 border-blue-500 bg-blue-50' : 'text-slate-700 hover:bg-slate-50'; ?> transition-colors duration-200">
                                <?php echo $i; ?>
                            </a>
                            <?php endfor; ?>

                            <?php if ($page < $total_pages): ?>
                            <a href="?page=<?php echo $page + 1; ?>" class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-slate-300 bg-white text-sm font-medium text-slate-500 hover:bg-slate-50 transition-colors duration-200">
                                <span class="sr-only">Next</span>
                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                            </a>
                            <?php endif; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-slate-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-6 border w-full max-w-md shadow-lg rounded-xl bg-white border-slate-200">
        <div class="text-center">
            <h3 class="text-xl font-semibold text-slate-900 mb-4">Inmate Details</h3>
            <div class="space-y-4">
                <div class="flex justify-center">
                    <img id="view-photo" src="" alt="Inmate Photo" class="w-32 h-32 object-cover rounded-full border-4 border-slate-200">
                </div>
                <div class="grid grid-cols-1 gap-3 text-left">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Name:</strong>
                        <span id="view-name" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Birthdate:</strong>
                        <span id="view-birthdate" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Gender:</strong>
                        <span id="view-gender" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Crime:</strong>
                        <span id="view-crime" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Sentence Years:</strong>
                        <span id="view-sentence" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Court Details:</strong>
                        <span id="view-court" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Cell Block:</strong>
                        <span id="view-cell" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Admission Date:</strong>
                        <span id="view-admission" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100">
                        <strong class="text-sm font-medium text-slate-600">Release Date:</strong>
                        <span id="view-release" class="text-sm text-slate-900"></span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <strong class="text-sm font-medium text-slate-600">Status:</strong>
                        <span id="view-status" class="text-sm text-slate-900"></span>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="button" id="closeViewModal" class="px-4 py-2 bg-slate-100 text-slate-900 text-sm font-medium rounded-md shadow-sm hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors duration-200">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // View modal functionality
    const viewBtns = document.querySelectorAll('.viewBtn');
    const viewModal = document.getElementById('viewModal');
    const closeViewModal = document.getElementById('closeViewModal');

    viewBtns.forEach(btn => {
        btn.onclick = function() {
            const photoSrc = this.getAttribute('data-photo');
            document.getElementById('view-photo').src = photoSrc ? photoSrc : 'https://via.placeholder.com/96x96?text=No+Photo';
            document.getElementById('view-name').textContent = this.getAttribute('data-name');
            document.getElementById('view-birthdate').textContent = this.getAttribute('data-birthdate');
            document.getElementById('view-gender').textContent = this.getAttribute('data-gender');
            document.getElementById('view-crime').textContent = this.getAttribute('data-crime');
            document.getElementById('view-sentence').textContent = this.getAttribute('data-sentence');
            document.getElementById('view-court').textContent = this.getAttribute('data-court');
            document.getElementById('view-cell').textContent = this.getAttribute('data-cell');
            document.getElementById('view-admission').textContent = this.getAttribute('data-admission');
            document.getElementById('view-release').textContent = this.getAttribute('data-release');
            document.getElementById('view-status').textContent = this.getAttribute('data-status');
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

    // Status distribution chart
    const statusCtx = document.getElementById('statusChart').getContext('2d');
    const statusChart = new Chart(statusCtx, {
        type: 'pie',
        data: {
            labels: ['Active', 'Released', 'Transferred'],
            datasets: [{
                label: 'Inmate Status',
                data: [<?php echo $active_inmates; ?>, <?php echo $released_inmates; ?>, <?php echo $transferred_inmates; ?>],
                backgroundColor: [
                    'rgba(34, 197, 94, 0.6)',
                    'rgba(251, 146, 60, 0.6)',
                    'rgba(147, 51, 234, 0.6)'
                ],
                borderColor: [
                    'rgba(34, 197, 94, 1)',
                    'rgba(251, 146, 60, 1)',
                    'rgba(147, 51, 234, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'top',
                }
            }
        }
    });

    // Cell block distribution chart
    const cellCtx = document.getElementById('cellChart').getContext('2d');
    const cellChart = new Chart(cellCtx, {
        type: 'bar',
        data: {
            labels: [<?php
                $labels = [];
                $cell_blocks->data_seek(0);
                while ($block = $cell_blocks->fetch_assoc()) {
                    $labels[] = "'" . addslashes($block['cell_block']) . "'";
                }
                echo implode(',', $labels);
            ?>],
            datasets: [{
                label: 'Inmates per Cell Block',
                data: [<?php
                    $data = [];
                    $cell_blocks->data_seek(0);
                    while ($block = $cell_blocks->fetch_assoc()) {
                        $data[] = $block['count'];
                    }
                    echo implode(',', $data);
                ?>],
                backgroundColor: 'rgba(59, 130, 246, 0.6)',
                borderColor: 'rgba(59, 130, 246, 1)',
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
