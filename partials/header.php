<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
$role_id   = $_SESSION['role_id'] ?? null;
$full_name = $_SESSION['full_name'] ?? 'Guest';

if (!function_exists('role_label')) {
  function role_label($id) {
    return match($id) {
      1 => 'Administrator',
      2 => 'Correctional Officer',
      3 => 'Medical Staff',
      4 => 'Rehabilitation Staff',
      5 => 'Visitor',
      default => 'Guest',
    };
  }
}

$page_title = $page_title ?? 'PMS Dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title><?= htmlspecialchars($page_title) ?></title>

  <!-- Tailwind CDN; if you already compile Tailwind, you can remove this -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            brand: {
              50:'#ecfdf5',100:'#d1fae5',200:'#a7f3d0',300:'#6ee7b7',
              400:'#34d399',500:'#10b981',600:'#059669',700:'#047857',
              800:'#065f46',900:'#064e3b',950:'#022c22'
            }
          }
        }
      }
    }
  </script>

  <style>
    :root{
      --header-h: 56px;          /* header height */
      --sbw: 16rem;              /* expanded sidebar width (w-64) */
      --sbw-collapsed: 4rem;     /* collapsed sidebar width */
    }

    /* Keep content below fixed header */
    body { padding-top: var(--header-h); }

    /* Sidebar collapsed visuals */
    #sidebar[data-collapsed="true"] { width: var(--sbw-collapsed); }
    #sidebar[data-collapsed="true"] .label,
    #sidebar[data-collapsed="true"] .chev,
    #sidebar[data-collapsed="true"] .submenu,
    #sidebar[data-collapsed="true"] .user-text,
    #sidebar[data-collapsed="true"] .section-label { display: none !important; }
    #sidebar[data-collapsed="true"] .user-avatar { margin: 0 auto; }

    /* Prevent any scroll/peek when collapsed */
    #sidebar[data-collapsed="true"],
    #sidebar[data-collapsed="true"] nav { overflow: hidden !important; }

    /* Push content and footer on desktop */
    @media (min-width: 768px){
      #content, main { margin-left: var(--sbw); }
      body.sidebar-collapsed #content,
      body.sidebar-collapsed main { margin-left: var(--sbw-collapsed); }

      footer.sticky-footer { margin-left: var(--sbw); }
      body.sidebar-collapsed footer.sticky-footer { margin-left: var(--sbw-collapsed); }
    }

    .scroll-y { scrollbar-width: thin; }
  </style>
</head>
<body class="bg-slate-100 text-slate-900 antialiased">

<!-- Top Navbar -->
<header id="app-header" class="fixed top-0 inset-x-0 z-50 bg-brand-900 text-white shadow" style="height: var(--header-h);">
  <div class="h-full flex items-center justify-between px-3">
    <div class="flex items-center gap-2">
      <!-- Mobile drawer open -->
      <button id="mobile-toggle"
              class="md:hidden p-2 rounded hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
              aria-label="Open sidebar">
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M4 6h16M4 12h16m-7 6h7"/>
        </svg>
      </button>

      <!-- Desktop collapse -->
      <button id="desktop-collapse"
              class="hidden md:inline-flex p-2 rounded hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
              aria-label="Collapse sidebar" title="Collapse sidebar">
        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M11 19l-7-7 7-7M20 19l-7-7 7-7"/>
        </svg>
      </button>

      <!-- Brand -->
      <a href="#" class="ml-1 flex items-center gap-2">
        <div class="grid h-8 w-8 place-content-center rounded bg-white text-brand-900 font-black">PM</div>
        <span class="font-semibold tracking-wide">Prison Management System</span>
      </a>
    </div>

    <div class="flex items-center gap-2">
      <?php if (isset($_SESSION['role_id']) && $_SESSION['role_id'] == 5): ?>
        <!-- Notifications (Visitor) -->
        <div class="relative">
          <button id="notification-btn"
                  class="relative p-2 rounded hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
                  aria-haspopup="true" aria-expanded="false">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6 6 0 10-12 0v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
            </svg>
            <span id="notification-count"
                  class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] rounded-full h-4 min-w-[16px] px-1 flex items-center justify-center hidden">0</span>
          </button>
          <div id="notification-dropdown"
               class="absolute right-0 mt-2 w-80 bg-white text-slate-800 rounded-md shadow-lg z-50 hidden">
            <div class="border-b px-4 py-2 text-sm font-semibold">Notifications</div>
            <div id="notification-list" class="max-h-72 overflow-y-auto scroll-y"></div>
          </div>
        </div>
      <?php endif; ?>

      <!-- User menu -->
      <div class="relative">
        <button id="user-menu-btn"
                class="flex items-center gap-2 rounded px-2 py-1 hover:bg-white/10 focus:outline-none focus:ring-2 focus:ring-white/30"
                aria-haspopup="true" aria-expanded="false">
          <span class="hidden sm:inline text-sm"><?= htmlspecialchars($full_name) ?></span>
          <svg class="h-4 w-4 opacity-80" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
          </svg>
        </button>
        <div id="user-menu" class="absolute right-0 mt-2 w-48 bg-white text-slate-800 rounded shadow-lg hidden">
          <div class="px-4 py-2 text-xs text-slate-500 border-b"><?= role_label($role_id) ?></div>
          <a href="../../auth/logout.php" class="block px-4 py-2 text-sm hover:bg-slate-50">Logout</a>
        </div>
      </div>
    </div>
  </div>
</header>

<!-- Mobile backdrop -->
<div id="backdrop" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden"></div>
