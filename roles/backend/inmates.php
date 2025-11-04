<?php
include 'backend/inmates_backend.php';

/* Page title for head */
$page_title = 'Inmates Management';

include '../../partials/header.php';
include '../../partials/sidebar.php';
?>
<main id="content" class="p-6 bg-slate-50 min-h-[calc(100vh-var(--header-h))]">
  <div class="max-w-7xl mx-auto space-y-6">
    <!-- Page header -->
    <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-3">
      <div>
        <h1 class="text-2xl font-semibold text-slate-900">Inmates Management</h1>
        <p class="text-sm text-slate-500">Manage inmate profiles, medical assignments, and records.</p>
      </div>
      <div class="flex flex-wrap items-center gap-3">
        <button id="addBtn"
          class="inline-flex items-center rounded-lg bg-brand-600 px-4 py-2 text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400">
          Add New Inmate
        </button>
        <button id="assignMedicalBtn"
          class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">
          Assign Medical Staff
        </button>
      </div>
    </div>

    <!-- Stats -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Total Inmates</p>
        <p class="mt-1 text-3xl font-bold text-slate-900"><?= (int)$total_inmates ?></p>
      </div>
      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Active Inmates</p>
        <p class="mt-1 text-3xl font-bold text-emerald-600"><?= (int)$active_inmates ?></p>
      </div>
      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Released Inmates</p>
        <p class="mt-1 text-3xl font-bold text-orange-600"><?= (int)$released_inmates ?></p>
      </div>
      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm">
        <p class="text-sm text-slate-500">Transferred Inmates</p>
        <p class="mt-1 text-3xl font-bold text-purple-600"><?= (int)$transferred_inmates ?></p>
      </div>
    </div>

    <!-- Chart + Table -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
      <!-- Chart -->
      <div class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm h-80">
        <h3 class="text-sm font-semibold text-slate-800">Inmate Status Distribution</h3>
        <div class="mt-3 h-[calc(100%-1.5rem)]">
          <canvas id="statusChart"></canvas>
        </div>
      </div>

      <!-- Table -->
      <div class="rounded-xl border border-slate-200 bg-white shadow-sm lg:col-span-2">
        <div class="overflow-x-auto">
          <table class="min-w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
              <tr>
                <th class="px-6 py-3 text-left font-semibold">First Name</th>
                <th class="px-6 py-3 text-left font-semibold">Last Name</th>
                <th class="px-6 py-3 text-left font-semibold">Birthdate</th>
                <th class="px-6 py-3 text-left font-semibold">Gender</th>
                <th class="px-6 py-3 text-left font-semibold">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200">
              <?php if (isset($result) && $result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <tr class="hover:bg-slate-50">
                    <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($row['first_name']) ?></td>
                    <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($row['last_name']) ?></td>
                    <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($row['birthdate']) ?></td>
                    <td class="px-6 py-3 text-slate-700"><?= htmlspecialchars($row['gender']) ?></td>
                    <td class="px-6 py-3">
                      <div class="flex gap-4">
                        <button
                          class="viewBtn text-brand-700 hover:underline"
                          data-id="<?= (int)$row['inmate_id'] ?>"
                          data-photo="<?= htmlspecialchars($row['photo_path']) ?>"
                          data-first-name="<?= htmlspecialchars($row['first_name']) ?>"
                          data-last-name="<?= htmlspecialchars($row['last_name']) ?>"
                          data-birthdate="<?= htmlspecialchars($row['birthdate']) ?>"
                          data-gender="<?= htmlspecialchars($row['gender']) ?>"
                          data-crime="<?= htmlspecialchars($row['crime']) ?>"
                          data-sentence="<?= htmlspecialchars($row['sentence_years']) ?>"
                          data-court="<?= htmlspecialchars($row['court_details']) ?>"
                          data-cell="<?= htmlspecialchars($row['cell_block']) ?>"
                          data-admission="<?= htmlspecialchars($row['admission_date']) ?>"
                          data-release="<?= htmlspecialchars($row['release_date']) ?>"
                          data-status="<?= htmlspecialchars($row['status']) ?>">
                          View
                        </button>

                        <button
                          class="editBtn text-indigo-700 hover:underline"
                          data-id="<?= (int)$row['inmate_id'] ?>"
                          data-photo="<?= htmlspecialchars($row['photo_path']) ?>"
                          data-first-name="<?= htmlspecialchars($row['first_name']) ?>"
                          data-last-name="<?= htmlspecialchars($row['last_name']) ?>"
                          data-birthdate="<?= htmlspecialchars($row['birthdate']) ?>"
                          data-gender="<?= htmlspecialchars($row['gender']) ?>"
                          data-crime="<?= htmlspecialchars($row['crime']) ?>"
                          data-sentence="<?= htmlspecialchars($row['sentence_years']) ?>"
                          data-court="<?= htmlspecialchars($row['court_details']) ?>"
                          data-cell="<?= htmlspecialchars($row['cell_block']) ?>"
                          data-admission="<?= htmlspecialchars($row['admission_date']) ?>"
                          data-release="<?= htmlspecialchars($row['release_date']) ?>"
                          data-status="<?= htmlspecialchars($row['status']) ?>">
                          Edit
                        </button>

                        <form method="post" onsubmit="return confirm('Delete this inmate?');">
                          <input type="hidden" name="delete_id" value="<?= (int)$row['inmate_id'] ?>">
                          <button type="submit" class="text-rose-600 hover:underline">Delete</button>
                        </form>
                      </div>
                    </td>
                  </tr>
                <?php endwhile; ?>
              <?php else: ?>
                <tr>
                  <td colspan="5" class="px-6 py-6 text-center text-slate-500">No inmates found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
          <div id="pagination" class="flex justify-center mt-4 space-x-2"></div>
        </div>
      </div>
    </div>
  </div>
</main>

<!-- ========================= MODALS (Tailwind polished) ========================= -->

<!-- Assign Medical Staff Modal -->
<div id="addMedicalModal" class="modal fixed inset-0 z-[70] hidden items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="addMedicalTitle">
  <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200" data-close="addMedicalModal"></div>
  <div class="modal-panel relative mx-4 w-full max-w-xl origin-center rounded-2xl bg-white shadow-xl opacity-0 scale-95 translate-y-2 transition-all duration-200">
    <div class="flex items-center justify-between border-b px-5 py-4">
      <h3 id="addMedicalTitle" class="text-lg font-semibold text-slate-900">Assign Medical Staff</h3>
      <button class="p-2 rounded-md text-slate-500 hover:bg-slate-100" data-close="addMedicalModal" aria-label="Close">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="px-5 py-4 max-h-[70vh] overflow-y-auto space-y-4">
      <form method="post" class="space-y-4">
        <div>
          <label for="inmate_id" class="block text-sm font-medium text-slate-700">Select Inmate</label>
          <select name="inmate_id" id="inmate_id" required data-autofocus
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
            <option value="">Select Inmate</option>
            <?php
              $inmates_result = $conn->query("SELECT inmate_id, first_name, last_name FROM inmates ORDER BY first_name, last_name");
              while ($inmate = $inmates_result->fetch_assoc()) {
                echo "<option value='".(int)$inmate['inmate_id']."'>".htmlspecialchars($inmate['first_name'].' '.$inmate['last_name'])."</option>";
              }
            ?>
          </select>
        </div>
        <div>
          <label for="staff_id" class="block text-sm font-medium text-slate-700">Medical Staff</label>
          <select name="staff_id" id="staff_id" required
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
            <option value="">Select Medical Staff</option>
            <?php
              $staff_result = $conn->query("SELECT user_id, full_name FROM users WHERE role_id = 3 ORDER BY full_name");
              while ($staff = $staff_result->fetch_assoc()) {
                echo "<option value='".(int)$staff['user_id']."'>".htmlspecialchars($staff['full_name'])."</option>";
              }
            ?>
          </select>
        </div>
        <div>
          <label for="record_date" class="block text-sm font-medium text-slate-700">Record Date</label>
          <input type="date" name="record_date" id="record_date" required
            class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 shadow-sm focus:border-brand-500 focus:ring-2 focus:ring-brand-500">
        </div>
        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="button" data-close="addMedicalModal"
            class="rounded-lg bg-slate-200 px-4 py-2 font-medium text-slate-900 hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300">Cancel</button>
          <button type="submit" name="add_medical_record"
            class="rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">Add Medical Record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Add Inmate Modal -->
<div id="addModal" class="modal fixed inset-0 z-[70] hidden items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="addInmateTitle">
  <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200" data-close="addModal"></div>
  <div class="modal-panel relative mx-4 w-full max-w-2xl origin-center rounded-2xl bg-white shadow-xl opacity-0 scale-95 translate-y-2 transition-all duration-200">
    <div class="flex items-center justify-between border-b px-5 py-4">
      <h3 id="addInmateTitle" class="text-lg font-semibold text-slate-900">Add New Inmate</h3>
      <button class="p-2 rounded-md text-slate-500 hover:bg-slate-100" data-close="addModal" aria-label="Close">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
      <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="text" name="first_name" placeholder="First Name" required data-autofocus class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="last_name" placeholder="Last Name" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="date" name="birthdate" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <select name="gender" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
          <option value="Male">Male</option><option value="Female">Female</option>
        </select>
        <textarea name="crime" placeholder="Crime" required class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
        <input type="number" name="sentence_years" placeholder="Sentence Years" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="cell_block" placeholder="Cell Block" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <textarea name="court_details" placeholder="Court Details" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
        <input type="date" name="admission_date" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="date" name="release_date" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <select name="status" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
          <option value="Active">Active</option>
          <option value="Released">Released</option>
          <option value="Transferred">Transferred</option>
        </select>
        <input type="file" name="photo" accept="image/*" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <div class="md:col-span-2 flex items-center justify-end gap-3 pt-2">
          <button type="button" data-close="addModal" class="rounded-lg bg-slate-200 px-4 py-2 font-medium text-slate-900 hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300">Cancel</button>
          <button type="submit" name="add_inmate" class="rounded-lg bg-brand-600 px-4 py-2 font-medium text-white hover:bg-brand-700 focus:outline-none focus:ring-2 focus:ring-brand-400">Add Inmate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- View Modal -->
<div id="viewModal" class="modal fixed inset-0 z-[70] hidden items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="viewInmateTitle">
  <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200" data-close="viewModal"></div>
  <div class="modal-panel relative mx-4 w-full max-w-xl origin-center rounded-2xl bg-white shadow-xl opacity-0 scale-95 translate-y-2 transition-all duration-200">
    <div class="flex items-center justify-between border-b px-5 py-4">
      <h3 id="viewInmateTitle" class="text-lg font-semibold text-slate-900">Inmate Details</h3>
      <button class="p-2 rounded-md text-slate-500 hover:bg-slate-100" data-close="viewModal" aria-label="Close">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
      <div class="flex justify-center mb-4">
        <img id="view-photo" src="" alt="Inmate Photo" class="h-24 w-24 rounded-full object-cover ring-2 ring-slate-200">
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-2 text-slate-700">
        <p><span class="font-medium">First Name:</span> <span id="view-first-name"></span></p>
        <p><span class="font-medium">Last Name:</span> <span id="view-last-name"></span></p>
        <p><span class="font-medium">Birthdate:</span> <span id="view-birthdate"></span></p>
        <p><span class="font-medium">Gender:</span> <span id="view-gender"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Crime:</span> <span id="view-crime"></span></p>
        <p><span class="font-medium">Sentence Years:</span> <span id="view-sentence"></span></p>
        <p><span class="font-medium">Court Details:</span> <span id="view-court"></span></p>
        <p><span class="font-medium">Cell Block:</span> <span id="view-cell"></span></p>
        <p><span class="font-medium">Admission Date:</span> <span id="view-admission"></span></p>
        <p><span class="font-medium">Release Date:</span> <span id="view-release"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Status:</span> <span id="view-status"></span></p>
      </div>
      <div class="mt-5 flex justify-end">
        <button type="button" data-close="viewModal"
          class="rounded-lg bg-slate-200 px-4 py-2 font-medium text-slate-900 hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300">Close</button>
      </div>
    </div>
  </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal fixed inset-0 z-[70] hidden items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="editInmateTitle">
  <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200" data-close="editModal"></div>
  <div class="modal-panel relative mx-4 w-full max-w-2xl origin-center rounded-2xl bg-white shadow-xl opacity-0 scale-95 translate-y-2 transition-all duration-200">
    <div class="flex items-center justify-between border-b px-5 py-4">
      <h3 id="editInmateTitle" class="text-lg font-semibold text-slate-900">Edit Inmate</h3>
      <button class="p-2 rounded-md text-slate-500 hover:bg-slate-100" data-close="editModal" aria-label="Close">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
      <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="edit_inmate_id" id="edit-inmate-id">
        <input type="hidden" name="existing_photo" id="existing-photo">
        <input type="text" name="edit_first_name" id="edit-first-name" placeholder="First Name" required data-autofocus class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_last_name" id="edit-last-name" placeholder="Last Name" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="date" name="edit_birthdate" id="edit-birthdate" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <select name="edit_gender" id="edit-gender" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="Male">Male</option><option value="Female">Female</option>
        </select>
        <textarea name="edit_crime" id="edit-crime" placeholder="Crime" required class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        <input type="number" name="edit_sentence_years" id="edit-sentence" placeholder="Sentence Years" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_cell_block" id="edit-cell" placeholder="Cell Block" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <textarea name="edit_court_details" id="edit-court" placeholder="Court Details" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        <input type="date" name="edit_admission_date" id="edit-admission" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="date" name="edit_release_date" id="edit-release" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <select name="edit_status" id="edit-status" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="Active">Active</option>
          <option value="Released">Released</option>
          <option value="Transferred">Transferred</option>
        </select>
        <input type="file" name="edit_photo" accept="image/*" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <div class="md:col-span-2 flex items-center justify-end gap-3 pt-2">
          <button type="button" data-close="editModal" class="rounded-lg bg-slate-200 px-4 py-2 font-medium text-slate-900 hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300">Cancel</button>
          <button type="submit" name="edit_inmate" class="rounded-lg bg-indigo-600 px-4 py-2 font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-400">Update Inmate</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================= SCRIPTS ========================= -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
  // ---------- Modal helpers (animations + accessibility)
  function openModal(id){
    const m = document.getElementById(id);
    if (!m) return;
    const overlay = m.querySelector('.modal-overlay');
    const panel   = m.querySelector('.modal-panel');

    m.classList.remove('hidden');
    m.classList.add('flex');
    document.body.classList.add('overflow-hidden');
    m.setAttribute('aria-hidden','false');

    // animate in
    requestAnimationFrame(() => {
      overlay.classList.add('opacity-100');
      panel.classList.remove('scale-95','translate-y-2','opacity-0');
      panel.classList.add('opacity-100','scale-100','translate-y-0');
    });

    // autofocus
    const af = m.querySelector('[data-autofocus]') ||
               m.querySelector('input, select, textarea, button, [href], [tabindex]:not([tabindex="-1"])');
    if (af) af.focus();
  }

  function closeModal(id){
    const m = document.getElementById(id);
    if (!m) return;
    const overlay = m.querySelector('.modal-overlay');
    const panel   = m.querySelector('.modal-panel');

    // animate out
    overlay.classList.remove('opacity-100');
    panel.classList.remove('opacity-100','scale-100','translate-y-0');
    panel.classList.add('opacity-0','scale-95','translate-y-2');

    const done = () => {
      m.classList.remove('flex');
      m.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
      m.removeEventListener('transitionend', done);
      m.setAttribute('aria-hidden','true');
    };
    // wait for panel transition
    panel.addEventListener('transitionend', done, { once: true });
  }

  // Click handlers (close via buttons or backdrop)
  document.addEventListener('click', (e) => {
    const closeId = e.target?.getAttribute?.('data-close');
    if (closeId) { closeModal(closeId); }
  });

  // ESC to close
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
      const open = document.querySelector('.modal.flex');
      if (open) closeModal(open.id);
    }
  });

  // Triggers
  document.getElementById('addBtn')?.addEventListener('click', () => openModal('addModal'));
  document.getElementById('assignMedicalBtn')?.addEventListener('click', () => openModal('addMedicalModal'));

  // View modal fill
document.querySelectorAll('.viewBtn').forEach(btn => {
  btn.addEventListener('click', () => {
    const g = (k) => btn.getAttribute(k) || '';
    const photo = g('data-photo');
    document.getElementById('view-photo').src = photo ? photo : 'https://via.placeholder.com/96x96?text=No+Photo';
    document.getElementById('view-first-name').textContent = g('data-first-name');
    document.getElementById('view-last-name').textContent  = g('data-last-name');
    document.getElementById('view-birthdate').textContent  = g('data-birthdate');
    document.getElementById('view-gender').textContent     = g('data-gender');
    document.getElementById('view-crime').textContent      = g('data-crime');
    document.getElementById('view-sentence').textContent   = g('data-sentence');
    document.getElementById('view-court').textContent      = g('data-court');
    document.getElementById('view-cell').textContent       = g('data-cell');
    document.getElementById('view-admission').textContent  = g('data-admission');
    document.getElementById('view-release').textContent    = g('data-release');
    document.getElementById('view-status').textContent     = g('data-status');
    openModal('viewModal');
  });
});


  // Edit modal fill
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      const g = (k) => btn.getAttribute(k) || '';
      document.getElementById('edit-inmate-id').value = g('data-id');
      document.getElementById('existing-photo').value = g('data-photo');
      document.getElementById('edit-first-name').value = g('data-first-name');
      document.getElementById('edit-last-name').value  = g('data-last-name');
      document.getElementById('edit-birthdate').value  = g('data-birthdate');
      document.getElementById('edit-gender').value     = g('data-gender');
      document.getElementById('edit-crime').value      = g('data-crime');
      document.getElementById('edit-sentence').value   = g('data-sentence');
      document.getElementById('edit-court').value      = g('data-court');
      document.getElementById('edit-cell').value       = g('data-cell');
      document.getElementById('edit-admission').value  = g('data-admission');
      document.getElementById('edit-release').value    = g('data-release');
      document.getElementById('edit-status').value     = g('data-status');
      openModal('editModal');
    });
  });

  // ---------- Chart.js
  const ctx = document.getElementById('statusChart');
  if (ctx) {
    new Chart(ctx, {
      type: 'pie',
      data: {
        labels: ['Active', 'Released', 'Transferred'],
        datasets: [{
          data: [<?= (int)$active_inmates ?>, <?= (int)$released_inmates ?>, <?= (int)$transferred_inmates ?>],
          backgroundColor: [
            'rgba(34, 197, 94, 0.6)',   // emerald
            'rgba(251, 146, 60, 0.6)',  // orange
            'rgba(147, 51, 234, 0.6)'   // purple
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
        maintainAspectRatio: false,
        plugins: { legend: { position: 'top' } }
      }
    });
  }

  // =============================
  // SIMPLE NEXT/PREV PAGINATION
  // =============================
  const rowsPerPage = 10; // number of rows per page
  const tableBody = document.querySelector("table tbody");
  const rows = Array.from(tableBody.querySelectorAll("tr"));
  const pagination = document.getElementById("pagination");

  let currentPage = 1;
  const totalPages = Math.ceil(rows.length / rowsPerPage);

  function displayPage(page) {
    // Ensure page stays within range
    if (page < 1) page = 1;
    if (page > totalPages) page = totalPages;

    currentPage = page;

    // Hide all rows
    rows.forEach(row => row.style.display = "none");

    // Show selected page
    const start = (page - 1) * rowsPerPage;
    const end = start + rowsPerPage;
    rows.slice(start, end).forEach(row => row.style.display = "");

    updatePagination();
  }

  function updatePagination() {
    pagination.innerHTML = `
      <button id="prevBtn" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50" ${currentPage === 1 ? "disabled" : ""}>
        ← Previous
      </button>
      <span class="text-gray-700 text-sm">Page ${currentPage} of ${totalPages}</span>
      <button id="nextBtn" class="px-3 py-1 bg-gray-200 rounded hover:bg-gray-300 disabled:opacity-50" ${currentPage === totalPages ? "disabled" : ""}>
        Next →
      </button>
    `;

    document.getElementById("prevBtn").addEventListener("click", () => displayPage(currentPage - 1));
    document.getElementById("nextBtn").addEventListener("click", () => displayPage(currentPage + 1));
  }

  // Initialize pagination on page load
  if (rows.length > 0) displayPage(1);

</script>

<?php include '../../partials/footer.php'; ?>













