// ====== Mobile nav toggle ======
(function () {
  const btn = document.getElementById('hamburger');
  const nav = document.getElementById('nav');
  if (btn && nav) {
    btn.addEventListener('click', () => {
      const show = getComputedStyle(nav).display === 'none';
      nav.style.display = show ? 'block' : 'none';
    });
  }
})();

// ====== Hero image swap ketika hover kartu menu ======
(function () {
  const heroImg = document.getElementById('heroImage');
  if (!heroImg) return;

  document.querySelectorAll('.dish').forEach(card => {
    const img = card.getAttribute('data-img');
    if (!img) return;

    card.addEventListener('mouseenter', () => {
      heroImg.dataset.prev = heroImg.src;
      heroImg.src = (window.ASSETS_BASE || '') + 'assets/img/' + img;
    });

    card.addEventListener('mouseleave', () => {
      if (heroImg.dataset.prev) heroImg.src = heroImg.dataset.prev;
    });
  });
})();

// ====== Search UX sederhana ======
(function () {
  const input = document.getElementById('qsearch');
  if (!input) return;

  input.addEventListener('input', () => {
    const q = input.value.toLowerCase().trim();
    document.querySelectorAll('.dish').forEach(d => {
      const t = (d.querySelector('h3')?.textContent || '').toLowerCase();
      d.style.display = t.includes(q) ? '' : 'none';
    });
  });
})();

// ====== Badge jumlah keranjang ======
function setCartCount(count) {
  const total = Number.parseInt(count, 10) || 0;

  document.querySelectorAll('.cart-count').forEach(el => {
    el.textContent = total > 99 ? '99+' : String(total);
    el.classList.toggle('show', total > 0);
    el.setAttribute('aria-label', `${total} item di keranjang`);
  });
}

async function refreshCartCount() {
  try {
    const base = window.APP_BASE || '/';
    const res = await fetch(base + 'cart/count', { credentials: 'same-origin' });
    if (!res.ok) return;

    const data = await res.json();
    if (typeof data.count !== 'undefined') {
      setCartCount(data.count);
    }
  } catch (e) {
    // Tidak mengganggu halaman jika endpoint belum tersedia.
  }
}

window.setCartCount = setCartCount;
window.refreshCartCount = refreshCartCount;

document.addEventListener('DOMContentLoaded', refreshCartCount);

// ====== Search AJAX untuk halaman lama yang memakai #qsearch ======
(function () {
  const input = document.getElementById('qsearch');
  const list = document.querySelector('.dishes');
  if (!input || !list) return;

  const debounce = (fn, d = 300) => {
    let t;
    return (...a) => {
      clearTimeout(t);
      t = setTimeout(() => fn(...a), d);
    };
  };

  const rupiah = (n) => new Intl.NumberFormat('id-ID').format(n);

  function render(items) {
    list.innerHTML = items.map(m => `
      <div class="dish" data-img="${m.image}">
        <img src="${(window.ASSETS_BASE || '')}assets/img/${m.image}" alt="${m.name}">
        <h3>${m.name}</h3>
        <p>${m.desc ?? ''}</p>
        <span class="price">Rp ${rupiah(m.price)}</span>
        <button class="add-to-cart"
          data-id="${m.id}"
          data-name="${m.name}"
          data-price="${m.price}">
          <i class="fas fa-plus"></i>
        </button>
      </div>
    `).join('');

    list.querySelectorAll('.add-to-cart').forEach(btn => {
      btn.addEventListener('click', async () => {
        const payload = new URLSearchParams();
        payload.append('id', btn.dataset.id);
        payload.append('qty', '1');

        const res = await fetch((window.APP_BASE || '/') + 'cart/add', {
          method: 'POST',
          headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
          body: payload.toString()
        });
        const data = await res.json().catch(() => ({ ok: false }));

        if (data.ok) {
          if (typeof data.cart_count !== 'undefined') {
            setCartCount(data.cart_count);
          } else {
            refreshCartCount();
          }
          alert('Ditambahkan ke keranjang: ' + btn.dataset.name);
        } else {
          alert(data.msg || 'Gagal menambah.');
          refreshCartCount();
        }
      });
    });
  }

  async function doSearch(q) {
    try {
      const url = (window.APP_BASE || '/') + 'menu/search?q=' + encodeURIComponent(q || '');
      const res = await fetch(url, { credentials: 'same-origin' });
      const data = await res.json();
      if (data && data.ok) render(data.items);
    } catch (e) {
      // Pencarian tambahan ini boleh gagal diam-diam.
    }
  }

  doSearch('');
  input.addEventListener('input', debounce(() => doSearch(input.value.trim()), 350));
})();
