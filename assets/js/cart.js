// ===== BANAVI - SHOPPING CART LOGIC =====

// ===== ADD TO CART =====
function addCart(id) {
  const p = products.find(x => x.id === id);
  const size = selSizes[id] || p.sizes[0];
  const ex = cart.find(c => c.id === id && c.size === size);
  
  if (ex) {
    ex.qty++;
  } else {
    cart.push({ ...p, size, qty: 1 });
  }
  
  updateCartBadge();
  renderCartPanel();
  showToast(`🛍️ ${p.name} added!`);
}

// ===== UPDATE CART BADGE =====
function updateCartBadge() {
  const b = document.getElementById('cartBadge');
  const t = cart.reduce((s, c) => s + c.qty, 0);
  b.textContent = t;
  b.style.display = t > 0 ? 'flex' : 'none';
}

// ===== RENDER CART PANEL =====
function renderCartPanel() {
  const body = document.getElementById('cartBody');
  const foot = document.getElementById('cartFoot');
  const empty = document.getElementById('cartEmpty');
  
  if (!cart.length) {
    body.innerHTML = '';
    body.appendChild(empty);
    empty.style.display = 'block';
    foot.style.display = 'none';
    return;
  }
  
  empty.style.display = 'none';
  foot.style.display = 'block';
  
  body.innerHTML = cart.map((c, i) => `
    <div class="cart-item">
      <div class="ci-img">${c.emoji}</div>
      <div class="ci-info">
        <div class="ci-name">${c.name}</div>
        <div class="ci-meta">Size: ${c.size}</div>
        <div class="ci-actions">
          <button class="qty-btn" onclick="chgQty(${i},-1)">−</button>
          <span class="qty-num">${c.qty}</span>
          <button class="qty-btn" onclick="chgQty(${i},1)">+</button>
          <button class="rm-btn" onclick="rmItem(${i})">Remove</button>
        </div>
      </div>
      <div class="ci-price">₹${(c.price * c.qty).toLocaleString('en-IN')}</div>
    </div>
  `).join('');
  
  const sub = cart.reduce((s, c) => s + c.price * c.qty, 0);
  document.getElementById('cSub').textContent = '₹' + sub.toLocaleString('en-IN');
  document.getElementById('cTotal').textContent = '₹' + sub.toLocaleString('en-IN');
}

// ===== CHANGE QUANTITY =====
function chgQty(i, d) {
  cart[i].qty += d;
  if (cart[i].qty <= 0) cart.splice(i, 1);
  updateCartBadge();
  renderCartPanel();
}

// ===== REMOVE ITEM FROM CART =====
function rmItem(i) {
  cart.splice(i, 1);
  updateCartBadge();
  renderCartPanel();
}

// ===== TOGGLE CART PANEL =====
function toggleCart() {
  document.getElementById('cartPanel').classList.toggle('open');
  document.getElementById('cartOverlay').classList.toggle('open');
}
