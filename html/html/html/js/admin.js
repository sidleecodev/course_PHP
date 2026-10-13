/* =====================================================================
   admin.js — 後台管理系統（靜態 HTML 版）共用 JavaScript
   需先載入 SweetAlert2 與 main.js
   功能：側欄開關、關鍵字即時查詢（標示關鍵字）、下拉篩選、
         SweetAlert2 刪除確認、表單驗證與送出、修改時帶入資料、圖片 / 圖示預覽、登入、登出
   ===================================================================== */

const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true });
const escapeHtml = s => String(s ?? '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));

/* ---------- 列表：關鍵字查詢 + 篩選 ---------- */
function initCrudList(root) {
  const keyword = root.querySelector('input[name="keyword"]');
  const filters = root.querySelectorAll('select[data-filter]');
  const rows = () => root.querySelectorAll('tbody tr[data-row]');
  const empty = root.querySelector('tr[data-empty]');
  const total = root.querySelector('[data-total]');
  let timer = null;

  // 記住每個可標示欄位的原始文字
  root.querySelectorAll('[data-mark]').forEach(td => { td.dataset.text = td.textContent.trim(); });

  function apply() {
    const kw = keyword.value.trim().toLowerCase();
    let shown = 0;
    rows().forEach(tr => {
      const text = Array.from(tr.querySelectorAll('[data-mark]')).map(td => td.dataset.text).join(' ').toLowerCase();
      let ok = !kw || text.includes(kw);
      filters.forEach(s => { if (s.value !== '' && tr.dataset[s.name] !== s.value) ok = false; });
      tr.classList.toggle('hidden', !ok);
      if (ok) shown++;

      // 標示關鍵字
      tr.querySelectorAll('[data-mark]').forEach(td => {
        const raw = td.dataset.text;
        if (!kw) { td.textContent = raw; return; }
        const re = new RegExp('(' + kw.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        td.innerHTML = escapeHtml(raw).replace(re, '<mark>$1</mark>');
      });
    });
    if (empty) empty.classList.toggle('hidden', shown > 0);
    if (total) total.textContent = shown;
  }

  keyword.addEventListener('input', () => { clearTimeout(timer); timer = setTimeout(apply, 200); });
  keyword.closest('form')?.addEventListener('submit', e => { e.preventDefault(); apply(); });
  filters.forEach(s => s.addEventListener('change', apply));
  root.querySelector('[data-clear]')?.addEventListener('click', () => {
    keyword.value = '';
    filters.forEach(s => { s.value = ''; });
    apply();
  });

  // 刪除：SweetAlert2 確認後移除該列
  root.addEventListener('click', async e => {
    const btn = e.target.closest('[data-delete]');
    if (!btn) return;
    const r = await Swal.fire({
      icon: 'warning', title: '確定要刪除？', text: `「${btn.dataset.name}」刪除後無法復原`,
      showCancelButton: true, confirmButtonText: '刪除', cancelButtonText: '取消', confirmButtonColor: '#DC2626', reverseButtons: true,
    });
    if (!r.isConfirmed) return;
    if (btn.dataset.blocked) {
      Swal.fire({ icon: 'error', title: '無法刪除', text: btn.dataset.blocked, confirmButtonColor: '#1B2E5E' });
      return;
    }
    btn.closest('tr').remove();
    apply();
    Toast.fire({ icon: 'success', title: '已刪除' });
  });

  apply();
}

/* ---------- 表單：修改時帶入資料、驗證、送出 ---------- */
function initForm(form) {
  const id = new URLSearchParams(location.search).get('id');
  const recordsEl = document.getElementById('records');
  const records = recordsEl ? JSON.parse(recordsEl.textContent) : {};
  const record = id ? records[id] : null;

  // 修改模式：標題改為「修改」、帶入資料
  if (record) {
    document.querySelectorAll('[data-mode-text]').forEach(el => { el.textContent = el.textContent.replace('新增', '修改'); });
    document.title = document.title.replace('新增', '修改');
    Object.entries(record).forEach(([name, value]) => {
      const el = form.elements[name];
      if (!el || el.type === 'file' || el.type === 'password') return;
      if (el.type === 'checkbox') el.checked = !!value;
      else if (el instanceof RadioNodeList) {
        const cb = Array.from(el).find(x => x.type === 'checkbox');
        if (cb) cb.checked = !!value;
      } else el.value = value ?? '';
      if (el.dataset?.iconPreview) document.getElementById(el.dataset.iconPreview).className = 'fa ' + el.value + ' icon';
    });
    form.querySelectorAll('[data-readonly-on-edit]').forEach(el => { el.readOnly = true; });
    form.querySelectorAll('[data-required-on-create]').forEach(el => { el.required = false; });
    form.querySelectorAll('[data-password-label]').forEach(el => { el.textContent = '新密碼（留空不變更）'; });
  }

  // 圖片預覽
  form.querySelectorAll('input[type=file][data-preview]').forEach(input => {
    input.addEventListener('change', () => {
      const img = document.getElementById(input.dataset.preview);
      if (input.files[0]) { img.src = URL.createObjectURL(input.files[0]); img.classList.remove('hidden'); }
    });
  });
  // 圖示預覽
  form.querySelectorAll('input[data-icon-preview]').forEach(input => {
    input.addEventListener('input', () => { document.getElementById(input.dataset.iconPreview).className = 'fa ' + input.value.trim() + ' icon'; });
  });

  // 送出：檢查必填 → SweetAlert2 成功 → 回列表
  form.addEventListener('submit', e => {
    e.preventDefault();
    form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    form.querySelectorAll('.invalid-feedback.js').forEach(el => el.remove());

    const missing = [];
    form.querySelectorAll('[required]').forEach(el => {
      if (String(el.value).trim() === '') {
        el.classList.add('is-invalid');
        const label = form.querySelector(`label[for="${el.id}"]`)?.childNodes[0].textContent.trim() || el.name;
        missing.push(label);
        el.closest('.form-group').insertAdjacentHTML('beforeend', `<div class="invalid-feedback js">請輸入 ${escapeHtml(label)}。</div>`);
      }
    });
    if (missing.length) {
      Swal.fire({ icon: 'error', title: '資料有誤', html: missing.map(m => `<div>請輸入 ${escapeHtml(m)}。</div>`).join(''), confirmButtonColor: '#1B2E5E' });
      return;
    }

    Swal.fire({ icon: 'success', title: record ? '資料已更新' : '資料已新增', timer: 1300, showConfirmButton: false })
      .then(() => { location.href = form.dataset.back; });
  });
}

/* ---------- 後台登入（示範帳號 admin / admin1234） ---------- */
function initLogin(form) {
  form.addEventListener('submit', e => {
    e.preventDefault();
    if (form.username.value === 'admin' && form.password.value === 'admin1234') {
      Swal.fire({ icon: 'success', title: '歡迎回來，系統管理員', timer: 1200, showConfirmButton: false })
        .then(() => { location.href = 'index.html'; });
    } else {
      Swal.fire({ icon: 'error', title: '帳號或密碼錯誤', text: '示範帳號：admin / admin1234', confirmButtonColor: '#1B2E5E' });
    }
  });
}

document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('[data-crud-list]').forEach(initCrudList);
  document.querySelectorAll('form.admin-form').forEach(initForm);
  document.querySelectorAll('form[data-login]').forEach(initLogin);

  document.querySelector('.admin-menu-toggle')?.addEventListener('click', () => {
    document.querySelector('.admin-sidebar').classList.toggle('open');
  });

  // 登出確認
  document.querySelector('[data-logout]')?.addEventListener('click', e => {
    e.preventDefault();
    Swal.fire({ icon: 'question', title: '確定要登出後台？', showCancelButton: true, confirmButtonText: '確定', cancelButtonText: '取消', confirmButtonColor: '#1B2E5E' })
      .then(r => { if (r.isConfirmed) location.href = 'login.html'; });
  });
});
