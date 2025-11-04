<?php /* No visual footer; scripts & closing tags only */ ?>

<script>
  // Header offset auto-fix (in case height changes)
  function updateHeaderOffset(){
    const hh = document.getElementById('app-header')?.offsetHeight || 56;
    document.body.style.paddingTop = hh + 'px';
  }
  updateHeaderOffset();
  window.addEventListener('resize', updateHeaderOffset);

  const sidebar          = document.getElementById('sidebar');
  const backdrop         = document.getElementById('backdrop');
  const mobileToggle     = document.getElementById('mobile-toggle');
  const desktopCollapse  = document.getElementById('desktop-collapse');
  const userBtn          = document.getElementById('user-menu-btn');
  const userMenu         = document.getElementById('user-menu');

  // Mobile drawer
  function openDrawer(){ sidebar.classList.remove('-translate-x-full'); backdrop.classList.remove('hidden'); }
  function closeDrawer(){ sidebar.classList.add('-translate-x-full'); backdrop.classList.add('hidden'); }
  mobileToggle?.addEventListener('click', openDrawer);
  backdrop?.addEventListener('click', closeDrawer);
  // Close drawer when clicking a sidebar link (mobile)
  sidebar?.querySelectorAll('a[href]').forEach(a => a.addEventListener('click', () => {
    if (window.innerWidth < 768) closeDrawer();
  }));

  // Desktop mini collapse
  desktopCollapse?.addEventListener('click', () => {
    const collapsed = sidebar.getAttribute('data-collapsed') === 'true';
    if (collapsed) {
      sidebar.setAttribute('data-collapsed','false');
      document.body.classList.remove('sidebar-collapsed');
    } else {
      sidebar.setAttribute('data-collapsed','true');
      document.body.classList.add('sidebar-collapsed');
    }
  });
  // default expanded
  sidebar?.setAttribute('data-collapsed','false');
  document.body.classList.remove('sidebar-collapsed');

  // Tree menus
  document.querySelectorAll('[data-tree]').forEach(btn => {
    btn.addEventListener('click', () => {
      const li   = btn.parentElement;
      const sub  = li.querySelector('.submenu');
      const chev = btn.querySelector('.chev');
      sub.classList.toggle('hidden');
      chev.classList.toggle('rotate-180');
    });
  });

  // User menu
  userBtn?.addEventListener('click', (e) => {
    e.stopPropagation();
    userMenu.classList.toggle('hidden');
  });
  document.addEventListener('click', () => userMenu?.classList.add('hidden'));

  // Notifications (Visitor)
  const notifBtn   = document.getElementById('notification-btn');
  const notifDrop  = document.getElementById('notification-dropdown');
  const notifCount = document.getElementById('notification-count');
  const notifList  = document.getElementById('notification-list');

  function loadNotifications(){
    fetch('get_notifications.php')
      .then(r => r.json())
      .then(data => {
        if (!notifList) return;
        notifList.innerHTML = '';
        const arr = data?.notifications ?? [];
        if (arr.length) {
          arr.forEach(n => {
            const item = document.createElement('div');
            item.className = 'px-4 py-2 text-sm text-slate-700 border-b hover:bg-slate-50 cursor-pointer';
            item.innerHTML = `
              <div class="font-medium">${n.title ?? 'Untitled'}</div>
              <div class="text-slate-500 text-xs">${n.message ?? ''}</div>
              <div class="text-slate-400 text-[11px] mt-1">${n.created_at ?? ''}</div>
            `;
            notifList.appendChild(item);
          });
          notifCount?.classList.remove('hidden');
          if (notifCount) notifCount.textContent = String(arr.length);
        } else {
          notifList.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">No new notifications</div>';
          notifCount?.classList.add('hidden');
        }
      })
      .catch(() => {
        if (notifList) notifList.innerHTML = '<div class="px-4 py-3 text-sm text-rose-600">Failed to load notifications</div>';
        notifCount?.classList.add('hidden');
      });
  }

  if (notifBtn && notifDrop) {
    notifBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      notifDrop.classList.toggle('hidden');
    });
    document.addEventListener('click', () => notifDrop.classList.add('hidden'));
    loadNotifications();
    setInterval(loadNotifications, 30000);
  }
</script>

</body>
</html>
