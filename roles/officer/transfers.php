<?php
require '../../includes/session_check.php';
include '../../config/config.php';

// Handle add transfer request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_transfer'])) {
    $inmate_id = intval($_POST['inmate_id']);
    $from_block = $conn->real_escape_string($_POST['from_block']);
    $to_block = $conn->real_escape_string($_POST['to_block']);
    $transfer_date = $_POST['transfer_date'];
    $reason = $conn->real_escape_string($_POST['reason']);
    $staff_id = $_SESSION['user_id']; // Assuming session has user_id

    $sql = "INSERT INTO transfers (inmate_id, from_block, to_block, transfer_date, approved_by, reason) VALUES ($inmate_id, '$from_block', '$to_block', '$transfer_date', NULL, '$reason')";
    $conn->query($sql);
    header("Location: transfers.php");
    exit();
}

include '../../partials/header.php';
include '../../partials/sidebar.php';

// Fetch transfer requests with inmate and staff names
$result = $conn->query("
    SELECT t.*, i.first_name, i.last_name, i.photo_path, u.full_name as staff_name
    FROM transfers t
    INNER JOIN inmates i ON t.inmate_id = i.inmate_id
    LEFT JOIN users u ON t.approved_by = u.user_id
    ORDER BY t.transfer_date DESC
");

// Fetch inmates for dropdown
$inmates_result = $conn->query("SELECT inmate_id, first_name, last_name, cell_block FROM inmates");

// Fetch statistics
$total_requests = $conn->query("SELECT COUNT(*) as count FROM transfers")->fetch_assoc()['count'];
$pending_requests = $conn->query("SELECT COUNT(*) as count FROM transfers WHERE approved_by IS NULL")->fetch_assoc()['count'];
$approved_requests = $conn->query("SELECT COUNT(*) as count FROM transfers WHERE approved_by IS NOT NULL")->fetch_assoc()['count'];
?>
<main class="flex-1 min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-100 p-8">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Header -->
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-4">
            <div>
                <h1 class="text-4xl font-bold text-gray-900 tracking-tight bg-gradient-to-r from-indigo-600 to-purple-600 bg-clip-text text-transparent">Transfer Requests</h1>
                <p class="text-gray-600 mt-2 text-lg">Manage and track inmate transfer requests efficiently.</p>
            </div>
            <button id="addBtn" class="inline-flex items-center gap-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white px-6 py-3 rounded-xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Request New Transfer
            </button>
        </div>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white/80 backdrop-blur-lg border border-gray-200/50 p-6 rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-indigo-100 rounded-xl group-hover:bg-indigo-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Requests</h3>
                        <p class="text-3xl font-extrabold text-indigo-600 mt-1"><?php echo $total_requests; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white/80 backdrop-blur-lg border border-gray-200/50 p-6 rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-yellow-100 rounded-xl group-hover:bg-yellow-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Pending</h3>
                        <p class="text-3xl font-extrabold text-yellow-600 mt-1"><?php echo $pending_requests; ?></p>
                    </div>
                </div>
            </div>
            <div class="bg-white/80 backdrop-blur-lg border border-gray-200/50 p-6 rounded-2xl shadow-lg hover:shadow-xl hover:scale-105 transition-all duration-300 group">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-green-100 rounded-xl group-hover:bg-green-200 transition-colors">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Approved</h3>
                        <p class="text-3xl font-extrabold text-green-600 mt-1"><?php echo $approved_requests; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Transfer Requests Table -->
        <div class="bg-white rounded-2xl shadow-lg overflow-hidden border border-gray-100">
            <div class="px-6 py-4 border-b flex justify-between items-center bg-gradient-to-r from-indigo-50 to-blue-50">
                <h2 class="text-lg font-semibold text-gray-800">Transfer Requests List</h2>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-100 text-gray-600 uppercase text-xs font-semibold">
                        <tr>
                            <th class="px-6 py-3 text-left">Transfer ID</th>
                            <th class="px-6 py-3 text-left">Inmate Name</th>
                            <th class="px-6 py-3 text-left">From Block</th>
                            <th class="px-6 py-3 text-left">To Block</th>
                            <th class="px-6 py-3 text-left">Transfer Date</th>
                            <th class="px-6 py-3 text-left">Status</th>
                            <th class="px-6 py-3 text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr class="hover:bg-gray-50 transition-all even:bg-gray-25">
                            <td class="px-6 py-4 text-gray-700"><?php echo $row['transfer_id']; ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars($row['from_block']); ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo htmlspecialchars($row['to_block']); ?></td>
                            <td class="px-6 py-4 text-gray-700"><?php echo $row['transfer_date']; ?></td>
                            <td class="px-6 py-4 text-gray-700">
                                <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                                    <?php echo $row['approved_by'] ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'; ?>">
                                    <?php echo $row['approved_by'] ? 'Approved' : 'Pending'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <button class="viewBtn text-indigo-600 hover:text-indigo-800 font-medium transition-colors"
                                        data-id="<?php echo $row['transfer_id']; ?>"
                                        data-photo="<?php echo htmlspecialchars($row['photo_path']); ?>"
                                        data-inmate="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                        data-from="<?php echo htmlspecialchars($row['from_block']); ?>"
                                        data-to="<?php echo htmlspecialchars($row['to_block']); ?>"
                                        data-date="<?php echo $row['transfer_date']; ?>"
                                        data-status="<?php echo $row['approved_by'] ? 'Approved' : 'Pending'; ?>"
                                        data-approver="<?php echo htmlspecialchars($row['staff_name'] ?? 'N/A'); ?>"
                                        data-reason="<?php echo htmlspecialchars($row['reason'] ?? 'N/A'); ?>">View Details</button>
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
<div id="addModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 animate-scale-in">
        <h3 class="text-2xl font-semibold text-gray-900 text-center mb-4">Request New Transfer</h3>
        <form method="post" class="space-y-4">
            <select name="inmate_id" id="inmate-select" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
                <option value="">Select Inmate</option>
                <?php
                $inmates_result->data_seek(0);
                while ($inmate = $inmates_result->fetch_assoc()):
                ?>
                <option value="<?php echo $inmate['inmate_id']; ?>" data-cell="<?php echo htmlspecialchars($inmate['cell_block']); ?>"><?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="from_block" id="from-block" placeholder="From Block" readonly required class="w-full border-gray-300 rounded-lg bg-gray-100 focus:ring-indigo-500 focus:border-indigo-500">
            <input type="text" name="to_block" placeholder="To Block" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            <input type="date" name="transfer_date" required class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500">
            <textarea name="reason" placeholder="Reason for Transfer" required rows="4" class="w-full border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500"></textarea>
            <div class="flex gap-3">
                <button type="submit" name="add_transfer" class="flex-1 bg-gradient-to-r from-indigo-600 to-purple-600 text-white rounded-lg py-2 hover:shadow-lg transition-all">Request Transfer</button>
                <button type="button" id="closeModal" class="flex-1 bg-gray-200 text-gray-800 rounded-lg py-2 hover:bg-gray-300 transition-all">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center hidden animate-fade-in">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6 animate-scale-in">
        <h3 class="text-2xl font-semibold text-gray-900 text-center mb-4">Transfer Request Details</h3>
        <div class="space-y-3 text-gray-700">
            <img id="view-photo" src="" alt="Inmate Photo" class="w-24 h-24 object-cover rounded-full mx-auto border">
            <p><strong>ID:</strong> <span id="view-id"></span></p>
            <p><strong>Inmate:</strong> <span id="view-inmate"></span></p>
            <p><strong>From Block:</strong> <span id="view-from"></span></p>
            <p><strong>To Block:</strong> <span id="view-to"></span></p>
            <p><strong>Date:</strong> <span id="view-date"></span></p>
            <p><strong>Status:</strong> <span id="view-status"></span></p>
            <p><strong>Approved By:</strong> <span id="view-approver"></span></p>
            <p><strong>Reason:</strong> <span id="view-reason"></span></p>
        </div>
        <button id="closeViewModal" class="mt-6 w-full bg-gray-200 text-gray-800 py-2 rounded-lg hover:bg-gray-300 transition-all">Close</button>
    </div>
</div>

<script>
    const addBtn = document.getElementById('addBtn');
    const addModal = document.getElementById('addModal');
    const closeModal = document.getElementById('closeModal');
    const inmateSelect = document.getElementById('inmate-select');
    const fromBlock = document.getElementById('from-block');

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

    // Auto-fill from block when inmate is selected
    inmateSelect.onchange = function() {
        const selectedOption = this.options[this.selectedIndex];
        const cellBlock = selectedOption.getAttribute('data-cell');
        fromBlock.value = cellBlock || '';
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
            document.getElementById('view-from').textContent = this.getAttribute('data-from');
            document.getElementById('view-to').textContent = this.getAttribute('data-to');
            document.getElementById('view-date').textContent = this.getAttribute('data-date');
            document.getElementById('view-status').textContent = this.getAttribute('data-status');
            document.getElementById('view-approver').textContent = this.getAttribute('data-approver');
            document.getElementById('view-reason').textContent = this.getAttribute('data-reason');
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

<?php include '../../partials/footer.php'; ?>
