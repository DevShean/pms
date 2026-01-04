<?php
require '../../includes/session_check.php';
require '../../config/config.php';

// Handle transfer update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_transfer'])) {
    $inmate_id = intval($_POST['inmate_id']);
    $new_block = $conn->real_escape_string($_POST['new_block']);
    $transfer_date = $_POST['transfer_date'];
    $staff_id = $_SESSION['user_id'];

    // Get current cell block
    $current = $conn->query("SELECT cell_block FROM inmates WHERE inmate_id = $inmate_id")->fetch_assoc();
    
    // Update inmate's cell block directly
    $conn->query("UPDATE inmates SET cell_block = '$new_block' WHERE inmate_id = $inmate_id");
    
    // Record the transfer
    $sql = "INSERT INTO transfers (inmate_id, from_block, to_block, transfer_date, approved_by) 
            VALUES ($inmate_id, '{$current['cell_block']}', '$new_block', '$transfer_date', $staff_id)";
    $conn->query($sql);
    
    header("Location: transfers.php");
    exit();
}

include '../../partials/header.php';
include '../../partials/sidebar.php';

// Fetch all inmates for cell block updates
$inmates_result = $conn->query("SELECT inmate_id, first_name, last_name, cell_block FROM inmates ORDER BY first_name ASC");

// Fetch completed transfers
$result = $conn->query("
    SELECT t.*, i.first_name, i.last_name, i.photo_path, u.full_name as staff_name
    FROM transfers t
    INNER JOIN inmates i ON t.inmate_id = i.inmate_id
    LEFT JOIN users u ON t.approved_by = u.user_id
    ORDER BY t.transfer_date DESC
");

// Fetch statistics
$total_transfers = $conn->query("SELECT COUNT(*) as count FROM transfers")->fetch_assoc()['count'];
$recent_transfers = $conn->query("SELECT COUNT(*) as count FROM transfers WHERE transfer_date >= DATE_SUB(NOW(), INTERVAL 30 DAY)")->fetch_assoc()['count'];
$total_inmates = $conn->query("SELECT COUNT(*) as count FROM inmates")->fetch_assoc()['count'];
?>

<main class="flex-1 p-6 bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Inmate Transfers Management</h1>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white/80 backdrop-blur-lg border border-gray-200/50 p-6 rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-100 rounded-xl group-hover:bg-indigo-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Transfers</h3>
                        <p class="text-3xl font-extrabold text-indigo-600 mt-1"><?php echo $total_transfers; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white/80 backdrop-blur-lg border border-gray-200/50 p-6 rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 rounded-xl group-hover:bg-green-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">This Month</h3>
                        <p class="text-3xl font-extrabold text-green-600 mt-1"><?php echo $recent_transfers; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white/80 backdrop-blur-lg border border-gray-200/50 p-6 rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-purple-100 rounded-xl group-hover:bg-purple-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 8.646 4 4 0 010-8.646M9 13H3v4a6 6 0 0012 0v-4h-6z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Inmates</h3>
                        <p class="text-3xl font-extrabold text-purple-600 mt-1"><?php echo $total_inmates; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Inmates Cell Block Management -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100 mb-6">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-blue-50">
                <h2 class="text-lg font-semibold text-gray-800">Update Inmate Cell Blocks</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Inmate Name</th>
                            <th class="px-6 py-3 text-left">Current Cell Block</th>
                            <th class="px-6 py-3 text-left">New Cell Block</th>
                            <th class="px-6 py-3 text-left">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php 
                        $inmates_result->data_seek(0);
                        if ($inmates_result->num_rows > 0): 
                            while ($inmate = $inmates_result->fetch_assoc()): 
                        ?>
                            <tr class="hover:bg-gray-50 transition-all">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($inmate['cell_block']); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="text" class="new-block-input border border-gray-300 rounded-lg px-3 py-1 text-sm focus:ring-indigo-500 focus:border-indigo-500" 
                                        placeholder="Enter new block" data-inmate-id="<?php echo $inmate['inmate_id']; ?>" data-old-block="<?php echo htmlspecialchars($inmate['cell_block']); ?>">
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <button class="transfer-btn bg-indigo-600 text-white px-4 py-1 rounded-lg hover:bg-indigo-700 transition-all text-sm font-medium"
                                        data-inmate-id="<?php echo $inmate['inmate_id']; ?>">Transfer</button>
                                </td>
                            </tr>
                        <?php 
                            endwhile; 
                        endif; 
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Transfer Records -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="px-6 py-4 border-b bg-gradient-to-r from-indigo-50 to-blue-50">
                <h2 class="text-lg font-semibold text-gray-800">Transfer Records</h2>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Transfer ID</th>
                            <th class="px-6 py-3 text-left">Inmate Name</th>
                            <th class="px-6 py-3 text-left">From Block</th>
                            <th class="px-6 py-3 text-left">To Block</th>
                            <th class="px-6 py-3 text-left">Transfer Date</th>
                            <th class="px-6 py-3 text-left">Processed By</th>
                            <th class="px-6 py-3 text-left">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr class="hover:bg-gray-50 transition-all">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $row['transfer_id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['from_block']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['to_block']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo $row['transfer_date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700"><?php echo htmlspecialchars($row['staff_name'] ?? 'System'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="viewBtn text-indigo-600 hover:text-indigo-800 transition-colors"
                                            data-id="<?php echo $row['transfer_id']; ?>"
                                            data-photo="<?php echo htmlspecialchars($row['photo_path']); ?>"
                                            data-inmate="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                            data-from="<?php echo htmlspecialchars($row['from_block']); ?>"
                                            data-to="<?php echo htmlspecialchars($row['to_block']); ?>"
                                            data-date="<?php echo $row['transfer_date']; ?>"
                                            data-staff="<?php echo htmlspecialchars($row['staff_name'] ?? 'System'); ?>">View Details</button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="px-6 py-4 text-center text-gray-500">No transfer records found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden animate-fade-in z-[9999]">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 animate-scale-in">
        <h3 class="text-2xl font-semibold text-gray-900 text-center mb-4">Transfer Details</h3>
        <div class="space-y-3 text-gray-700">
            <img id="view-photo" src="" alt="Inmate Photo" class="w-24 h-24 object-cover rounded-full mx-auto border">
            <p><strong>ID:</strong> <span id="view-id"></span></p>
            <p><strong>Inmate:</strong> <span id="view-inmate"></span></p>
            <p><strong>From Block:</strong> <span id="view-from"></span></p>
            <p><strong>To Block:</strong> <span id="view-to"></span></p>
            <p><strong>Date:</strong> <span id="view-date"></span></p>
            <p><strong>Processed By:</strong> <span id="view-staff"></span></p>
        </div>
        <button id="closeViewModal" class="mt-6 w-full bg-gray-200 text-gray-800 py-2 rounded-lg hover:bg-gray-300 transition-all">Close</button>
    </div>
</div>

<?php include '../../partials/footer.php'; ?>

<script>
    // Handle direct transfer button clicks
    document.querySelectorAll('.transfer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const inmateId = this.getAttribute('data-inmate-id');
            const newBlockInput = document.querySelector(`.new-block-input[data-inmate-id="${inmateId}"]`);
            const newBlock = newBlockInput.value.trim();
            const oldBlock = newBlockInput.getAttribute('data-old-block');

            if (!newBlock) {
                alert('Please enter a new cell block');
                return;
            }

            if (newBlock === oldBlock) {
                alert('New cell block must be different from current cell block');
                return;
            }

            // Create form and submit
            const form = document.createElement('form');
            form.method = 'POST';
            form.innerHTML = `
                <input type="hidden" name="inmate_id" value="${inmateId}">
                <input type="hidden" name="new_block" value="${newBlock}">
                <input type="hidden" name="transfer_date" value="${new Date().toISOString().split('T')[0]}">
                <input type="hidden" name="update_transfer" value="1">
            `;
            document.body.appendChild(form);
            form.submit();
        });
    });

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
            document.getElementById('view-from').textContent = this.getAttribute('data-from');
            document.getElementById('view-to').textContent = this.getAttribute('data-to');
            document.getElementById('view-date').textContent = this.getAttribute('data-date');
            document.getElementById('view-staff').textContent = this.getAttribute('data-staff');
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
</script>
