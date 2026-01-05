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
                          data-status="<?= htmlspecialchars($row['status']) ?>"
                          data-place-of-birth="<?= htmlspecialchars($row['place_of_birth'] ?? '') ?>"
                          data-marital-status="<?= htmlspecialchars($row['marital_status'] ?? '') ?>"
                          data-height="<?= htmlspecialchars($row['height'] ?? '') ?>"
                          data-weight="<?= htmlspecialchars($row['weight'] ?? '') ?>"
                          data-hair="<?= htmlspecialchars($row['hair_description'] ?? '') ?>"
                          data-complexion="<?= htmlspecialchars($row['complexion'] ?? '') ?>"
                          data-eyes="<?= htmlspecialchars($row['eyes_description'] ?? '') ?>"
                          data-citizenship="<?= htmlspecialchars($row['citizenship'] ?? '') ?>"
                          data-religion="<?= htmlspecialchars($row['religion'] ?? '') ?>"
                          data-race="<?= htmlspecialchars($row['race'] ?? '') ?>"
                          data-occupation="<?= htmlspecialchars($row['occupation'] ?? '') ?>"
                          data-educational="<?= htmlspecialchars($row['educational_attainment'] ?? '') ?>"
                          data-course="<?= htmlspecialchars($row['course'] ?? '') ?>"
                          data-school="<?= htmlspecialchars($row['school_attended'] ?? '') ?>"
                          data-permanent-address="<?= htmlspecialchars($row['permanent_address'] ?? '') ?>"
                          data-provincial-address="<?= htmlspecialchars($row['provincial_address'] ?? '') ?>"
                          data-children="<?= htmlspecialchars($row['no_of_children'] ?? '') ?>"
                          data-father-name="<?= htmlspecialchars($row['father_name'] ?? '') ?>"
                          data-father-address="<?= htmlspecialchars($row['father_address'] ?? '') ?>"
                          data-mother-name="<?= htmlspecialchars($row['mother_name'] ?? '') ?>"
                          data-mother-address="<?= htmlspecialchars($row['mother_address'] ?? '') ?>"
                          data-wife-name="<?= htmlspecialchars($row['wife_clw_name'] ?? '') ?>"
                          data-wife-address="<?= htmlspecialchars($row['wife_clw_address'] ?? '') ?>"
                          data-relative-name="<?= htmlspecialchars($row['relative_name'] ?? '') ?>"
                          data-relative-address="<?= htmlspecialchars($row['relative_address'] ?? '') ?>"
                          data-contact-number="<?= htmlspecialchars($row['contact_number'] ?? '') ?>"
                          data-return-rate="<?= htmlspecialchars($row['return_rate'] ?? '') ?>"
                          data-date-time-received="<?= htmlspecialchars($row['date_time_received'] ?? '') ?>"
                          data-turned-over-by="<?= htmlspecialchars($row['turned_over_by'] ?? '') ?>"
                          data-receiving-duty-officer="<?= htmlspecialchars($row['receiving_duty_officer'] ?? '') ?>"
                          data-offense-charged="<?= htmlspecialchars($row['offense_charged'] ?? '') ?>"
                          data-criminal-case-number="<?= htmlspecialchars($row['criminal_case_number'] ?? '') ?>"
                          data-case-court="<?= htmlspecialchars($row['case_court'] ?? '') ?>"
                          data-case-status="<?= htmlspecialchars($row['case_status'] ?? '') ?>"
                          data-prisoner-property="<?= htmlspecialchars($row['prisoner_property'] ?? '') ?>"
                          data-property-receipt-number="<?= htmlspecialchars($row['property_receipt_number'] ?? '') ?>">
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
                          data-status="<?= htmlspecialchars($row['status']) ?>"
                          data-place-of-birth="<?= htmlspecialchars($row['place_of_birth'] ?? '') ?>"
                          data-marital-status="<?= htmlspecialchars($row['marital_status'] ?? '') ?>"
                          data-height="<?= htmlspecialchars($row['height'] ?? '') ?>"
                          data-weight="<?= htmlspecialchars($row['weight'] ?? '') ?>"
                          data-hair="<?= htmlspecialchars($row['hair_description'] ?? '') ?>"
                          data-complexion="<?= htmlspecialchars($row['complexion'] ?? '') ?>"
                          data-eyes="<?= htmlspecialchars($row['eyes_description'] ?? '') ?>"
                          data-citizenship="<?= htmlspecialchars($row['citizenship'] ?? '') ?>"
                          data-religion="<?= htmlspecialchars($row['religion'] ?? '') ?>"
                          data-race="<?= htmlspecialchars($row['race'] ?? '') ?>"
                          data-occupation="<?= htmlspecialchars($row['occupation'] ?? '') ?>"
                          data-educational="<?= htmlspecialchars($row['educational_attainment'] ?? '') ?>"
                          data-course="<?= htmlspecialchars($row['course'] ?? '') ?>"
                          data-school="<?= htmlspecialchars($row['school_attended'] ?? '') ?>"
                          data-permanent-address="<?= htmlspecialchars($row['permanent_address'] ?? '') ?>"
                          data-provincial-address="<?= htmlspecialchars($row['provincial_address'] ?? '') ?>"
                          data-children="<?= htmlspecialchars($row['no_of_children'] ?? '') ?>"
                          data-father-name="<?= htmlspecialchars($row['father_name'] ?? '') ?>"
                          data-father-address="<?= htmlspecialchars($row['father_address'] ?? '') ?>"
                          data-mother-name="<?= htmlspecialchars($row['mother_name'] ?? '') ?>"
                          data-mother-address="<?= htmlspecialchars($row['mother_address'] ?? '') ?>"
                          data-wife-name="<?= htmlspecialchars($row['wife_clw_name'] ?? '') ?>"
                          data-wife-address="<?= htmlspecialchars($row['wife_clw_address'] ?? '') ?>"
                          data-relative-name="<?= htmlspecialchars($row['relative_name'] ?? '') ?>"
                          data-relative-address="<?= htmlspecialchars($row['relative_address'] ?? '') ?>"
                          data-contact-number="<?= htmlspecialchars($row['contact_number'] ?? '') ?>"
                          data-return-rate="<?= htmlspecialchars($row['return_rate'] ?? '') ?>"
                          data-date-time-received="<?= htmlspecialchars($row['date_time_received'] ?? '') ?>"
                          data-turned-over-by="<?= htmlspecialchars($row['turned_over_by'] ?? '') ?>"
                          data-receiving-duty-officer="<?= htmlspecialchars($row['receiving_duty_officer'] ?? '') ?>"
                          data-offense-charged="<?= htmlspecialchars($row['offense_charged'] ?? '') ?>"
                          data-criminal-case-number="<?= htmlspecialchars($row['criminal_case_number'] ?? '') ?>"
                          data-case-court="<?= htmlspecialchars($row['case_court'] ?? '') ?>"
                          data-case-status="<?= htmlspecialchars($row['case_status'] ?? '') ?>"
                          data-prisoner-property="<?= htmlspecialchars($row['prisoner_property'] ?? '') ?>"
                          data-property-receipt-number="<?= htmlspecialchars($row['property_receipt_number'] ?? '') ?>">
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
        <!-- Basic Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Basic Information</h4>
        <input type="text" name="first_name" placeholder="First Name" required data-autofocus class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="last_name" placeholder="Last Name" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="date" name="birthdate" placeholder="Birthdate" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="place_of_birth" placeholder="Place of Birth" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <select name="gender" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
          <option value="">Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
        <select name="marital_status" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
          <option value="">Select Marital Status</option>
          <option value="Single">Single</option>
          <option value="Married">Married</option>
          <option value="Divorced">Divorced</option>
          <option value="Widowed">Widowed</option>
          <option value="Common-law">Common-law</option>
        </select>

        <!-- Physical Description -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Physical Description</h4>
        <input type="text" name="height" placeholder="Height (e.g., 5'10&quot;)" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="weight" placeholder="Weight (e.g., 180 lbs)" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="hair_description" placeholder="Hair" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="complexion" placeholder="Complexion" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="eyes_description" placeholder="Eyes" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

        <!-- Demographic Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Demographic Information</h4>
        <input type="text" name="citizenship" placeholder="Citizenship" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="race" placeholder="Race/Ethnicity" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="religion" placeholder="Religion" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="occupation" placeholder="Occupation" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

        <!-- Education -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Education</h4>
        <select name="educational_attainment" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
          <option value="">Select Educational Attainment</option>
          <option value="Elementary">Elementary</option>
          <option value="High School">High School</option>
          <option value="Vocational">Vocational</option>
          <option value="College">College</option>
          <option value="Graduate">Graduate</option>
        </select>
        <input type="text" name="course" placeholder="Course/Major" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="school_attended" placeholder="School/University" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

        <!-- Address Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Address Information</h4>
        <textarea name="permanent_address" placeholder="Permanent Address" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
        <textarea name="provincial_address" placeholder="Provincial Address" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>

        <!-- Family/Emergency Contacts -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Family Information</h4>
        <input type="text" name="no_of_children" placeholder="Number of Children" type="number" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <div class="md:col-span-2"></div>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Father</h5>
        <input type="text" name="father_name" placeholder="Father's Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <textarea name="father_address" placeholder="Father's Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Mother</h5>
        <input type="text" name="mother_name" placeholder="Mother's Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <textarea name="mother_address" placeholder="Mother's Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Wife/Common-Law Wife</h5>
        <input type="text" name="wife_clw_name" placeholder="Wife/CLW Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <textarea name="wife_clw_address" placeholder="Wife/CLW Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Emergency Contact (Relative)</h5>
        <input type="text" name="relative_name" placeholder="Relative Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <textarea name="relative_address" placeholder="Relative Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>

        <!-- Crime & Sentencing -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Crime & Sentencing</h4>
        <textarea name="crime" placeholder="Crime" required class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
        <input type="number" name="sentence_years" id="sentence_years" placeholder="Sentence Years" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <textarea name="court_details" placeholder="Court Details" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>

        <!-- Legal Case Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Legal Case Information</h4>
        <textarea name="offense_charged" placeholder="Offense/s Charged" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
        <input type="text" name="criminal_case_number" placeholder="Criminal Case No./s" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="case_court" placeholder="Court" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <select name="case_status" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
          <option value="">Select Case Status</option>
          <option value="Pending">Pending</option>
          <option value="Ongoing">Ongoing</option>
          <option value="Resolved">Resolved</option>
          <option value="Closed">Closed</option>
          <option value="Dismissed">Dismissed</option>
        </select>

        <!-- Incarceration Details -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Incarceration Details</h4>
        <input type="text" name="cell_block" placeholder="Cell Block" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="date" name="admission_date" id="admission_date" placeholder="Admission Date" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="date" name="release_date" id="release_date" placeholder="Release Date" readonly class="rounded-lg border border-slate-300 px-3 py-2 bg-slate-50 cursor-not-allowed focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <select name="status" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
          <option value="">Select Status</option>
          <option value="Active">Active</option>
          <option value="Released">Released</option>
          <option value="Transferred">Transferred</option>
        </select>

        <!-- Contact & Receiving Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Contact & Receiving Information</h4>
        <input type="text" name="contact_number" placeholder="Contact Number" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="return_rate" placeholder="Return Rate" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="datetime-local" name="date_time_received" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="turned_over_by" placeholder="Turned Over by" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">
        <input type="text" name="receiving_duty_officer" placeholder="Receiving Duty Officer" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

        <!-- Prisoner's Property -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Prisoner's Property</h4>
        <textarea name="prisoner_property" placeholder="Prisoner's Property" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500"></textarea>
        <input type="text" name="property_receipt_number" placeholder="Property Receipt No." class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

        <!-- Photo -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Photo</h4>
        <input type="file" name="photo" accept="image/*" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-brand-500 focus:border-brand-500">

        <div class="md:col-span-2 flex items-center justify-end gap-3 pt-4">
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
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-3 text-slate-700 text-sm">
        <!-- Basic Information -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-2">Basic Information</h4>
        <p><span class="font-medium">First Name:</span> <span id="view-first-name"></span></p>
        <p><span class="font-medium">Last Name:</span> <span id="view-last-name"></span></p>
        <p><span class="font-medium">Birthdate:</span> <span id="view-birthdate"></span></p>
        <p><span class="font-medium">Place of Birth:</span> <span id="view-place-of-birth"></span></p>
        <p><span class="font-medium">Gender:</span> <span id="view-gender"></span></p>
        <p><span class="font-medium">Marital Status:</span> <span id="view-marital-status"></span></p>

        <!-- Physical Description -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Physical Description</h4>
        <p><span class="font-medium">Height:</span> <span id="view-height"></span></p>
        <p><span class="font-medium">Weight:</span> <span id="view-weight"></span></p>
        <p><span class="font-medium">Hair:</span> <span id="view-hair"></span></p>
        <p><span class="font-medium">Complexion:</span> <span id="view-complexion"></span></p>
        <p><span class="font-medium">Eyes:</span> <span id="view-eyes"></span></p>

        <!-- Demographic Information -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Demographic Information</h4>
        <p><span class="font-medium">Citizenship:</span> <span id="view-citizenship"></span></p>
        <p><span class="font-medium">Race:</span> <span id="view-race"></span></p>
        <p><span class="font-medium">Religion:</span> <span id="view-religion"></span></p>
        <p><span class="font-medium">Occupation:</span> <span id="view-occupation"></span></p>

        <!-- Education -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Education</h4>
        <p><span class="font-medium">Educational Attainment:</span> <span id="view-educational"></span></p>
        <p><span class="font-medium">Course:</span> <span id="view-course"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">School Attended:</span> <span id="view-school"></span></p>

        <!-- Address Information -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Address Information</h4>
        <p class="sm:col-span-2"><span class="font-medium">Permanent Address:</span> <span id="view-permanent-address" class="block text-xs mt-1"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Provincial Address:</span> <span id="view-provincial-address" class="block text-xs mt-1"></span></p>

        <!-- Family Information -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Family Information</h4>
        <p><span class="font-medium">No. of Children:</span> <span id="view-children"></span></p>

        <p class="sm:col-span-2 text-xs font-semibold text-slate-600 mt-2">Father</p>
        <p class="sm:col-span-2"><span class="font-medium">Name:</span> <span id="view-father-name"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Address:</span> <span id="view-father-address" class="block text-xs mt-1"></span></p>

        <p class="sm:col-span-2 text-xs font-semibold text-slate-600 mt-2">Mother</p>
        <p class="sm:col-span-2"><span class="font-medium">Name:</span> <span id="view-mother-name"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Address:</span> <span id="view-mother-address" class="block text-xs mt-1"></span></p>

        <p class="sm:col-span-2 text-xs font-semibold text-slate-600 mt-2">Wife/Common-Law Wife</p>
        <p class="sm:col-span-2"><span class="font-medium">Name:</span> <span id="view-wife-name"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Address:</span> <span id="view-wife-address" class="block text-xs mt-1"></span></p>

        <p class="sm:col-span-2 text-xs font-semibold text-slate-600 mt-2">Emergency Contact (Relative)</p>
        <p class="sm:col-span-2"><span class="font-medium">Name:</span> <span id="view-relative-name"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Address:</span> <span id="view-relative-address" class="block text-xs mt-1"></span></p>

        <!-- Crime & Sentencing -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Crime & Sentencing</h4>
        <p class="sm:col-span-2"><span class="font-medium">Crime:</span> <span id="view-crime" class="block mt-1"></span></p>
        <p><span class="font-medium">Sentence Years:</span> <span id="view-sentence"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Court Details:</span> <span id="view-court" class="block mt-1"></span></p>

        <!-- Legal Case Information -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Legal Case Information</h4>
        <p class="sm:col-span-2"><span class="font-medium">Offense/s Charged:</span> <span id="view-offense-charged" class="block mt-1"></span></p>
        <p><span class="font-medium">Criminal Case No./s:</span> <span id="view-criminal-case-number"></span></p>
        <p><span class="font-medium">Court:</span> <span id="view-case-court"></span></p>
        <p><span class="font-medium">Case Status:</span> <span id="view-case-status"></span></p>

        <!-- Incarceration Details -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Incarceration Details</h4>
        <p><span class="font-medium">Cell Block:</span> <span id="view-cell"></span></p>
        <p><span class="font-medium">Admission Date:</span> <span id="view-admission"></span></p>
        <p><span class="font-medium">Release Date:</span> <span id="view-release"></span></p>
        <p><span class="font-medium">Status:</span> <span id="view-status"></span></p>

        <!-- Contact & Receiving Information -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Contact & Receiving Information</h4>
        <p><span class="font-medium">Contact Number:</span> <span id="view-contact-number"></span></p>
        <p><span class="font-medium">Return Rate:</span> <span id="view-return-rate"></span></p>
        <p><span class="font-medium">Date/Time Received:</span> <span id="view-date-time-received"></span></p>
        <p><span class="font-medium">Turned Over by:</span> <span id="view-turned-over-by"></span></p>
        <p class="sm:col-span-2"><span class="font-medium">Receiving Duty Officer:</span> <span id="view-receiving-duty-officer"></span></p>

        <!-- Prisoner's Property -->
        <h4 class="sm:col-span-2 text-sm font-semibold text-slate-800 mt-3">Prisoner's Property</h4>
        <p class="sm:col-span-2"><span class="font-medium">Property:</span> <span id="view-prisoner-property" class="block mt-1"></span></p>
        <p><span class="font-medium">Property Receipt No.:</span> <span id="view-property-receipt-number"></span></p>
      </div>
      <div class="mt-6 flex justify-end">
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

        <!-- Basic Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Basic Information</h4>
        <input type="text" name="edit_first_name" id="edit-first-name" placeholder="First Name" required data-autofocus class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_last_name" id="edit-last-name" placeholder="Last Name" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="date" name="edit_birthdate" id="edit-birthdate" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_place_of_birth" id="edit-place-of-birth" placeholder="Place of Birth" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <select name="edit_gender" id="edit-gender" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
        </select>
        <select name="edit_marital_status" id="edit-marital-status" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">Select Marital Status</option>
          <option value="Single">Single</option>
          <option value="Married">Married</option>
          <option value="Divorced">Divorced</option>
          <option value="Widowed">Widowed</option>
          <option value="Common-law">Common-law</option>
        </select>

        <!-- Physical Description -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Physical Description</h4>
        <input type="text" name="edit_height" id="edit-height" placeholder="Height (e.g., 5'10&quot;)" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_weight" id="edit-weight" placeholder="Weight (e.g., 180 lbs)" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_hair_description" id="edit-hair-description" placeholder="Hair" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_complexion" id="edit-complexion" placeholder="Complexion" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_eyes_description" id="edit-eyes-description" placeholder="Eyes" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

        <!-- Demographic Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Demographic Information</h4>
        <input type="text" name="edit_citizenship" id="edit-citizenship" placeholder="Citizenship" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_race" id="edit-race" placeholder="Race/Ethnicity" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_religion" id="edit-religion" placeholder="Religion" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_occupation" id="edit-occupation" placeholder="Occupation" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

        <!-- Education -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Education</h4>
        <select name="edit_educational_attainment" id="edit-educational-attainment" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">Select Educational Attainment</option>
          <option value="Elementary">Elementary</option>
          <option value="High School">High School</option>
          <option value="Vocational">Vocational</option>
          <option value="College">College</option>
          <option value="Graduate">Graduate</option>
        </select>
        <input type="text" name="edit_course" id="edit-course" placeholder="Course/Major" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_school_attended" id="edit-school-attended" placeholder="School/University" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

        <!-- Address Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Address Information</h4>
        <textarea name="edit_permanent_address" id="edit-permanent-address" placeholder="Permanent Address" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        <textarea name="edit_provincial_address" id="edit-provincial-address" placeholder="Provincial Address" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>

        <!-- Family/Emergency Contacts -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Family Information</h4>
        <input type="number" name="edit_no_of_children" id="edit-no-of-children" placeholder="Number of Children" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <div class="md:col-span-2"></div>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Father</h5>
        <input type="text" name="edit_father_name" id="edit-father-name" placeholder="Father's Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <textarea name="edit_father_address" id="edit-father-address" placeholder="Father's Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Mother</h5>
        <input type="text" name="edit_mother_name" id="edit-mother-name" placeholder="Mother's Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <textarea name="edit_mother_address" id="edit-mother-address" placeholder="Mother's Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Wife/Common-Law Wife</h5>
        <input type="text" name="edit_wife_clw_name" id="edit-wife-clw-name" placeholder="Wife/CLW Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <textarea name="edit_wife_clw_address" id="edit-wife-clw-address" placeholder="Wife/CLW Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>

        <h5 class="md:col-span-2 text-xs font-semibold text-slate-600 mt-2">Emergency Contact (Relative)</h5>
        <input type="text" name="edit_relative_name" id="edit-relative-name" placeholder="Relative Name" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <textarea name="edit_relative_address" id="edit-relative-address" placeholder="Relative Address" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>

        <!-- Crime & Sentencing -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Crime & Sentencing</h4>
        <textarea name="edit_crime" id="edit-crime" placeholder="Crime" required class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        <input type="number" name="edit_sentence_years" id="edit-sentence" placeholder="Sentence Years" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <textarea name="edit_court_details" id="edit-court" placeholder="Court Details" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>

        <!-- Legal Case Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Legal Case Information</h4>
        <textarea name="edit_offense_charged" id="edit-offense-charged" placeholder="Offense/s Charged" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        <input type="text" name="edit_criminal_case_number" id="edit-criminal-case-number" placeholder="Criminal Case No./s" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_case_court" id="edit-case-court" placeholder="Court" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <select name="edit_case_status" id="edit-case-status" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">Select Case Status</option>
          <option value="Pending">Pending</option>
          <option value="Ongoing">Ongoing</option>
          <option value="Resolved">Resolved</option>
          <option value="Closed">Closed</option>
          <option value="Dismissed">Dismissed</option>
        </select>

        <!-- Incarceration Details -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Incarceration Details</h4>
        <input type="text" name="edit_cell_block" id="edit-cell" placeholder="Cell Block" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="date" name="edit_admission_date" id="edit-admission" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="date" name="edit_release_date" id="edit-release" readonly class="rounded-lg border border-slate-300 px-3 py-2 bg-slate-50 cursor-not-allowed focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <select name="edit_status" id="edit-status" required class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
          <option value="">Select Status</option>
          <option value="Active">Active</option>
          <option value="Released">Released</option>
          <option value="Transferred">Transferred</option>
        </select>

        <!-- Contact & Receiving Information -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Contact & Receiving Information</h4>
        <input type="text" name="edit_contact_number" id="edit-contact-number" placeholder="Contact Number" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_return_rate" id="edit-return-rate" placeholder="Return Rate" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="datetime-local" name="edit_date_time_received" id="edit-date-time-received" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_turned_over_by" id="edit-turned-over-by" placeholder="Turned Over by" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">
        <input type="text" name="edit_receiving_duty_officer" id="edit-receiving-duty-officer" placeholder="Receiving Duty Officer" class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

        <!-- Prisoner's Property -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Prisoner's Property</h4>
        <textarea name="edit_prisoner_property" id="edit-prisoner-property" placeholder="Prisoner's Property" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500"></textarea>
        <input type="text" name="edit_property_receipt_number" id="edit-property-receipt-number" placeholder="Property Receipt No." class="rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

        <!-- Photo -->
        <h4 class="md:col-span-2 text-sm font-semibold text-slate-700 mt-3">Photo</h4>
        <input type="file" name="edit_photo" accept="image/*" class="md:col-span-2 rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500">

        <div class="md:col-span-2 flex items-center justify-end gap-3 pt-4">
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
    document.getElementById('view-place-of-birth').textContent = g('data-place-of-birth');
    document.getElementById('view-gender').textContent     = g('data-gender');
    document.getElementById('view-marital-status').textContent = g('data-marital-status');
    document.getElementById('view-height').textContent = g('data-height');
    document.getElementById('view-weight').textContent = g('data-weight');
    document.getElementById('view-hair').textContent = g('data-hair');
    document.getElementById('view-complexion').textContent = g('data-complexion');
    document.getElementById('view-eyes').textContent = g('data-eyes');
    document.getElementById('view-citizenship').textContent = g('data-citizenship');
    document.getElementById('view-religion').textContent = g('data-religion');
    document.getElementById('view-race').textContent = g('data-race');
    document.getElementById('view-occupation').textContent = g('data-occupation');
    document.getElementById('view-educational').textContent = g('data-educational');
    document.getElementById('view-course').textContent = g('data-course');
    document.getElementById('view-school').textContent = g('data-school');
    document.getElementById('view-permanent-address').textContent = g('data-permanent-address');
    document.getElementById('view-provincial-address').textContent = g('data-provincial-address');
    document.getElementById('view-children').textContent = g('data-children');
    document.getElementById('view-father-name').textContent = g('data-father-name');
    document.getElementById('view-father-address').textContent = g('data-father-address');
    document.getElementById('view-mother-name').textContent = g('data-mother-name');
    document.getElementById('view-mother-address').textContent = g('data-mother-address');
    document.getElementById('view-wife-name').textContent = g('data-wife-name');
    document.getElementById('view-wife-address').textContent = g('data-wife-address');
    document.getElementById('view-relative-name').textContent = g('data-relative-name');
    document.getElementById('view-relative-address').textContent = g('data-relative-address');
    document.getElementById('view-crime').textContent      = g('data-crime');
    document.getElementById('view-sentence').textContent   = g('data-sentence');
    document.getElementById('view-court').textContent      = g('data-court');
    document.getElementById('view-offense-charged').textContent = g('data-offense-charged');
    document.getElementById('view-criminal-case-number').textContent = g('data-criminal-case-number');
    document.getElementById('view-case-court').textContent = g('data-case-court');
    document.getElementById('view-case-status').textContent = g('data-case-status');
    document.getElementById('view-cell').textContent       = g('data-cell');
    document.getElementById('view-admission').textContent  = g('data-admission');
    document.getElementById('view-release').textContent    = g('data-release');
    document.getElementById('view-status').textContent     = g('data-status');
    document.getElementById('view-contact-number').textContent = g('data-contact-number');
    document.getElementById('view-return-rate').textContent = g('data-return-rate');
    document.getElementById('view-date-time-received').textContent = g('data-date-time-received');
    document.getElementById('view-turned-over-by').textContent = g('data-turned-over-by');
    document.getElementById('view-receiving-duty-officer').textContent = g('data-receiving-duty-officer');
    document.getElementById('view-prisoner-property').textContent = g('data-prisoner-property');
    document.getElementById('view-property-receipt-number').textContent = g('data-property-receipt-number');
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
      document.getElementById('edit-place-of-birth').value = g('data-place-of-birth');
      document.getElementById('edit-gender').value     = g('data-gender');
      document.getElementById('edit-marital-status').value = g('data-marital-status');
      document.getElementById('edit-height').value = g('data-height');
      document.getElementById('edit-weight').value = g('data-weight');
      document.getElementById('edit-hair-description').value = g('data-hair');
      document.getElementById('edit-complexion').value = g('data-complexion');
      document.getElementById('edit-eyes-description').value = g('data-eyes');
      document.getElementById('edit-citizenship').value = g('data-citizenship');
      document.getElementById('edit-religion').value = g('data-religion');
      document.getElementById('edit-race').value = g('data-race');
      document.getElementById('edit-occupation').value = g('data-occupation');
      document.getElementById('edit-educational-attainment').value = g('data-educational');
      document.getElementById('edit-course').value = g('data-course');
      document.getElementById('edit-school-attended').value = g('data-school');
      document.getElementById('edit-permanent-address').value = g('data-permanent-address');
      document.getElementById('edit-provincial-address').value = g('data-provincial-address');
      document.getElementById('edit-no-of-children').value = g('data-children');
      document.getElementById('edit-father-name').value = g('data-father-name');
      document.getElementById('edit-father-address').value = g('data-father-address');
      document.getElementById('edit-mother-name').value = g('data-mother-name');
      document.getElementById('edit-mother-address').value = g('data-mother-address');
      document.getElementById('edit-wife-clw-name').value = g('data-wife-name');
      document.getElementById('edit-wife-clw-address').value = g('data-wife-address');
      document.getElementById('edit-relative-name').value = g('data-relative-name');
      document.getElementById('edit-relative-address').value = g('data-relative-address');
      document.getElementById('edit-crime').value      = g('data-crime');
      document.getElementById('edit-sentence').value   = g('data-sentence');
      document.getElementById('edit-court').value      = g('data-court');
      document.getElementById('edit-offense-charged').value = g('data-offense-charged');
      document.getElementById('edit-criminal-case-number').value = g('data-criminal-case-number');
      document.getElementById('edit-case-court').value = g('data-case-court');
      document.getElementById('edit-case-status').value = g('data-case-status');
      document.getElementById('edit-cell').value       = g('data-cell');
      document.getElementById('edit-admission').value  = g('data-admission');
      document.getElementById('edit-release').value    = g('data-release');
      document.getElementById('edit-status').value     = g('data-status');
      document.getElementById('edit-contact-number').value = g('data-contact-number');
      document.getElementById('edit-return-rate').value = g('data-return-rate');
      document.getElementById('edit-date-time-received').value = g('data-date-time-received');
      document.getElementById('edit-turned-over-by').value = g('data-turned-over-by');
      document.getElementById('edit-receiving-duty-officer').value = g('data-receiving-duty-officer');
      document.getElementById('edit-prisoner-property').value = g('data-prisoner-property');
      document.getElementById('edit-property-receipt-number').value = g('data-property-receipt-number');
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

  // =============================
  // AUTO-CALCULATE RELEASE DATE
  // =============================
  function calculateReleaseDate(admissionDate, sentenceYears) {
    if (!sentenceYears) return '';
    const admission = admissionDate ? new Date(admissionDate) : new Date();
    admission.setFullYear(admission.getFullYear() + parseInt(sentenceYears));
    return admission.toISOString().split('T')[0];
  }

  function updateReleaseDate(admissionFieldId, sentenceFieldId, releaseFieldId) {
    const admissionField = document.getElementById(admissionFieldId);
    const sentenceField = document.getElementById(sentenceFieldId);
    const releaseField = document.getElementById(releaseFieldId);

    if (admissionField && sentenceField && releaseField) {
      const admissionDate = admissionField.value;
      const sentenceYears = sentenceField.value;
      const releaseDate = calculateReleaseDate(admissionDate, sentenceYears);
      releaseField.value = releaseDate;
      console.log('Updated release date:', releaseDate); // Debug log
    }
  }

  // Add event listeners for add modal
  document.getElementById('admission_date')?.addEventListener('change', () => updateReleaseDate('admission_date', 'sentence_years', 'release_date'));
  document.getElementById('sentence_years')?.addEventListener('input', () => updateReleaseDate('admission_date', 'sentence_years', 'release_date'));
  document.getElementById('sentence_years')?.addEventListener('change', () => updateReleaseDate('admission_date', 'sentence_years', 'release_date'));

  // Add event listeners for edit modal
  document.getElementById('edit-admission')?.addEventListener('change', () => updateReleaseDate('edit-admission', 'edit-sentence', 'edit-release'));
  document.getElementById('edit-sentence')?.addEventListener('input', () => updateReleaseDate('edit-admission', 'edit-sentence', 'edit-release'));
  document.getElementById('edit-sentence')?.addEventListener('change', () => updateReleaseDate('edit-admission', 'edit-sentence', 'edit-release'));

</script>

<?php include '../../partials/footer.php'; ?>
