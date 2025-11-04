<?php
require '../../includes/session_check.php';
require '../../config/config.php';
include 'backend/medical_backend.php';
include '../../partials/header.php';
include '../../partials/sidebar.php';

// Fetch medical records assigned to the logged-in medical staff
$sql = "SELECT mr.*, i.first_name, i.last_name
        FROM medical_records mr
        JOIN inmates i ON mr.inmate_id = i.inmate_id
        WHERE mr.staff_id = " . $_SESSION['user_id'] . "
        ORDER BY mr.record_date DESC";
$result = $conn->query($sql);
?>

<div class="container mx-auto px-6 py-10 md:ml-64">
    <div class="flex items-center justify-between mb-8">
        <h2 class="text-4xl font-extrabold text-gray-800 tracking-tight">Assigned Inmate Medical Records</h2>
    </div>

    <?php if ($result->num_rows > 0): ?>
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
            <?php while($row = $result->fetch_assoc()): ?>
                <div class="bg-gradient-to-br from-white to-gray-50 border border-gray-200 hover:shadow-2xl shadow-md rounded-2xl p-6 transition-all duration-300 transform hover:-translate-y-1 hover:scale-105">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-2xl font-semibold text-gray-900">
                                <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?>
                            </h3>
                            <p class="text-sm text-gray-500 mt-1">
                                Record Date: <?php echo htmlspecialchars(date('F j, Y', strtotime($row['record_date']))); ?>
                            </p>
                        </div>
                        <div class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded-full font-semibold uppercase">
                            <?php echo htmlspecialchars($row['visit_type']); ?>
                        </div>
                    </div>

                    <div class="space-y-3 text-gray-700">
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-red-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            <p><span class="font-semibold text-gray-800">Diagnosis:</span> <?php echo htmlspecialchars($row['diagnosis']); ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <p><span class="font-semibold text-gray-800">Treatment:</span> <?php echo htmlspecialchars($row['treatment']); ?></p>
                        </div>
                        <div class="flex items-center space-x-2">
                            <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z"/></svg>
                            <p><span class="font-semibold text-gray-800">Medication:</span> <?php echo htmlspecialchars($row['medication']); ?></p>
                        </div>

                        <div class="border-t border-gray-300 pt-4 mt-4">
                            <h4 class="text-lg font-semibold text-gray-900 mb-3 flex items-center space-x-2">
                                <svg class="w-5 h-5 text-blue-500" fill="currentColor" viewBox="0 0 20 20"><path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"/></svg>
                                <span>Vital Signs</span>
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-blue-50 p-3 rounded-lg border border-blue-100">
                                    <p><span class="font-semibold text-blue-700">BP:</span> <?php echo htmlspecialchars($row['blood_pressure']); ?></p>
                                </div>
                                <div class="bg-red-50 p-3 rounded-lg border border-red-100">
                                    <p><span class="font-semibold text-red-700">Temp:</span> <?php echo htmlspecialchars($row['temperature_c']); ?>°C</p>
                                </div>
                                <div class="bg-green-50 p-3 rounded-lg border border-green-100">
                                    <p><span class="font-semibold text-green-700">Pulse:</span> <?php echo htmlspecialchars($row['pulse_rate']); ?> bpm</p>
                                </div>
                                <div class="bg-purple-50 p-3 rounded-lg border border-purple-100">
                                    <p><span class="font-semibold text-purple-700">Resp Rate:</span> <?php echo htmlspecialchars($row['respiratory_rate']); ?> bpm</p>
                                </div>
                            </div>
                        </div>

                        <div class="border-t border-gray-300 pt-4 mt-4 space-y-2 text-sm">
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-orange-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Condition:</span> <?php echo htmlspecialchars($row['medical_condition']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-yellow-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Allergies:</span> <?php echo htmlspecialchars($row['allergies']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-indigo-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Next Checkup:</span> <?php echo htmlspecialchars($row['next_checkup_date']); ?></p>
                            </div>
                            <div class="flex items-center space-x-2">
                                <svg class="w-5 h-5 text-teal-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v8a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2H4zm3 2a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"/></svg>
                                <p><span class="font-semibold text-gray-800">Referred To:</span> <?php echo htmlspecialchars($row['hospital_referred']); ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button class="editBtn inline-flex items-center px-3 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg shadow transition"
                                data-id="<?php echo $row['record_id']; ?>"
                                data-inmate-id="<?php echo $row['inmate_id']; ?>"
                                data-visit-type="<?php echo htmlspecialchars($row['visit_type'] ?? ''); ?>"
                                data-diagnosis="<?php echo htmlspecialchars($row['diagnosis'] ?? ''); ?>"
                                data-vital-signs="<?php echo htmlspecialchars($row['vital_signs'] ?? ''); ?>"
                                data-blood-pressure="<?php echo htmlspecialchars($row['blood_pressure'] ?? ''); ?>"
                                data-temperature="<?php echo htmlspecialchars($row['temperature_c'] ?? ''); ?>"
                                data-pulse-rate="<?php echo htmlspecialchars($row['pulse_rate'] ?? ''); ?>"
                                data-respiratory-rate="<?php echo htmlspecialchars($row['respiratory_rate'] ?? ''); ?>"
                                data-treatment="<?php echo htmlspecialchars($row['treatment'] ?? ''); ?>"
                                data-medication="<?php echo htmlspecialchars($row['medication'] ?? ''); ?>"
                                data-medical-condition="<?php echo htmlspecialchars($row['medical_condition'] ?? ''); ?>"
                                data-allergies="<?php echo htmlspecialchars($row['allergies'] ?? ''); ?>"
                                data-remarks="<?php echo htmlspecialchars($row['remarks'] ?? ''); ?>"
                                data-next-checkup-date="<?php echo htmlspecialchars($row['next_checkup_date'] ?? ''); ?>"
                                data-hospital-referred="<?php echo htmlspecialchars($row['hospital_referred'] ?? ''); ?>"
                                data-attachment-path="<?php echo htmlspecialchars($row['attachment_path'] ?? ''); ?>"
                                data-existing-attachment-path="<?php echo htmlspecialchars($row['attachment_path'] ?? ''); ?>"
                                data-record-date="<?php echo $row['record_date'] ?? ''; ?>">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit
                        </button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="text-center text-gray-500 py-20 bg-gradient-to-br from-white to-gray-50 rounded-xl shadow-sm border border-gray-200">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <p class="text-xl font-medium">No medical records found.</p>
            <p class="text-sm mt-2">Start by adding a new medical record.</p>
        </div>
    <?php endif; ?>
</div>


<!-- Edit Modal -->
<div id="editModal" class="modal fixed inset-0 z-[70] hidden items-center justify-center" role="dialog" aria-modal="true" aria-labelledby="editMedicalTitle">
  <div class="modal-overlay absolute inset-0 bg-slate-900/60 backdrop-blur-sm opacity-0 transition-opacity duration-200" data-close="editModal"></div>
  <div class="modal-panel relative mx-4 w-full max-w-2xl origin-center rounded-2xl bg-white shadow-xl opacity-0 scale-95 translate-y-2 transition-all duration-200">
    <div class="flex items-center justify-between border-b px-5 py-4">
      <h3 id="editMedicalTitle" class="text-lg font-semibold text-slate-900">Edit Medical Record</h3>
      <button class="p-2 rounded-md text-slate-500 hover:bg-slate-100" data-close="editModal" aria-label="Close">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
      </button>
    </div>
    <div class="px-5 py-4 max-h-[70vh] overflow-y-auto">
      <form method="post" enctype="multipart/form-data" class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <input type="hidden" name="record_id" id="edit-record-id">
        <input type="hidden" name="inmate_id" id="edit-inmate-id">
        <div class="md:col-span-2">
          <label for="edit-visit-type" class="block text-sm font-medium text-slate-700">Visit Type</label>
          <select name="visit_type" id="edit-visit-type" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <option value="Routine Checkup">Routine Checkup</option>
            <option value="Emergency">Emergency</option>
            <option value="Follow-up">Follow-up</option>
            <option value="Consultation">Consultation</option>
          </select>
        </div>
        <div class="md:col-span-2">
          <label for="edit-diagnosis" class="block text-sm font-medium text-slate-700">Diagnosis</label>
          <textarea name="diagnosis" id="edit-diagnosis" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div class="md:col-span-2">
          <label for="edit-vital-signs" class="block text-sm font-medium text-slate-700">Vital Signs</label>
          <textarea name="vital_signs" id="edit-vital-signs" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div>
          <label for="edit-blood-pressure" class="block text-sm font-medium text-slate-700">Blood Pressure</label>
          <input type="text" name="blood_pressure" id="edit-blood-pressure" placeholder="e.g., 120/80" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label for="edit-temperature" class="block text-sm font-medium text-slate-700">Temperature (°C)</label>
          <input type="number" step="0.1" name="temperature_c" id="edit-temperature" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label for="edit-pulse-rate" class="block text-sm font-medium text-slate-700">Pulse Rate (bpm)</label>
          <input type="number" name="pulse_rate" id="edit-pulse-rate" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label for="edit-respiratory-rate" class="block text-sm font-medium text-slate-700">Respiratory Rate (bpm)</label>
          <input type="number" name="respiratory_rate" id="edit-respiratory-rate" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="md:col-span-2">
          <label for="edit-treatment" class="block text-sm font-medium text-slate-700">Treatment</label>
          <textarea name="treatment" id="edit-treatment" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div class="md:col-span-2">
          <label for="edit-medication" class="block text-sm font-medium text-slate-700">Medication</label>
          <textarea name="medication" id="edit-medication" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div class="md:col-span-2">
          <label for="edit-medical-condition" class="block text-sm font-medium text-slate-700">Medical Condition</label>
          <textarea name="medical_condition" id="edit-medical-condition" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div class="md:col-span-2">
          <label for="edit-allergies" class="block text-sm font-medium text-slate-700">Allergies</label>
          <textarea name="allergies" id="edit-allergies" rows="2" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div class="md:col-span-2">
          <label for="edit-remarks" class="block text-sm font-medium text-slate-700">Remarks</label>
          <textarea name="remarks" id="edit-remarks" rows="3" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
        </div>
        <div>
          <label for="edit-next-checkup-date" class="block text-sm font-medium text-slate-700">Next Checkup Date</label>
          <input type="date" name="next_checkup_date" id="edit-next-checkup-date" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div>
          <label for="edit-hospital-referred" class="block text-sm font-medium text-slate-700">Hospital Referred</label>
          <input type="text" name="hospital_referred" id="edit-hospital-referred" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="md:col-span-2">
          <label for="edit-attachment-path" class="block text-sm font-medium text-slate-700">Attachment</label>
          <input type="file" name="attachment_path" id="edit-attachment-path" accept="image/*,application/pdf" class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
          <input type="hidden" name="existing_attachment_path" id="edit-existing-attachment-path">
        </div>
        <div>
          <label for="edit-record-date" class="block text-sm font-medium text-slate-700">Record Date</label>
          <input type="date" name="record_date" id="edit-record-date" required class="mt-1 w-full rounded-lg border border-slate-300 px-3 py-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
        </div>
        <div class="md:col-span-2 flex items-center justify-end gap-3 pt-2">
          <button type="button" data-close="editModal" class="rounded-lg bg-slate-200 px-4 py-2 font-medium text-slate-900 hover:bg-slate-300 focus:outline-none focus:ring-2 focus:ring-slate-300">Cancel</button>
          <button type="submit" name="update_full_medical_record" class="rounded-lg bg-blue-600 px-4 py-2 font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-400">Update Record</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- ========================= SCRIPTS ========================= -->
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

  // Edit modal fill
  document.querySelectorAll('.editBtn').forEach(btn => {
    btn.addEventListener('click', () => {
      const g = (k) => btn.getAttribute(k) || '';
      document.getElementById('edit-record-id').value = g('data-id');
      document.getElementById('edit-inmate-id').value = g('data-inmate-id');
      document.getElementById('edit-visit-type').value = g('data-visit-type');
      document.getElementById('edit-diagnosis').value = g('data-diagnosis');
      document.getElementById('edit-vital-signs').value = g('data-vital-signs');
      document.getElementById('edit-blood-pressure').value = g('data-blood-pressure');
      document.getElementById('edit-temperature').value = g('data-temperature');
      document.getElementById('edit-pulse-rate').value = g('data-pulse-rate');
      document.getElementById('edit-respiratory-rate').value = g('data-respiratory-rate');
      document.getElementById('edit-treatment').value = g('data-treatment');
      document.getElementById('edit-medication').value = g('data-medication');
      document.getElementById('edit-medical-condition').value = g('data-medical-condition');
      document.getElementById('edit-allergies').value = g('data-allergies');
      document.getElementById('edit-remarks').value = g('data-remarks');
      document.getElementById('edit-next-checkup-date').value = g('data-next-checkup-date');
      document.getElementById('edit-hospital-referred').value = g('data-hospital-referred');
      document.getElementById('edit-existing-attachment-path').value = g('data-existing-attachment-path');
      document.getElementById('edit-record-date').value = g('data-record-date');
      openModal('editModal');
    });
  });
</script>

<?php
$conn->close();
include '../../partials/footer.php';
?>
