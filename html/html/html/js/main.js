/* =====================================================================
   main.js — 全站共用 JavaScript（手機選單、回頂端、捲動淡入、密碼工具、購物車徽章）
   ===================================================================== */

/* 手機選單 */
function toggleNav() {
  const links = document.querySelector('.nav-links');
  if (links) links.classList.toggle('open');
}

/* 購物車數量徽章 */
function updateCartBadge(n) {
  const badge = document.getElementById('cart-badge');
  if (!badge) return;
  badge.textContent = n;
  badge.style.display = n > 0 ? 'flex' : 'none';
}

/* 顯示 / 隱藏密碼 */
function togglePw(id, btn) {
  const el = document.getElementById(id);
  const icon = btn.querySelector('i');
  if (el.type === 'password') { el.type = 'text'; icon.className = 'fa fa-eye-slash'; }
  else { el.type = 'password'; icon.className = 'fa fa-eye'; }
}

/* 密碼強度 */
function checkStrength(v) {
  const bar = document.getElementById('pw-bar');
  const hint = document.getElementById('pw-hint');
  if (!bar) return;
  let s = 0;
  if (v.length >= 8) s++;
  if (/[A-Za-z]/.test(v)) s++;
  if (/[0-9]/.test(v)) s++;
  if (/[^A-Za-z0-9]/.test(v)) s++;
  const colors = ['#ef4444', '#f97316', '#eab308', '#22c55e'];
  const labels = ['過短', '弱', '普通', '強'];
  const idx = Math.max(0, s - 1);
  bar.style.width = (s * 25) + '%';
  bar.style.background = colors[idx];
  if (hint) hint.textContent = '密碼強度：' + labels[idx];
}

/* 兩次密碼是否一致（input#pw1、input#pw2、p#match-hint） */
function bindPasswordMatch(pw1Id, pw2Id, hintId) {
  const pw2 = document.getElementById(pw2Id);
  if (!pw2) return;
  pw2.addEventListener('input', function () {
    const hint = document.getElementById(hintId);
    if (this.value && this.value !== document.getElementById(pw1Id).value) {
      hint.style.color = '#dc2626'; hint.textContent = '⚠ 兩次密碼不一致';
    } else if (this.value) {
      hint.style.color = '#16a34a'; hint.textContent = '✓ 密碼一致';
    } else { hint.textContent = ''; }
  });
}

/* 簡易 toast */
function showToast(msg) {
  let t = document.getElementById('toast');
  if (!t) {
    t = document.createElement('div');
    t.id = 'toast'; t.className = 'toast';
    t.innerHTML = '<i class="fa fa-check-circle"></i><span></span>';
    document.body.appendChild(t);
  }
  t.querySelector('span').textContent = msg;
  t.classList.add('show');
  clearTimeout(t._timer);
  t._timer = setTimeout(() => t.classList.remove('show'), 2500);
}

document.addEventListener('DOMContentLoaded', function () {
  /* 回頂端 */
  const backBtn = document.getElementById('back-top');
  if (backBtn) {
    window.addEventListener('scroll', () => backBtn.classList.toggle('show', window.scrollY > 400));
    backBtn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));
  }

  /* 捲動淡入 */
  const reveals = document.querySelectorAll('.reveal');
  if (reveals.length && 'IntersectionObserver' in window) {
    const obs = new IntersectionObserver(entries => {
      entries.forEach(e => { if (e.isIntersecting) e.target.classList.add('visible'); });
    }, { threshold: 0.12 });
    reveals.forEach(el => obs.observe(el));
  } else {
    reveals.forEach(el => el.classList.add('visible'));
  }

  /* 數字跳動 */
  const nums = document.querySelectorAll('.stat-num[data-target]');
  if (nums.length && 'IntersectionObserver' in window) {
    const numObs = new IntersectionObserver(entries => {
      entries.forEach(e => {
        if (!e.isIntersecting) return;
        const el = e.target;
        const target = parseInt(el.dataset.target, 10);
        const suffix = el.dataset.suffix || '';
        let cur = 0;
        const step = Math.max(1, Math.ceil(target / 60));
        const timer = setInterval(() => {
          cur = Math.min(cur + step, target);
          el.innerHTML = cur.toLocaleString() + (suffix ? `<sup>${suffix}</sup>` : '');
          if (cur >= target) clearInterval(timer);
        }, 20);
        numObs.unobserve(el);
      });
    }, { threshold: 0.5 });
    nums.forEach(el => numObs.observe(el));
  }
});
