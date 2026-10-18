// ============================================
// PIUL CELL — Main JS (Enhanced)
// ============================================

// Dark mode (persistent)
(function () {
  const saved = localStorage.getItem('piulcell_theme');
  if (saved) document.documentElement.setAttribute('data-theme', saved);
  else if (window.matchMedia?.('(prefers-color-scheme: dark)').matches) {
    document.documentElement.setAttribute('data-theme', 'dark');
  }
})();

function toggleTheme() {
  const root = document.documentElement;
  const cur = root.getAttribute('data-theme') || 'light';
  const next = cur === 'dark' ? 'light' : 'dark';
  root.setAttribute('data-theme', next);
  localStorage.setItem('piulcell_theme', next);
  document.querySelectorAll('#themeToggle i').forEach(i => i.className = next === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars');
}

function toggleUserMenu(e) {
  e.stopPropagation();
  document.getElementById('userMenu')?.classList.toggle('show');
}
document.addEventListener('click', () => document.getElementById('userMenu')?.classList.remove('show'));

function toggleMobileMenu() {
  const menu = document.getElementById('mobileMenu');
  if (!menu) return;
  const opened = menu.classList.toggle('show');
  menu.setAttribute('aria-hidden', opened ? 'false' : 'true');
  const button = document.querySelector('.mobile-menu-btn');
  if (button) button.setAttribute('aria-expanded', opened ? 'true' : 'false');
  document.body.classList.toggle('mobile-menu-open', opened);
}

// Toast
function showToast(message, type = 'info') {
  const toast = document.createElement('div');
  toast.className = `notif-toast ${type}`;
  const icon = type === 'success' ? 'check-circle-fill' : type === 'error' ? 'x-circle-fill' : 'info-circle-fill';
  toast.innerHTML = `<i class="bi bi-${icon}"></i><span>${message}</span>`;
  document.body.appendChild(toast);
  requestAnimationFrame(() => toast.classList.add('show'));
  setTimeout(() => { toast.classList.remove('show'); setTimeout(() => toast.remove(), 400); }, 3000);
}

// CSRF + AJAX helper
const getCsrf = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
async function postJSON(url, data) {
  const fd = new FormData();
  for (const k in data) fd.append(k, data[k]);
  fd.append('_csrf', getCsrf());
  const res = await fetch(url, { method: 'POST', body: fd, credentials: 'same-origin' });
  return res.json();
}

// Cart helpers
async function addToCart(variantId, qty = 1) {
  if (!variantId) { showToast('Pilih varian terlebih dahulu', 'error'); return; }
  const r = await postJSON('/cart/add', { variant_id: variantId, quantity: qty });
  if (r.success) {
    showToast(r.message || 'Ditambahkan ke keranjang', 'success');
    const badge = document.querySelector('#cartCount');
    if (badge) {
      badge.textContent = r.cart_count;
      badge.style.display = r.cart_count > 0 ? 'flex' : 'none';
      badge.classList.add('pulse'); setTimeout(() => badge.classList.remove('pulse'), 400);
    }
  } else showToast(r.message || 'Gagal', 'error');
}
async function buyNow(variantId, qty = 1) {
  if (!variantId) { showToast('Pilih varian terlebih dahulu', 'error'); return; }
  const r = await postJSON('/cart/add', { variant_id: variantId, quantity: qty });
  if (r.success) window.location.href = '/checkout';
  else showToast(r.message || 'Gagal', 'error');
}
async function updateCartItem(variantId, qty) {
  const r = await postJSON('/cart/update', { variant_id: variantId, quantity: qty });
  if (r.success) location.reload(); else showToast(r.message || 'Gagal', 'error');
}
async function removeCartItem(variantId) {
  if (!confirm('Hapus item ini?')) return;
  const r = await postJSON('/cart/remove', { variant_id: variantId });
  if (r.success) location.reload();
}
async function toggleWishlist(productId, btn) {
  const r = await postJSON('/wishlist/toggle', { product_id: productId });
  if (r.success) {
    if (btn) {
      btn.classList.toggle('active', r.added);
      const i = btn.querySelector('i');
      if (i) i.className = r.added ? 'bi bi-heart-fill' : 'bi bi-heart';
    }
    showToast(r.added ? '❤️ Disimpan ke wishlist' : 'Dihapus dari wishlist', r.added ? 'success' : 'info');
  } else if (r.redirect) window.location.href = r.redirect;
}

// Tab navigation
function showTab(tabId, btn) {
  const container = btn.closest('.pd-tabs') || btn.closest('.tabs');
  if (!container) return;
  container.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
  container.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  container.querySelector('#' + tabId)?.classList.add('active');
  btn.classList.add('active');
}

// Copy
function copyText(text, btn) {
  navigator.clipboard.writeText(text).then(() => {
    showToast('Disalin: ' + text, 'success');
    if (btn) { const o = btn.innerHTML; btn.innerHTML = '<i class="bi bi-check-lg"></i> Tersalin'; setTimeout(() => btn.innerHTML = o, 1500); }
  });
}

// Qty +/-
function changeQty(input, delta) {
  const v = Math.max(1, (parseInt(input.value) || 1) + delta);
  input.value = v;
  input.dispatchEvent(new Event('change'));
}

// =============== DOM Ready: animations & sliders ===============
document.addEventListener('DOMContentLoaded', () => {
  // Hero slider auto-rotate
  const slider = document.querySelector('.hero-slider');
  if (slider) {
    const slides = slider.querySelectorAll('.hero-slide');
    const dots = slider.querySelectorAll('.hero-dot');
    if (slides.length > 1) {
      let idx = 0;
      const go = (i) => {
        slides[idx].classList.remove('active');
        dots[idx]?.classList.remove('active');
        idx = (i + slides.length) % slides.length;
        slides[idx].classList.add('active');
        dots[idx]?.classList.add('active');
      };
      let timer = setInterval(() => go(idx + 1), 5500);
      dots.forEach((d, i) => d.addEventListener('click', () => { go(i); clearInterval(timer); timer = setInterval(() => go(idx + 1), 5500); }));
      slider.addEventListener('mouseenter', () => clearInterval(timer));
      slider.addEventListener('mouseleave', () => { timer = setInterval(() => go(idx + 1), 5500); });
    }
  }

  // Search live suggest
  const sInput = document.getElementById('searchInput');
  const sBox = document.getElementById('searchSuggest');
  if (sInput && sBox) {
    let t;
    sInput.addEventListener('input', () => {
      clearTimeout(t);
      const q = sInput.value.trim();
      if (q.length < 2) { sBox.style.display = 'none'; return; }
      t = setTimeout(async () => {
        const r = await fetch('/cari?q=' + encodeURIComponent(q) + '&ajax=1').then(r => r.json()).catch(() => null);
        if (!r || !r.items?.length) { sBox.innerHTML = '<div class="suggest-item">Tidak ada hasil</div>'; sBox.style.display = 'block'; return; }
        sBox.innerHTML = r.items.map(i =>
          `<a class="suggest-item" href="/produk/${i.slug}"><img src="${i.thumb}" alt=""><div><div class="fw-medium">${i.name}</div><div class="text-xs text-muted">${i.brand} · Rp ${i.price}</div></div></a>`
        ).join('');
        sBox.style.display = 'block';
      }, 250);
    });
    document.addEventListener('click', e => { if (!e.target.closest('#searchWrap')) sBox.style.display = 'none'; });
  }

  // Scroll-reveal (IntersectionObserver)
  const reveals = document.querySelectorAll('.reveal, section, .product-card, .stat-card, .admin-card, .brand-chip');
  if (reveals.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry, i) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('in-view');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
    reveals.forEach(el => { el.classList.add('reveal'); io.observe(el); });
  }

  // Staggered product cards
  document.querySelectorAll('.product-grid').forEach(grid => {
    Array.from(grid.children).forEach((c, i) => {
      c.style.transitionDelay = (i * 60) + 'ms';
    });
  });

  // Smooth count-up on stat values (admin/owner dashboards)
  document.querySelectorAll('.stat-value[data-count]').forEach(el => {
    const target = parseFloat(el.dataset.count);
    if (isNaN(target)) return;
    const prefix = el.dataset.prefix || '';
    const dur = 900;
    const start = performance.now();
    const tick = (now) => {
      const p = Math.min(1, (now - start) / dur);
      const e = 1 - Math.pow(1 - p, 3); // easeOutCubic
      const cur = Math.floor(target * e);
      el.textContent = prefix + cur.toLocaleString('id-ID');
      if (p < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
  });

  // Set theme icon
  const dark = document.documentElement.getAttribute('data-theme') === 'dark';
  document.querySelectorAll('#themeToggle i').forEach(i => i.className = dark ? 'bi bi-sun-fill' : 'bi bi-moon-stars');

  // Magnetic hover for buttons (subtle)
  document.querySelectorAll('.btn-primary, .btn-secondary').forEach(btn => {
    btn.addEventListener('mousemove', (e) => {
      const r = btn.getBoundingClientRect();
      const x = e.clientX - r.left - r.width / 2;
      const y = e.clientY - r.top - r.height / 2;
      btn.style.transform = `translate(${x * 0.08}px, ${y * 0.1}px)`;
    });
    btn.addEventListener('mouseleave', () => btn.style.transform = '');
  });
});

// Page enter animation
window.addEventListener('pageshow', () => {
  document.body.classList.add('page-loaded');
});
