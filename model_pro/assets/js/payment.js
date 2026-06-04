// ===== BANAVI - PAYMENT & CHECKOUT LOGIC =====

function getSelectedPaymentMethod() {
  const selected = document.querySelector('.pay-method.sel input');
  return selected ? selected.value : 'COD';
}

function updatePaymentTotals() {
  const sub = cart.reduce((s, c) => s + c.price * c.qty, 0);
  const discount = promoApplied ? Math.round(sub * promoDiscount) : 0;
  const total = Math.max(sub - discount, 0);

  document.getElementById('paySub').textContent = formatPrice(sub);
  document.getElementById('payDiscount').textContent = discount ? `−${formatPrice(discount)}` : '−₹0';
  document.getElementById('payTotal').textContent = formatPrice(total);
}

// ===== RENDER PAYMENT PAGE =====
function renderPayPage() {
  const items = document.getElementById('payItems');
  
  if (!cart.length) {
    items.innerHTML = '<p style="color:var(--muted);font-size:14px">No items in cart. <a href="#" onclick="goTo(\'shop\')" style="color:var(--rose-dark)">Shop now</a></p>';
    document.getElementById('paymentForm').style.display = 'none';
    document.getElementById('orderSuccess').style.display = 'none';
    return;
  }
  
  items.innerHTML = cart.map(c => `
    <div class="order-summary-item">
      <div class="osi-img">${c.emoji}</div>
      <div class="osi-info">
        <div class="osi-name">${c.name}</div>
        <div class="osi-meta">Size: ${c.size} &nbsp;×${c.qty}</div>
      </div>
      <div class="osi-price">${formatPrice(c.price * c.qty)}</div>
    </div>
  `).join('');
  
  promoApplied = false;
  promoDiscount = 0;
  document.getElementById('promoInput').value = '';
  document.getElementById('payDiscount').textContent = '−₹0';
  updatePaymentTotals();
  
  document.getElementById('paymentForm').style.display = 'block';
  document.getElementById('orderSuccess').style.display = 'none';
}

// ===== SELECT PAYMENT METHOD =====
function selPay(el) {
  document.querySelectorAll('.pay-method').forEach(m => m.classList.remove('sel'));
  el.classList.add('sel');
  const input = el.querySelector('input');
  if (input) input.checked = true;
}

// ===== APPLY PROMO CODE =====
function applyPromo() {
  const code = document.getElementById('promoInput').value.trim().toUpperCase();
  const validCodes = { 'BANAVI15': 0.15 };
  
  if (validCodes[code]) {
    promoApplied = true;
    promoDiscount = validCodes[code];
    updatePaymentTotals();
    showToast('🎉 15% discount applied!');
  } else {
    promoApplied = false;
    promoDiscount = 0;
    updatePaymentTotals();
    showToast('❌ Invalid promo code');
  }
}

// ===== PLACE ORDER =====
async function placeOrder() {
  if (!cart.length) {
    showToast('❌ Your cart is empty!');
    return;
  }
  
  const name = document.getElementById('payName').value.trim();
  const phone = document.getElementById('payPhone').value.trim();
  const addr1 = document.getElementById('payAddr1').value.trim();
  const addr2 = document.querySelector('#paymentForm input[placeholder="Area, Landmark (optional)"]').value.trim();
  const city = document.getElementById('payCity').value.trim();
  const pin = document.getElementById('payPin').value.trim();
  const state = document.querySelector('#paymentForm select').value;
  
  if (!name || !phone || !addr1 || !city || !pin) {
    showToast('❌ Please fill all required fields');
    return;
  }
  
  const subtotal = cart.reduce((s, c) => s + c.price * c.qty, 0);
  const discount = promoApplied ? Math.round(subtotal * promoDiscount) : 0;
  const total = Math.max(subtotal - discount, 0);
  const paymentMethod = getSelectedPaymentMethod();
  
  const payload = {
    customer_name: name,
    customer_email: currentUser?.email || '',
    customer_phone: phone,
    shipping_address: addr1 + (addr2 ? ', ' + addr2 : ''),
    city,
    state,
    pincode: pin,
    total_amount: subtotal,
    discount_amount: discount,
    final_amount: total,
    payment_method: paymentMethod,
    user_id: currentUser?.id || null,
    items: cart.map(c => ({ id: c.id, name: c.name, size: c.size, qty: c.qty, price: c.price }))
  };
  
  try {
    const response = await apiPost('orders.php?action=create', payload);
    if (!response.success) {
      showToast(response.message || '❌ Order failed');
      return;
    }
    const orderNumber = response.data?.order_number || 'BNV-' + Date.now().toString().slice(-6);
    
    cart = [];
    updateCartBadge();
    renderCartPanel();
    
    document.getElementById('successOrderId').textContent = orderNumber;
    document.getElementById('paymentForm').style.display = 'none';
    document.getElementById('orderSuccess').style.display = 'block';
    
    showToast('🎉 Order placed successfully!');
  } catch (error) {
    showToast('❌ Payment failed. Try again.');
  }
}
