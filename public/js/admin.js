/* ═══════════════════════════════════════════════════════
   INVITATION MARIAGE — Admin JS
   ═══════════════════════════════════════════════════════ */

document.addEventListener('DOMContentLoaded', function () {

  // ─── Sidebar Toggle (mobile) ──────────────────────────
  const sidebarToggle = document.getElementById('sidebarToggle');
  const sidebar = document.getElementById('adminSidebar');
  const main = document.getElementById('adminMain');

  if (sidebarToggle && sidebar) {
    sidebarToggle.addEventListener('click', () => {
      sidebar.classList.toggle('open');
    });

    document.addEventListener('click', (e) => {
      if (window.innerWidth < 768 && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove('open');
      }
    });
  }

  // ─── Auto-dismiss alerts ──────────────────────────────
  setTimeout(() => {
    document.querySelectorAll('.alert:not(.alert-permanent)').forEach(alert => {
      alert.style.transition = 'opacity 0.5s';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    });
  }, 4500);

  // ─── Confirm delete forms ─────────────────────────────
  document.querySelectorAll('[data-confirm]').forEach(el => {
    el.addEventListener('click', (e) => {
      if (!confirm(el.dataset.confirm)) e.preventDefault();
    });
  });

  // ─── Active nav based on current URL ─────────────────
  const currentPath = window.location.pathname;
  document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
    if (link.getAttribute('href') === currentPath) {
      link.classList.add('active');
    }
  });

  // ─── Flatpickr init ───────────────────────────────────
  if (typeof flatpickr !== 'undefined') {
    flatpickr('.datepicker', {
      locale: 'fr',
      dateFormat: 'Y-m-d',
      allowInput: true,
    });
    flatpickr('.timepicker', {
      enableTime: true,
      noCalendar: true,
      dateFormat: 'H:i',
      time_24hr: true,
    });
  }

  // ─── Image preview on file input ──────────────────────
  document.querySelectorAll('input[type="file"][data-preview]').forEach(input => {
    input.addEventListener('change', function () {
      const preview = document.getElementById(this.dataset.preview);
      if (preview && this.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { preview.src = e.target.result; preview.style.display = 'block'; };
        reader.readAsDataURL(this.files[0]);
      }
    });
  });

  // ─── Wedding session store ────────────────────────────
  const weddingSelector = document.querySelector('[data-wedding-selector]');
  if (weddingSelector) {
    weddingSelector.addEventListener('change', function () {
      fetch('/admin/set-wedding/' + this.value, { method: 'POST', headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content } });
    });
  }

  // ─── Color picker sync ───────────────────────────────
  document.querySelectorAll('input[type="color"][data-sync]').forEach(picker => {
    const target = document.getElementById(picker.dataset.sync);
    if (target) {
      picker.addEventListener('input', () => { target.value = picker.value; });
      target.addEventListener('input', () => {
        if (/^#[0-9a-fA-F]{6}$/.test(target.value)) picker.value = target.value;
      });
    }
  });

});
