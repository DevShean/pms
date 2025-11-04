<?php
require '../../includes/session_check.php';
require '../../config/config.php';

/* --- Handle user deletion --- */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role_id != 5");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: users.php?deleted=1");
    exit;
}

/* --- Handle password reset --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $user_id = intval($_POST['user_id']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_password, $user_id);
    $stmt->execute();
    header("Location: users.php?password_reset=1");
    exit;
}

/* --- Handle user creation --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $role_id = intval($_POST['role_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($role_id && $role_id !== 5) {
        $stmt = $conn->prepare("INSERT INTO users (role_id, full_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $role_id, $full_name, $email, $password);
        $stmt->execute();
        header("Location: users.php?added=1");
        exit;
    }
}

/* --- Fetch all users --- */
$sql = "SELECT u.*, r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.role_id 
        WHERE u.role_id != 5 
        ORDER BY u.created_at DESC";
$result = $conn->query($sql);

/* --- Fetch all roles for dropdown --- */
$roles_sql = "SELECT role_id, role_name FROM roles WHERE role_id != 5";
$roles_result = $conn->query($roles_sql);

include '../../partials/header.php';
include '../../partials/sidebar.php';
?>


<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-6">

        <?php if (isset($_GET['deleted'])): ?>
            <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">User has been successfully deleted.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('success-alert').style.display='none';">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['password_reset'])): ?>
            <div id="success-alert-reset" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Password has been successfully reset.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('success-alert-reset').style.display='none';">
                    <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add User Form -->
            <div class="lg:col-span-1 bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-slate-800 mb-6">Add New User</h2>
                <form id="addUserForm" method="POST" class="space-y-5" onsubmit="return validateForm()">
                    <div>
                        <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                        <small id="fullNameError" class="text-red-500 mt-1 text-xs hidden">Full name is required.</small>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                        <small id="emailError" class="text-red-500 mt-1 text-xs hidden">Please enter a valid email address.</small>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="text" id="password" name="password" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                        </div>
                        <div id="passwordStrength" class="w-full bg-slate-200 rounded-full h-2 mt-2">
                            <div class="h-2 rounded-full"></div>
                        </div>
                        <small id="passwordError" class="text-red-500 mt-1 text-xs hidden">Password must be at least 8 characters long.</small>
                    </div>
                    
                    <div>
                        <label for="role_id" class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                        <select id="role_id" name="role_id" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                            <option value="">-- Select Role --</option>
                            <?php while ($role_row = $roles_result->fetch_assoc()): ?>
                                <option value="<?= $role_row['role_id'] ?>"><?= htmlspecialchars($role_row['role_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small id="roleError" class="text-red-500 mt-1 text-xs hidden">Please select a role.</small>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" name="create_user" class="w-full px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-semibold shadow-md hover:shadow-lg">Add User</button>
                    </div>
                </form>
            </div>

            <!-- User List Table -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-slate-800">User List</h2>
                </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Full Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Reset Password</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Email</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Role</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Date Created</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50 transition">
                  <td class="px-6 py-4 text-slate-800 font-medium"><?= htmlspecialchars($row['full_name']) ?></td>
                  <td class="px-6 py-4">
                    <button onclick="openResetModal(<?= $row['user_id'] ?>)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Reset Password</button>
                  </td>
                  <td class="px-6 py-4 text-slate-700"><?= htmlspecialchars($row['email']) ?></td>
                  <td class="px-6 py-4">
                    <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($row['role_name']) ?></span>
                  </td>
                  <td class="px-6 py-4 text-slate-600 text-sm"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                  <td class="px-6 py-4">
                    <button onclick="openDeleteModal(<?= $row['user_id'] ?>)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">No users found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 m-4 transform transition-transform duration-300 scale-95">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-5">Delete User</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="flex justify-center space-x-4 mt-6">
                <button type="button" onclick="closeDeleteModal()" class="px-6 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors font-semibold">Cancel</button>
                <a id="deleteConfirmBtn" href="#" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md hover:shadow-lg">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 m-4 transform transition-transform duration-300 scale-95">
        <form method="POST">
            <input type="hidden" name="user_id" id="resetUserId">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Reset Password</h3>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="text" name="new_password" id="new_password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="closeResetModal()" class="px-6 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors font-semibold">Cancel</button>
                <button type="submit" name="reset_password" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-md hover:shadow-lg">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteModal(userId) {
    const modal = document.getElementById('deleteModal');
    const deleteBtn = document.getElementById('deleteConfirmBtn');
    deleteBtn.href = `?delete=${userId}`;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function openResetModal(userId) {
    const modal = document.getElementById('resetModal');
    document.getElementById('resetUserId').value = userId;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeResetModal() {
    const modal = document.getElementById('resetModal');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function validateForm() {
  let isValid = true;
  resetErrors();

  // Full Name
  const fullName = document.getElementById('full_name');
  if (fullName.value.trim() === '') {
    showError('fullNameError', 'Full name is required.');
    isValid = false;
  }

  // Email
  const email = document.getElementById('email');
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    showError('emailError', 'Please enter a valid email address.');
    isValid = false;
  }

  // Password
  const password = document.getElementById('password');
  if (password.value.length < 8) {
    showError('passwordError', 'Password must be at least 8 characters long.');
    isValid = false;
  }

  // Role
  const role = document.getElementById('role_id');
  if (role.value === '') {
    showError('roleError', 'Please select a role.');
    isValid = false;
  }

  return isValid;
}

function showError(elementId, message) {
  const errorElement = document.getElementById(elementId);
  errorElement.textContent = message;
  errorElement.classList.remove('hidden');
  document.getElementById(elementId.replace('Error', '')).classList.add('border-red-500');
}

function resetErrors() {
  const errorMessages = document.querySelectorAll('small[id$="Error"]');
  errorMessages.forEach(msg => msg.classList.add('hidden'));
  const inputs = document.querySelectorAll('#addUserForm input, #addUserForm select');
  inputs.forEach(input => input.classList.remove('border-red-500'));
}

// Password strength
document.addEventListener('DOMContentLoaded', () => {
  const passwordInput = document.getElementById('password');
  const strengthBar = document.querySelector('#passwordStrength div');

  if (passwordInput) {
    passwordInput.addEventListener('input', () => {
      const pass = passwordInput.value;
      let score = 0;
      if (pass.length > 8) score++;
      if (/[A-Z]/.test(pass)) score++;
      if (/[a-z]/.test(pass)) score++;
      if (/[0-9]/.test(pass)) score++;
      if (/[^A-Za-z0-9]/.test(pass)) score++;
      
      strengthBar.className = 'h-2 rounded-full transition-all';
      switch (score) {
        case 0:
        case 1:
          strengthBar.classList.add('w-1/5', 'bg-red-500');
          break;
        case 2:
          strengthBar.classList.add('w-2/5', 'bg-orange-500');
          break;
        case 3:
          strengthBar.classList.add('w-3/5', 'bg-yellow-500');
          break;
        case 4:
          strengthBar.classList.add('w-4/5', 'bg-blue-500');
          break;
        case 5:
          strengthBar.classList.add('w-full', 'bg-emerald-500');
          break;
      }
    });
  }
});
</script>

<?php include '../../partials/footer.php'; ?>
<?php
require '../../includes/session_check.php';
require '../../config/config.php';

/* --- Handle user deletion --- */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);
    $stmt = $conn->prepare("DELETE FROM users WHERE user_id = ? AND role_id != 5");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    header("Location: users.php?deleted=1");
    exit;
}

/* --- Handle password reset --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $user_id = intval($_POST['user_id']);
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE user_id = ?");
    $stmt->bind_param("si", $new_password, $user_id);
    $stmt->execute();
    header("Location: users.php?password_reset=1");
    exit;
}

/* --- Handle user creation --- */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $role_id = intval($_POST['role_id']);
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    if ($role_id && $role_id !== 5) {
        $stmt = $conn->prepare("INSERT INTO users (role_id, full_name, email, password) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $role_id, $full_name, $email, $password);
        $stmt->execute();
        header("Location: users.php?added=1");
        exit;
    }
}

/* --- Fetch all users --- */
$sql = "SELECT u.*, r.role_name 
        FROM users u 
        JOIN roles r ON u.role_id = r.role_id 
        WHERE u.role_id != 5 
        ORDER BY u.created_at DESC";
$result = $conn->query($sql);

/* --- Fetch all roles for dropdown --- */
$roles_sql = "SELECT role_id, role_name FROM roles WHERE role_id != 5";
$roles_result = $conn->query($roles_sql);

include '../../partials/header.php';
include '../../partials/sidebar.php';
?>


<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">
    <div class="max-w-7xl mx-auto space-y-6">

        <?php if (isset($_GET['deleted'])): ?>
            <div id="success-alert" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">User has been successfully deleted.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('success-alert').style.display='none';">
                    <svg class="fill-current h-6 w-6 text-green-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['password_reset'])): ?>
            <div id="success-alert-reset" class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded relative" role="alert">
                <strong class="font-bold">Success!</strong>
                <span class="block sm:inline">Password has been successfully reset.</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="document.getElementById('success-alert-reset').style.display='none';">
                    <svg class="fill-current h-6 w-6 text-blue-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                </span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Add User Form -->
            <div class="lg:col-span-1 bg-white border border-slate-200 rounded-xl shadow-sm p-6">
                <h2 class="text-2xl font-bold text-slate-800 mb-6">Add New User</h2>
                <form id="addUserForm" method="POST" class="space-y-5" onsubmit="return validateForm()">
                    <div>
                        <label for="full_name" class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
                        <input type="text" id="full_name" name="full_name" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                        <small id="fullNameError" class="text-red-500 mt-1 text-xs hidden">Full name is required.</small>
                    </div>
                    
                    <div>
                        <label for="email" class="block text-sm font-semibold text-slate-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                        <small id="emailError" class="text-red-500 mt-1 text-xs hidden">Please enter a valid email address.</small>
                    </div>
                    
                    <div>
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1">Password</label>
                        <div class="relative">
                            <input type="text" id="password" name="password" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                        </div>
                        <div id="passwordStrength" class="w-full bg-slate-200 rounded-full h-2 mt-2">
                            <div class="h-2 rounded-full"></div>
                        </div>
                        <small id="passwordError" class="text-red-500 mt-1 text-xs hidden">Password must be at least 8 characters long.</small>
                    </div>
                    
                    <div>
                        <label for="role_id" class="block text-sm font-semibold text-slate-700 mb-1">Role</label>
                        <select id="role_id" name="role_id" class="w-full border border-slate-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-shadow">
                            <option value="">-- Select Role --</option>
                            <?php while ($role_row = $roles_result->fetch_assoc()): ?>
                                <option value="<?= $role_row['role_id'] ?>"><?= htmlspecialchars($role_row['role_name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                        <small id="roleError" class="text-red-500 mt-1 text-xs hidden">Please select a role.</small>
                    </div>
                    
                    <div class="flex justify-end pt-4">
                        <button type="submit" name="create_user" class="w-full px-6 py-2.5 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors font-semibold shadow-md hover:shadow-lg">Add User</button>
                    </div>
                </form>
            </div>

            <!-- User List Table -->
            <div class="lg:col-span-2 bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="p-6">
                    <h2 class="text-2xl font-bold text-slate-800">User List</h2>
                </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-slate-200">
          <thead class="bg-slate-100">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Full Name</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Reset Password</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Email</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Role</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Date Created</th>
              <th class="px-6 py-3 text-left text-xs font-semibold text-slate-600 uppercase">Actions</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100">
            <?php if ($result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr class="hover:bg-slate-50 transition">
                  <td class="px-6 py-4 text-slate-800 font-medium"><?= htmlspecialchars($row['full_name']) ?></td>
                  <td class="px-6 py-4">
                    <button onclick="openResetModal(<?= $row['user_id'] ?>)" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Reset Password</button>
                  </td>
                  <td class="px-6 py-4 text-slate-700"><?= htmlspecialchars($row['email']) ?></td>
                  <td class="px-6 py-4">
                    <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($row['role_name']) ?></span>
                  </td>
                  <td class="px-6 py-4 text-slate-600 text-sm"><?= date('M d, Y', strtotime($row['created_at'])) ?></td>
                  <td class="px-6 py-4">
                    <button onclick="openDeleteModal(<?= $row['user_id'] ?>)" class="text-red-600 hover:text-red-800 text-sm font-medium">Delete</button>
                  </td>
                </tr>
              <?php endwhile; ?>
            <?php else: ?>
              <tr><td colspan="6" class="px-6 py-12 text-center text-slate-500">No users found.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</main>

<!-- Delete Confirmation Modal -->
<div id="deleteModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 m-4 transform transition-transform duration-300 scale-95">
        <div class="text-center">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mt-5">Delete User</h3>
            <div class="mt-2 px-7 py-3">
                <p class="text-sm text-gray-500">Are you sure you want to delete this user? This action cannot be undone.</p>
            </div>
            <div class="flex justify-center space-x-4 mt-6">
                <button type="button" onclick="closeDeleteModal()" class="px-6 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors font-semibold">Cancel</button>
                <a id="deleteConfirmBtn" href="#" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors font-semibold shadow-md hover:shadow-lg">Delete</a>
            </div>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div id="resetModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 transition-opacity duration-300">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md p-8 m-4 transform transition-transform duration-300 scale-95">
        <form method="POST">
            <input type="hidden" name="user_id" id="resetUserId">
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Reset Password</h3>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <input type="text" name="new_password" id="new_password" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="flex justify-end space-x-4 mt-6">
                <button type="button" onclick="closeResetModal()" class="px-6 py-2 bg-slate-200 text-slate-800 rounded-lg hover:bg-slate-300 transition-colors font-semibold">Cancel</button>
                <button type="submit" name="reset_password" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors font-semibold shadow-md hover:shadow-lg">Reset</button>
            </div>
        </form>
    </div>
</div>

<script>
function openDeleteModal(userId) {
    const modal = document.getElementById('deleteModal');
    const deleteBtn = document.getElementById('deleteConfirmBtn');
    deleteBtn.href = `?delete=${userId}`;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeDeleteModal() {
    const modal = document.getElementById('deleteModal');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function openResetModal(userId) {
    const modal = document.getElementById('resetModal');
    document.getElementById('resetUserId').value = userId;
    modal.classList.remove('hidden');
    setTimeout(() => {
        modal.querySelector('.transform').classList.remove('scale-95');
    }, 10);
}

function closeResetModal() {
    const modal = document.getElementById('resetModal');
    modal.querySelector('.transform').classList.add('scale-95');
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

function validateForm() {
  let isValid = true;
  resetErrors();

  // Full Name
  const fullName = document.getElementById('full_name');
  if (fullName.value.trim() === '') {
    showError('fullNameError', 'Full name is required.');
    isValid = false;
  }

  // Email
  const email = document.getElementById('email');
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email.value)) {
    showError('emailError', 'Please enter a valid email address.');
    isValid = false;
  }

  // Password
  const password = document.getElementById('password');
  if (password.value.length < 8) {
    showError('passwordError', 'Password must be at least 8 characters long.');
    isValid = false;
  }

  // Role
  const role = document.getElementById('role_id');
  if (role.value === '') {
    showError('roleError', 'Please select a role.');
    isValid = false;
  }

  return isValid;
}

function showError(elementId, message) {
  const errorElement = document.getElementById(elementId);
  errorElement.textContent = message;
  errorElement.classList.remove('hidden');
  document.getElementById(elementId.replace('Error', '')).classList.add('border-red-500');
}

function resetErrors() {
  const errorMessages = document.querySelectorAll('small[id$="Error"]');
  errorMessages.forEach(msg => msg.classList.add('hidden'));
  const inputs = document.querySelectorAll('#addUserForm input, #addUserForm select');
  inputs.forEach(input => input.classList.remove('border-red-500'));
}

// Password strength
document.addEventListener('DOMContentLoaded', () => {
  const passwordInput = document.getElementById('password');
  const strengthBar = document.querySelector('#passwordStrength div');

  if (passwordInput) {
    passwordInput.addEventListener('input', () => {
      const pass = passwordInput.value;
      let score = 0;
      if (pass.length > 8) score++;
      if (/[A-Z]/.test(pass)) score++;
      if (/[a-z]/.test(pass)) score++;
      if (/[0-9]/.test(pass)) score++;
      if (/[^A-Za-z0-9]/.test(pass)) score++;
      
      strengthBar.className = 'h-2 rounded-full transition-all';
      switch (score) {
        case 0:
        case 1:
          strengthBar.classList.add('w-1/5', 'bg-red-500');
          break;
        case 2:
          strengthBar.classList.add('w-2/5', 'bg-orange-500');
          break;
        case 3:
          strengthBar.classList.add('w-3/5', 'bg-yellow-500');
          break;
        case 4:
          strengthBar.classList.add('w-4/5', 'bg-blue-500');
          break;
        case 5:
          strengthBar.classList.add('w-full', 'bg-emerald-500');
          break;
      }
    });
  }
});
</script>

<?php include '../../partials/footer.php'; ?>
