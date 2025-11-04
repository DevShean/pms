<?php
require '../../includes/session_check.php';
include '../../config/config.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Handle program CRUD
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_program'])) {
        $program_name = $conn->real_escape_string($_POST['program_name']);
        $program_type = $_POST['program_type'];
        $description = $conn->real_escape_string($_POST['description']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $duration_weeks = intval($_POST['duration_weeks']);
        $capacity = intval($_POST['capacity']);
        $location = $conn->real_escape_string($_POST['location']);
        $assigned_staff_id = intval($_POST['assigned_staff_id']);
        $requirements = $conn->real_escape_string($_POST['requirements']);

        $sql = "INSERT INTO programs (program_name, program_type, description, start_date, end_date, duration_weeks, capacity, location, assigned_staff_id, requirements) VALUES ('$program_name', '$program_type', '$description', '$start_date', '$end_date', $duration_weeks, $capacity, '$location', $assigned_staff_id, '$requirements')";
        $conn->query($sql);
    } elseif (isset($_POST['update_program'])) {
        $program_id = intval($_POST['program_id']);
        $program_name = $conn->real_escape_string($_POST['program_name']);
        $program_type = $_POST['program_type'];
        $description = $conn->real_escape_string($_POST['description']);
        $start_date = $_POST['start_date'];
        $end_date = $_POST['end_date'];
        $duration_weeks = intval($_POST['duration_weeks']);
        $capacity = intval($_POST['capacity']);
        $location = $conn->real_escape_string($_POST['location']);
        $assigned_staff_id = intval($_POST['assigned_staff_id']);
        $status = $_POST['status'];
        $requirements = $conn->real_escape_string($_POST['requirements']);

        $sql = "UPDATE programs SET program_name='$program_name', program_type='$program_type', description='$description', start_date='$start_date', end_date='$end_date', duration_weeks=$duration_weeks, capacity=$capacity, location='$location', assigned_staff_id=$assigned_staff_id, status='$status', requirements='$requirements' WHERE program_id=$program_id";
        $conn->query($sql);
    } elseif (isset($_POST['delete_program'])) {
        $program_id = intval($_POST['delete_program']);
        $conn->query("DELETE FROM programs WHERE program_id=$program_id");
    } elseif (isset($_POST['assign_inmate'])) {
        $inmate_ids = $_POST['inmate_id']; // array
        $program_id = intval($_POST['program_id']);
        $staff_id = $_SESSION['user_id'];
        $start_date = $_POST['start_date'];

        // Get current enrollment and capacity
        $current_result = $conn->query("SELECT COUNT(*) as count FROM inmate_programs WHERE program_id=$program_id AND progress != 'Dropped'");
        $current = $current_result->fetch_assoc()['count'];
        $capacity_result = $conn->query("SELECT capacity FROM programs WHERE program_id=$program_id");
        $capacity = $capacity_result->fetch_assoc()['capacity'];
        $available = $capacity - $current;

        $assigned = 0;
        foreach ($inmate_ids as $inmate_id) {
            if ($assigned >= $available) break;
            $inmate_id = intval($inmate_id);
            $sql = "INSERT INTO inmate_programs (inmate_id, program_id, staff_id, start_date) VALUES ($inmate_id, $program_id, $staff_id, '$start_date')";
            $conn->query($sql);
            $assigned++;
        }
    }
}

// Fetch programs with current enrollment count
$programs = $conn->query("
    SELECT p.*, u.full_name as staff_name,
           (SELECT COUNT(*) FROM inmate_programs ip WHERE ip.program_id = p.program_id AND ip.progress != 'Dropped') as current_enrollment
    FROM programs p
    LEFT JOIN users u ON p.assigned_staff_id = u.user_id
");

// Fetch inmates for assignment (exclude only those with ongoing programs)
$inmates = $conn->query("SELECT inmate_id, first_name, last_name FROM inmates WHERE status='Active' AND inmate_id NOT IN (SELECT inmate_id FROM inmate_programs WHERE progress = 'Ongoing')");

// Fetch staff for assignment
$staff = $conn->query("SELECT user_id, full_name FROM users WHERE role_id=4"); // Rehab staff
?>
<main class="flex-1 p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-8">
        <!-- Page header -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-slate-900">Programs Management</h1>
                    <p class="text-sm text-slate-500 mt-1">Create, manage, and assign rehabilitation programs</p>
                </div>
                <div class="hidden sm:block">
                    <svg class="w-12 h-12 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Add Program Button -->
        <div class="flex justify-end">
            <button id="addProgramBtn" class="inline-flex items-center px-6 py-3 bg-indigo-600 text-white font-medium rounded-lg shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Add New Program
            </button>
        </div>

        <!-- Programs Table -->
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Program Name</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Start Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">End Date</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Enrolled/Capacity</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-slate-600 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <?php while ($program = $programs->fetch_assoc()): ?>
                        <tr class="hover:bg-slate-50 transition-colors duration-150">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-slate-900"><?php echo htmlspecialchars($program['program_name']); ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    <?php
                                    switch ($program['program_type']) {
                                        case 'Educational': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'Vocational': echo 'bg-green-100 text-green-800'; break;
                                        case 'Psychological': echo 'bg-purple-100 text-purple-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo $program['program_type']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo $program['start_date']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600"><?php echo $program['end_date']; ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-600">
                                <span class="font-medium"><?php echo $program['current_enrollment']; ?>/<?php echo $program['capacity']; ?></span>
                                <?php if ($program['current_enrollment'] >= $program['capacity']): ?>
                                    <span class="text-red-500 text-xs">(Full)</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                    <?php
                                    switch ($program['status']) {
                                        case 'Active': echo 'bg-green-100 text-green-800'; break;
                                        case 'Inactive': echo 'bg-gray-100 text-gray-800'; break;
                                        case 'Completed': echo 'bg-blue-100 text-blue-800'; break;
                                        case 'Cancelled': echo 'bg-red-100 text-red-800'; break;
                                        default: echo 'bg-gray-100 text-gray-800';
                                    }
                                    ?>">
                                    <?php echo $program['status']; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                <button onclick="editProgram(<?php echo $program['program_id']; ?>)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-indigo-700 bg-indigo-50 rounded-md hover:bg-indigo-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors duration-150">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit
                                </button>
                                <form method="post" style="display:inline;" onsubmit="return confirm('Are you sure you want to delete this program?');">
                                    <input type="hidden" name="delete_program" value="<?php echo $program['program_id']; ?>">
                                    <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-red-700 bg-red-50 rounded-md hover:bg-red-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                        Delete
                                    </button>
                                </form>
                                <?php if ($program['current_enrollment'] < $program['capacity']): ?>
                                    <button onclick="assignInmate(<?php echo $program['program_id']; ?>)" class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-green-700 bg-green-50 rounded-md hover:bg-green-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors duration-150">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Assign
                                    </button>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1.5 text-sm font-medium text-gray-500 bg-gray-50 rounded-md">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        Full
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Add Program Modal -->
<div id="addProgramModal" class="fixed inset-0 bg-slate-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-slate-900" id="modalTitle">Add New Program</h3>
                    <button id="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="program_id" id="program_id">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Program Name</label>
                        <input type="text" name="program_name" id="program_name" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Program Type</label>
                        <select name="program_type" id="program_type" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="Educational">Educational</option>
                            <option value="Vocational">Vocational</option>
                            <option value="Psychological">Psychological</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
                            <input type="date" name="start_date" id="start_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">End Date</label>
                            <input type="date" name="end_date" id="end_date" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Duration (Weeks)</label>
                            <input type="number" name="duration_weeks" id="duration_weeks" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-2">Capacity</label>
                            <input type="number" name="capacity" id="capacity" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Location</label>
                        <input type="text" name="location" id="location" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Assigned Staff</label>
                        <select name="assigned_staff_id" id="assigned_staff_id" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <?php $staff->data_seek(0); while ($s = $staff->fetch_assoc()): ?>
                            <option value="<?php echo $s['user_id']; ?>"><?php echo htmlspecialchars($s['full_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div id="statusDiv" style="display:none;">
                        <label class="block text-sm font-medium text-slate-700 mb-2">Status</label>
                        <select name="status" id="status" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                            <option value="Active">Active</option>
                            <option value="Inactive">Inactive</option>
                            <option value="Completed">Completed</option>
                            <option value="Cancelled">Cancelled</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Requirements</label>
                        <textarea name="requirements" id="requirements" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors"></textarea>
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                        <button type="button" id="cancelBtn" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">Cancel</button>
                        <button type="submit" name="add_program" id="submitBtn" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Add Program</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Assign Inmate Modal -->
<div id="assignModal" class="fixed inset-0 bg-slate-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="relative bg-white rounded-xl shadow-xl max-w-md w-full">
            <div class="p-6">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-semibold text-slate-900">Assign Inmates to Program</h3>
                    <button id="closeAssignModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <form method="post" class="space-y-4">
                    <input type="hidden" name="program_id" id="assign_program_id">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Select Inmates (Hold Ctrl to select multiple)</label>
                        <select name="inmate_id[]" multiple required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors" size="6">
                            <?php $inmates->data_seek(0); while ($inmate = $inmates->fetch_assoc()): ?>
                            <option value="<?php echo $inmate['inmate_id']; ?>"><?php echo htmlspecialchars($inmate['first_name'] . ' ' . $inmate['last_name']); ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-2">Start Date</label>
                        <input type="date" name="start_date" required class="w-full px-3 py-2 border border-slate-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-colors">
                    </div>
                    <div class="flex items-center justify-end space-x-3 pt-4 border-t border-slate-200">
                        <button type="button" id="cancelAssignBtn" class="px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-lg hover:bg-slate-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 transition-colors">Cancel</button>
                        <button type="submit" name="assign_inmate" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">Assign Inmates</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    const addProgramBtn = document.getElementById('addProgramBtn');
    const addProgramModal = document.getElementById('addProgramModal');
    const assignModal = document.getElementById('assignModal');
    const closeModal = document.getElementById('closeModal');
    const closeAssignModal = document.getElementById('closeAssignModal');
    const cancelBtn = document.getElementById('cancelBtn');
    const cancelAssignBtn = document.getElementById('cancelAssignBtn');
    const submitBtn = document.getElementById('submitBtn');
    const modalTitle = document.getElementById('modalTitle');
    const statusDiv = document.getElementById('statusDiv');

    addProgramBtn.onclick = function() {
        modalTitle.textContent = 'Add New Program';
        submitBtn.name = 'add_program';
        submitBtn.textContent = 'Add Program';
        statusDiv.style.display = 'none';
        document.getElementById('addProgramModal').querySelector('form').reset();
        addProgramModal.classList.remove('hidden');
    }

    closeModal.onclick = function() {
        addProgramModal.classList.add('hidden');
    }

    cancelBtn.onclick = function() {
        addProgramModal.classList.add('hidden');
    }

    closeAssignModal.onclick = function() {
        assignModal.classList.add('hidden');
    }

    cancelAssignBtn.onclick = function() {
        assignModal.classList.add('hidden');
    }

    window.onclick = function(event) {
        if (event.target == addProgramModal) {
            addProgramModal.classList.add('hidden');
        }
        if (event.target == assignModal) {
            assignModal.classList.add('hidden');
        }
    }

    function editProgram(programId) {
        modalTitle.textContent = 'Edit Program';
        submitBtn.name = 'update_program';
        submitBtn.textContent = 'Update Program';
        statusDiv.style.display = 'block';
        addProgramModal.classList.remove('hidden');

        // Fetch program data and populate modal
        fetch('../rehab/ajax_request/get_program.php?id=' + programId)
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    alert(data.error);
                    addProgramModal.classList.add('hidden');
                    return;
                }
                document.getElementById('program_id').value = data.program_id;
                document.getElementById('program_name').value = data.program_name;
                document.getElementById('program_type').value = data.program_type;
                document.getElementById('description').value = data.description;
                document.getElementById('start_date').value = data.start_date;
                document.getElementById('end_date').value = data.end_date;
                document.getElementById('duration_weeks').value = data.duration_weeks;
                document.getElementById('capacity').value = data.capacity;
                document.getElementById('location').value = data.location;
                document.getElementById('assigned_staff_id').value = data.assigned_staff_id;
                document.getElementById('status').value = data.status;
                document.getElementById('requirements').value = data.requirements;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading program data');
                addProgramModal.classList.add('hidden');
            });
    }

    function assignInmate(programId) {
        document.getElementById('assign_program_id').value = programId;
        assignModal.classList.remove('hidden');
    }
</script>

<?php include '../../partials/footer.php'; ?>
