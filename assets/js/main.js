// ===== BANAVI - MAIN APPLICATION LOGIC =====

// Global State
let cart = [];
let wishlist = new Set();
let selSizes = {};
let currentFilter = 'All';
let currentPage = 'home';
let currentUser = null;
let products = [];
let promoApplied = false;
let promoDiscount = 0;

// Fallback Product Catalog
const fallbackProducts = [
  { id: 1, name: 'Floral Block Print Kurti', cat: 'Kurti', price: 899, old: 1299, emoji: '👗', badge: 'Sale', sizes: ['S', 'M', 'L', 'XL'] },
  { id: 2, name: 'Banarasi Silk Saree', cat: 'Saree', price: 3499, old: null, emoji: '🥻', badge: 'New', sizes: ['Free'] },
  { id: 3, name: 'Embroidered Lehenga Set', cat: 'Lehenga', price: 5999, old: 7999, emoji: '👘', badge: 'Sale', sizes: ['S', 'M', 'L'] },
  { id: 4, name: 'Cotton Printed Crop Top', cat: 'Tops', price: 499, old: null, emoji: '👚', badge: 'New', sizes: ['XS', 'S', 'M', 'L'] },
  { id: 5, name: 'Chanderi Palazzo Kurti', cat: 'Kurti', price: 1199, old: null, emoji: '👗', badge: null, sizes: ['S', 'M', 'L', 'XL', 'XXL'] },
  { id: 6, name: 'Georgette Party Saree', cat: 'Saree', price: 2899, old: 3999, emoji: '🥻', badge: 'Sale', sizes: ['Free'] },
  { id: 7, name: 'Mirror Work Kurti', cat: 'Kurti', price: 1599, old: null, emoji: '👗', badge: 'New', sizes: ['S', 'M', 'L'] },
  { id: 8, name: 'Co-ord Set Printed', cat: 'Tops', price: 1099, old: 1499, emoji: '👚', badge: 'Sale', sizes: ['XS', 'S', 'M'] },
  { id: 9, name: 'Rajwadi Lehenga Choli', cat: 'Lehenga', price: 8499, old: null, emoji: '👘', badge: 'New', sizes: ['S', 'M', 'L'] },
  { id: 10, name: 'Ikat Fusion Kurti', cat: 'Kurti', price: 749, old: 999, emoji: '👗', badge: 'Sale', sizes: ['M', 'L', 'XL'] },
  { id: 11, name: 'Pure Mysore Silk Saree', cat: 'Saree', price: 6999, old: null, emoji: '🥻', badge: null, sizes: ['Free'] },
  { id: 12, name: 'Embroidered Peplum Top', cat: 'Tops', price: 699, old: null, emoji: '👚', badge: 'New', sizes: ['XS', 'S', 'M', 'L'] }
];

const mockOrders = [
  { id: 'BNV-240501', date: '01 May 2025', status: 'Delivered', items: [{ name: 'Floral Block Print Kurti', emoji: '👗', size: 'M', qty: 1, price: 899 }, { name: 'Cotton Printed Crop Top', emoji: '👚', size: 'S', qty: 2, price: 998 }], total: 1897 },
  { id: 'BNV-240418', date: '18 Apr 2025', status: 'Delivered', items: [{ name: 'Banarasi Silk Saree', emoji: '🥻', size: 'Free', qty: 1, price: 3499 }], total: 3499 },
  { id: 'BNV-240510', date: '10 May 2025', status: 'Shipped', items: [{ name: 'Mirror Work Kurti', emoji: '👗', size: 'L', qty: 1, price: 1599 }], total: 1599 },
  { id: 'BNV-240525', date: '25 May 2025', status: 'Processing', items: [{ name: 'Rajwadi Lehenga Choli', emoji: '👘', size: 'M', qty: 1, price: 8499 }], total: 8499 }
];

// ===== DATA LOAD =====
async function loadProducts() {
  try {
    const response = await apiGet('products.php?action=all');
    if (response.success && Array.isArray(response.data) && response.data.length) {
      products = response.data.map(mapProduct);
      return;
    }
    showToast('⚠️ Backend products unavailable. Loaded demo data.');
  } catch (error) {
    showToast('⚠️ Backend unavailable. Using local demo data.');
  }
  loadFallbackProducts();
}

function mapProduct(row) {
  return {
    id: Number(row.id),
    name: row.name,
    cat: row.category || row.cat,
    price: Number(row.price),
    old: row.original_price ? Number(row.original_price) : row.old ? Number(row.old) : null,
    emoji: row.emoji || '👗',
    badge: row.badge || null,
    sizes: row.sizes ? row.sizes.split(',') : row.sizes || ['Free']
  };
}

function loadFallbackProducts() {
  if (products.length) return;
  products = fallbackProducts.map(p => ({ ...p }));
}

function loadAuthState() {
  try {
    const stored = localStorage.getItem('banaviUser');
    currentUser = stored ? JSON.parse(stored) : null;
  } catch (error) {
    currentUser = null;
  }
}

function refreshProfileUI() {
  if (!currentUser) return;
  const fullName = `${currentUser.first_name || ''} ${currentUser.last_name || ''}`.trim();
  const first = currentUser.first_name || '';

  document.getElementById('profileNameDisplay').textContent = fullName || 'Banavi Shopper';
  document.getElementById('profileEmailDisplay').textContent = currentUser.email || '';
  document.getElementById('profileBigName').textContent = fullName || 'Banavi Shopper';
  document.getElementById('profileBigEmail').textContent = currentUser.email || '';
  document.getElementById('profileAvatar').textContent = (first[0] || 'B').toUpperCase();
  document.getElementById('bigAvatar').textContent = (first[0] || 'B').toUpperCase();

  document.getElementById('pFirstName').value = currentUser.first_name || '';
  document.getElementById('pLastName').value = currentUser.last_name || '';
  document.getElementById('pEmail').value = currentUser.email || '';
  document.getElementById('pPhone').value = currentUser.phone || '';
  document.getElementById('pDob').value = currentUser.date_of_birth || '';
  const genderEl = document.querySelector('#ps-info select');
  if (genderEl && currentUser.gender) {
    genderEl.value = currentUser.gender;
  }
}

// ===== PAGE NAVIGATION =====
function goTo(page) {
  document.querySelectorAll('.page').forEach(p => p.classList.remove('active'));
  const target = document.getElementById('page-' + page);
  if (target) target.classList.add('active');

  document.querySelectorAll('.nav-links a').forEach(a => a.classList.remove('active'));
  const navEl = document.getElementById('nav-' + page);
  if (navEl) navEl.classList.add('active');

  currentPage = page;
  if (page === 'payment') renderPayPage();
  if (page === 'profile') renderWishlist();
  if (page === 'shop') renderShopGrid();
  if (page === 'home') renderFeatured();

  window.scrollTo(0, 0);
}

// ===== PRODUCT RENDERING =====
function renderProductCard(p) {
  const inW = wishlist.has(p.id);
  return `
    <div class="prod-card">
      <div class="prod-img">
        ${p.badge ? `<span class="prod-badge ${p.badge.toLowerCase()}">${p.badge}</span>` : ''}
        <button class="prod-wishlist ${inW ? 'active' : ''}" id="w${p.id}" onclick="toggleWish(${p.id})">♡</button>
        <span style="font-size:46px">${p.emoji}</span>
      </div>
      <div class="prod-info">
        <div class="prod-cat">${p.cat}</div>
        <div class="prod-name">${p.name}</div>
        <div class="prod-sizes">${p.sizes.map(s => `<span class="size-tag" onclick="selSize(${p.id},'${s}',this)">${s}</span>`).join('')}</div>
        <div class="prod-bottom">
          <div class="prod-price">${formatPrice(p.price)}${p.old ? `<span class="old">${formatPrice(p.old)}</span>` : ''}</div>
          <button class="add-btn" onclick="addCart(${p.id})">Add +</button>
        </div>
      </div>
    </div>
  `;
}

function renderFeatured() {
  const g = document.getElementById('featuredGrid');
  if (g) g.innerHTML = products.slice(0, 8).map(p => renderProductCard(p)).join('');
}

function renderShopGrid() {
  const fp = currentFilter === 'All' ? products : currentFilter === 'Sale' ? products.filter(p => p.badge === 'Sale') : products.filter(p => p.cat === currentFilter);
  const g = document.getElementById('shopGrid');
  if (g) g.innerHTML = fp.map(p => renderProductCard(p)).join('');
}

function setFilter(cat, btn) {
  currentFilter = cat;
  if (btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');
  }
  renderShopGrid();
  if (currentPage !== 'shop') goTo('shop');
}

// ===== SIZE SELECTION =====
function selSize(id, s, el) {
  if (!el) return;
  el.closest('.prod-sizes').querySelectorAll('.size-tag').forEach(x => x.classList.remove('sel'));
  el.classList.add('sel');
  selSizes[id] = s;
}

// ===== WISHLIST =====
function toggleWish(id) {
  if (wishlist.has(id)) {
    wishlist.delete(id);
    showToast('💔 Removed from wishlist');
  } else {
    wishlist.add(id);
    showToast('❤️ Added to wishlist!');
  }

  document.querySelectorAll('#w' + id).forEach(b => {
    b.classList.toggle('active', wishlist.has(id));
  });

  if (currentPage === 'profile') renderWishlist();
}

// ===== NOTIFICATIONS =====
function showToast(msg) {
  const t = document.getElementById('toast');
  if (!t) return;
  t.textContent = msg;
  t.classList.add('show');
  setTimeout(() => t.classList.remove('show'), 2500);
}

// ===== INITIALIZATION =====
document.addEventListener('DOMContentLoaded', async function() {
  loadAuthState();
  await loadProducts();
  refreshProfileUI();
  renderFeatured();
  renderShopGrid();
  renderCartPanel();
});
