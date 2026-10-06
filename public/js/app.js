document.addEventListener('DOMContentLoaded', () => {
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');
  const menuBtn = document.getElementById('menuBtn');
  const closeBtn = document.getElementById('closeSidebar');

  function openSidebar() {
    if (sidebar) sidebar.classList.add('open');
    if (overlay) overlay.classList.add('show');
  }
  function closeSidebar() {
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('show');
  }

  if (menuBtn) menuBtn.addEventListener('click', openSidebar);
  if (closeBtn) closeBtn.addEventListener('click', closeSidebar);
  if (overlay)  overlay.addEventListener('click', closeSidebar);

  document.querySelectorAll('.modal-backdrop').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
  });
  document.querySelectorAll('[data-modal-close]').forEach(btn => {
    btn.addEventListener('click', () => {
      const id = btn.getAttribute('data-modal-close');
      const m  = document.getElementById(id);
      if (m) m.classList.remove('open');
    });
  });

  document.querySelectorAll('[data-confirm-form]').forEach(form => {
    form.addEventListener('submit', e => {
      if (!confirm(form.getAttribute('data-confirm-message') || 'Are you sure?')) e.preventDefault();
    });
  });

  document.querySelectorAll('form[novalidate]').forEach(form => {
    form.addEventListener('submit', () => {
      const btn = form.querySelector('[type="submit"]');
      if (btn) { btn.disabled = true; btn.dataset.orig = btn.innerHTML; btn.innerHTML = '<span class="spinner"></span> Processing...'; setTimeout(() => { btn.disabled = false; if (btn.dataset.orig) btn.innerHTML = btn.dataset.orig; }, 4000); }
    });
  });

  // Auto Dismiss alerts after 4s
  setTimeout(() => {
    document.querySelectorAll('.alert').forEach(a => a.style.display = 'none');
  }, 4000);

  // Image preview
  const imgInput = document.getElementById('product_image');
  const preview  = document.getElementById('imagePreview');
  if (imgInput && preview) {
    imgInput.addEventListener('change', () => {
      const f = imgInput.files && imgInput.files[0];
      if (f) {
        const url = URL.createObjectURL(f);
        preview.innerHTML = '<img src="' + url + '" alt="Preview">';
      }
    });
  }

  // Message modal open helper
  window.openMessageModal = function(el) {
    const m = document.getElementById('messageModal');
    if (!m) return;
    m.querySelector('#mName').textContent    = el.dataset.name || '-';
    m.querySelector('#mEmail').textContent   = el.dataset.email || '-';
    m.querySelector('#mDate').textContent    = el.dataset.date || '-';
    m.querySelector('#mText').textContent    = el.dataset.message || '-';
    const delForm = m.querySelector('#modalDeleteForm');
    if (delForm) { delForm.action = el.dataset.deleteUrl || delForm.action; }
    const idEl = m.querySelector('#modalDeleteId');
    if (idEl) idEl.value = el.dataset.id || '';
    m.classList.add('open');
  };
});
