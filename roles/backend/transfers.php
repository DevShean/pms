<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Handle approve/decline actions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    $transfer_id = intval($_POST['transfer_id']);
    $action = $_POST['action'];
    $staff_id = $_SESSION['user_id'];

    if ($action == 'approve') {
        $conn->query("UPDATE transfers SET approved_by = $staff_id WHERE transfer_id = $transfer_id");
        $transfer = $conn->query("SELECT inmate_id, to_block FROM transfers WHERE transfer_id = $transfer_id")->fetch_assoc();
        if ($transfer) {
            $conn->query("UPDATE inmates SET cell_block = '{$transfer['to_block']}' WHERE inmate_id = {$transfer['inmate_id']}");
        }
    } elseif ($action == 'decline') {
        $conn->query("DELETE FROM transfers WHERE transfer_id = $transfer_id");
    }
    header("Location: transfers.php");
    exit();
}

// Fetch transfer requests
$result = $conn->query("
    SELECT t.*, i.first_name, i.last_name, i.photo_path, u.full_name as staff_name
    FROM transfers t
    INNER JOIN inmates i ON t.inmate_id = i.inmate_id
    LEFT JOIN users u ON t.approved_by = u.user_id
    ORDER BY t.transfer_date DESC
");

// Fetch statistics
$total_requests = $conn->query("SELECT COUNT(*) as count FROM transfers")->fetch_assoc()['count'];
$pending_requests = $conn->query("SELECT COUNT(*) as count FROM transfers WHERE approved_by IS NULL")->fetch_assoc()['count'];
$approved_requests = $conn->query("SELECT COUNT(*) as count FROM transfers WHERE approved_by IS NOT NULL")->fetch_assoc()['count'];

// Fetch data for select dropdowns
$inmates_result = $conn->query("SELECT inmate_id, first_name, last_name FROM inmates ORDER BY first_name ASC");
$users_result = $conn->query("SELECT user_id, full_name FROM users WHERE role_id IN (1,2,3,4) ORDER BY full_name ASC");
?>

<main class="flex-1 p-6 bg-blue-50 min-h-screen">
    <div class="max-w-7xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-900 mb-6">Transfers Management</h1>

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800">Total Transfers</h3>
                <p class="text-3xl font-bold text-blue-600"><?php echo $total_requests; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800">Approved Transfers</h3>
                <p class="text-3xl font-bold text-green-600"><?php echo $approved_requests; ?></p>
            </div>
            <div class="bg-white p-6 rounded-lg shadow-md">
                <h3 class="text-lg font-semibold text-gray-800">Pending Approvals</h3>
                <p class="text-3xl font-bold text-orange-600"><?php echo $pending_requests; ?></p>
            </div>
        </div>

        <!-- Transfer Records -->
        <div class="bg-white shadow-md rounded-lg overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-800">Transfer Records</h2>
                <button id="addBtn" class="bg-emerald-600 text-white px-4 py-2 rounded hover:bg-emerald-700 transition">Add New Transfer</button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full table-auto">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inmate Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">From Block</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">To Block</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transfer Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Approved By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo $row['transfer_id']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['from_block']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['to_block']); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo $row['transfer_date']; ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($row['staff_name'] ?? 'Pending'); ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button class="viewBtn text-blue-600 hover:text-blue-900 mr-3"
                                            data-id="<?php echo $row['transfer_id']; ?>"
                                            data-photo="<?php echo htmlspecialchars($row['photo_path']); ?>"
                                            data-inmate="<?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>"
                                            data-from="<?php echo htmlspecialchars($row['from_block']); ?>"
                                            data-to="<?php echo htmlspecialchars($row['to_block']); ?>"
                                            data-date="<?php echo $row['transfer_date']; ?>"
                                            data-approver="<?php echo htmlspecialchars($row['staff_name'] ?? 'N/A'); ?>"
                                            data-reason="<?php echo htmlspecialchars($row['reason'] ?? 'N/A'); ?>">View</button>

                                        <?php if (!$row['approved_by']): ?>
                                            <form method="post" style="display:inline;">
                                                <input type="hidden" name="transfer_id" value="<?php echo $row['transfer_id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="text-green-600 hover:text-green-900 mr-2">Approve</button>
                                            </form>
                                            <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to decline this transfer?');">
                                                <input type="hidden" name="transfer_id" value="<?php echo $row['transfer_id']; ?>">
                                                <input type="hidden" name="action" value="decline">
                                                <button type="submit" class="text-red-600 hover:text-red-900">Decline</button>
                                            </form>
                                        <?php else: ?>
                                            <span class="text-gray-500">Approved</span>
                                        <?php endif; ?>
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

<!-- ================= Modals ================= -->
<!-- Add Modal -->
<div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-[9999] overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <h3 class="text-lg font-medium text-gray-900 text-center mb-4">Add New Transfer</h3>
        <form method="post">
            <div class="space-y-3">
                <select name="inmate_id" required class="w-full px-3 py-2 border rounded">
                    <option value="">Select Inmate</option>
                    <?php while ($inmate = $inmates_result->fetch_assoc()): ?>
                        <option value="<?php echo $inmate['inmate_id']; ?>"><?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?></option>
                    <?php endwhile; ?>
                </select>
                <input type="text" name="from_block" placeholder="From Block" required class="w-full px-3 py-2 border rounded">
                <input type="text" name="to_block" placeholder="To Block" required class="w-full px-3 py-2 border rounded">
                <input type="date" name="transfer_date" required class="w-full px-3 py-2 border rounded">
                <select name="approved_by" required class="w-full px-3 py-2 border rounded">
                    <option value="">Select Approver</option>
                    <?php while ($user = $users_result->fetch_assoc()): ?>
                        <option value="<?php echo $user['user_id']; ?>"><?php echo htmlspecialchars($user['full_name']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="flex items-center mt-4">
                <button type="submit" name="add_transfer" class="w-full bg-emerald-600 text-white py-2 rounded hover:bg-emerald-700">Add</button>
                <button type="button" id="closeModal" class="ml-3 w-full bg-gray-300 text-gray-900 py-2 rounded hover:bg-gray-400">Cancel</button>
            </div>
        </form>
    </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden z-[9999] overflow-y-auto h-full w-full">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white text-center">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Transfer Details</h3>
        <img id="view-photo" src="" alt="Inmate Photo" class="w-24 h-24 object-cover rounded-full mx-auto mb-3">
        <div class="text-left space-y-1 text-sm">
            <p><strong>ID:</strong> <span id="view-id"></span></p>
            <p><strong>Inmate:</strong> <span id="view-inmate"></span></p>
            <p><strong>From:</strong> <span id="view-from"></span></p>
            <p><strong>To:</strong> <span id="view-to"></span></p>
            <p><strong>Date:</strong> <span id="view-date"></span></p>
            <p><strong>Approved By:</strong> <span id="view-approver"></span></p>
            <p><strong>Reason:</strong> <span id="view-reason"></span></p>
        </div>
        <button id="closeViewModal" class="mt-4 bg-gray-300 text-gray-900 py-2 px-4 rounded hover:bg-gray-400">Close</button>
    </div>
</div>

<?php include '../../partials/footer.php'; ?>


<script>
    const addModal = document.getElementById('addModal');
    const viewModal = document.getElementById('viewModal');
    const addBtn = document.getElementById('addBtn');
    const closeModal = document.getElementById('closeModal');
    const closeViewModal = document.getElementById('closeViewModal');

    addBtn.onclick = () => addModal.classList.remove('hidden');
    closeModal.onclick = () => addModal.classList.add('hidden');
    closeViewModal.onclick = () => viewModal.classList.add('hidden');

    const viewBtns = document.querySelectorAll('.viewBtn');
    viewBtns.forEach(btn => {
        btn.onclick = function() {
            document.getElementById('view-photo').src = this.dataset.photo || 'https://via.placeholder.com/96x96?text=No+Photo';
            document.getElementById('view-id').textContent = this.dataset.id;
            document.getElementById('view-inmate').textContent = this.dataset.inmate;
            document.getElementById('view-from').textContent = this.dataset.from;
            document.getElementById('view-to').textContent = this.dataset.to;
            document.getElementById('view-date').textContent = this.dataset.date;
            document.getElementById('view-approver').textContent = this.dataset.approver;
            document.getElementById('view-reason').textContent = this.dataset.reason;
            viewModal.classList.remove('hidden');
        };
    });

    // Combined window click listener
    window.onclick = function(event) {
        if (event.target === addModal) addModal.classList.add('hidden');
        if (event.target === viewModal) viewModal.classList.add('hidden');
    };
</script>
