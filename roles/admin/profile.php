<?php
require '../../includes/session_check.php';

/* Page title for <head> (header.php reads this) */
$page_title = 'Profile';

include '../../partials/header.php';
include '../../partials/sidebar.php';
?>

<main id="content" class="p-6 bg-gradient-to-br from-slate-50 to-slate-100 min-h-[calc(100vh-var(--header-h))]">

  <div class="max-w-2xl mx-auto bg-white shadow-lg rounded-2xl p-8 border border-slate-200">
    <h2 class="text-2xl font-semibold text-slate-700 mb-6 flex items-center gap-2">
      <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 15c2.686 0 5.175.784 7.121 2.121M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
      </svg>
      Edit Profile
    </h2>

    <form action="update_profile.php" method="POST" class="space-y-6">
      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Full Name</label>
        <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" 
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" 
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none" required>
      </div>

      <div>
        <label class="block text-sm font-medium text-slate-600 mb-1">New Password <span class="text-slate-400 text-xs">(optional)</span></label>
        <input type="password" name="password" placeholder="Enter new password"
               class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-400 focus:outline-none">
      </div>

      <div class="flex justify-end gap-3">
        <a href="profile.php" class="px-4 py-2 bg-slate-200 text-slate-700 rounded-lg hover:bg-slate-300 transition">Cancel</a>
        <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition">Save Changes</button>
      </div>
    </form>
  </div>

</main>

<?php include '../../partials/footer.php'; ?>
