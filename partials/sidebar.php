<?php
// expects $role_id and $full_name defined by header.php
include '../../config/config.php';

// Helpers
if (!function_exists('sb_link')) {
  function sb_link($href, $label, $svg) {
    return '
      <li>
        <a href="'.$href.'" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-brand-800/60">
          '.$svg.'
          <span class="label">'.$label.'</span>
        </a>
      </li>';
  }
}
if (!function_exists('sb_tree_start')) {
  function sb_tree_start($label, $icon) {
    return '
      <li>
        <button class="w-full flex items-center justify-between gap-3 rounded-lg px-3 py-2 hover:bg-brand-800/60" data-tree>
          <span class="flex items-center gap-3">
            '.$icon.'
            <span class="label">'.$label.'</span>
          </span>
          <svg class="chev h-4 w-4 opacity-80 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <ul class="submenu hidden pl-10 pr-2 py-1 space-y-1">';
  }
}
$tree_end = '</ul></li>';

// Icons (Heroicons outline)
$ic_home   = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l9-9 9 9M4 10v10a1 1 0 001 1h5m4 0h5a1 1 0 001-1V10"/></svg>';
$ic_users  = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a4 4 0 00-3-3.87M9 20H4v-2a4 4 0 013-3.87M12 12a4 4 0 100-8 4 4 0 000 8z"/></svg>';
$ic_clip   = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6M9 8h6M7 4h10a2 2 0 012 2v12a2 2 0 01-2 2H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>';
$ic_med    = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-3-3v6m8-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>';
$ic_note   = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6M9 16h6M9 8h6m-6 12h6M7 4h10a2 2 0 012 2v4H7a2 2 0 01-2-2V6a2 2 0 012-2z"/></svg>';
$ic_users2 = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 20h10M12 14a5 5 0 100-10 5 5 0 000 10z"/></svg>';
$ic_cal    = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3M3 11h18M5 21h14a2 2 0 002-2V7H3v12a2 2 0 002 2z"/></svg>';
$ic_chart  = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3v18M6 8v13M16 13v8M21 6v15"/></svg>';
$ic_logout = '<svg class="h-5 w-5 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 11-4 0v-1"/></svg>';
?>

<aside id="sidebar"
       class="fixed top-[var(--header-h)] left-0 z-50 h-[calc(100vh-var(--header-h))] w-64 -translate-x-full md:translate-x-0
              bg-brand-950 text-emerald-50 border-r border-brand-800 transition-transform duration-300
              overflow-x-hidden">
  <div class="h-full flex flex-col">
    <!-- User Panel -->
    <div class="px-4 py-4 border-b border-brand-800 flex items-center gap-3">
      <div class="min-w-0 user-text">
        <div class="font-semibold truncate"><?= htmlspecialchars($full_name) ?></div>
        <div class="text-xs text-emerald-300"><?= role_label($role_id) ?></div>
      </div>
    </div>

    <!-- Menu -->
    <nav class="flex-1 overflow-y-auto p-3 scroll-y">
      <p class="section-label px-2 mb-2 text-[10px] uppercase tracking-widest text-emerald-300/70">Main Navigation</p>
      <ul class="space-y-1 text-sm">
        <?php
        // Fetch unread logs count
        $unread_count = $conn->query("SELECT COUNT(*) as count FROM system_logs WHERE is_read = 0")->fetch_assoc()['count'];

        switch ($role_id) {
          case 1: // Administrator
            echo sb_link('../../roles/admin/index.php', 'Dashboard', $ic_home);

            echo sb_tree_start('Manage Inmates', $ic_users);
            echo sb_link('../../roles/admin/inmates.php', 'View / Manage Inmate Profiles', $ic_clip);
            echo sb_link('../../roles/admin/transfers.php', 'Transfers & Releases', $ic_clip);
            echo $tree_end;

            echo sb_tree_start('Programs & Rehabilitation', $ic_note);
            echo sb_link('../../roles/admin/program.php', 'Manage Programs', $ic_clip);
            echo sb_link('../../roles/admin/progress.php', 'Inmate Progress', $ic_clip);
            echo $tree_end;

            echo sb_link('../../roles/admin/medical.php', 'Medical Records', $ic_med);
            echo sb_link('../../roles/admin/users.php', 'Users & Roles', $ic_users2);
            echo sb_link('../../roles/admin/visitation.php', 'Visitations', $ic_cal);

            // System Logs dropdown below visitation
            $logs_label = 'System Logs' . ($unread_count > 0 ? " <span class='bg-red-500 text-white text-xs px-2 py-1 rounded'>$unread_count</span>" : '');
            echo sb_tree_start($logs_label, $ic_note);
            echo sb_link('../../roles/admin/logs.php', 'View Logs', $ic_clip);
            if ($unread_count > 0) {
              // Fetch recent logs
              $recent_logs = $conn->query("SELECT action, details, created_at FROM system_logs WHERE is_read = 0 ORDER BY created_at DESC LIMIT 5");
              while ($log = $recent_logs->fetch_assoc()) {
                echo '<li class="pl-6 py-1 text-xs text-emerald-200">' . htmlspecialchars($log['action']) . ': ' . htmlspecialchars(substr($log['details'], 0, 50)) . '...</li>';
              }
            }
            echo $tree_end;
            break;

          case 2: // Correctional Officer
            echo sb_link('../../roles/officer/index.php', 'Dashboard', $ic_home);
            echo sb_link('../../roles/officer/inmates.php', 'Inmates', $ic_users);
            echo sb_link('../../roles/officer/behavior.php', 'Behavior Logs', $ic_note);
            break;

          case 3: // Medical Staff
            echo sb_link('../../roles/medical/index.php', 'Dashboard', $ic_home);
            echo sb_link('../../roles/medical/medical.php', 'Medical Records', $ic_med);
            echo sb_link('../../roles/medical/inmates.php', 'Inmates Medic', $ic_users);
            echo sb_link('../../roles/medical/health_reports.php', 'Health Reports', $ic_chart);
            echo sb_link('../../roles/medical/incidents.php', 'Incidents', $ic_clip);
            break;

          case 4: // Rehabilitation Staff
            echo sb_link('../../roles/rehab/index.php', 'Dashboard', $ic_home);
            echo sb_tree_start('Programs', $ic_note);
            echo sb_link('../../roles/rehab/programs.php', 'Manage Programs', $ic_clip);
            echo sb_link('../../roles/rehab/progress.php', 'Inmate Progress', $ic_chart);
            echo $tree_end;
            echo sb_link('../../roles/rehab/behavior.php', 'Behavior Logs', $ic_note);
            echo sb_link('../../roles/rehab/report.php', 'Reports', $ic_chart);
            break;

          case 5: // Visitor
            echo sb_link('../../roles/visitor/index.php', 'Dashboard', $ic_home);
            echo sb_link('../../roles/visitor/schedule.php', 'Visitation Schedule', $ic_cal);
            break;

          default:
            echo '<li><div class="px-3 py-2 text-emerald-200/70">No menu available</div></li>';
            break;
        }
        ?>
      </ul>
    </nav>

    <!-- Logout -->
    <div class="mt-auto border-t border-brand-800 p-3">
      <a href="../../auth/logout.php" class="flex items-center gap-3 rounded-lg px-3 py-2 hover:bg-brand-800/60">
        <?= $ic_logout ?>
        <span class="label">Logout</span>
      </a>
    </div>
  </div>
</aside>
