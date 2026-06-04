// ===== BANAVI - PROFILE & AUTH LOGIC =====

async function renderOrderHistory() {
  const list = document.getElementById('orderHistoryList');
  if (!list) return;

  let orders = [...mockOrders];
  if (currentUser) {
    try {
      const response = await apiGet('orders.php?action=user', { user_id: currentUser.id });
      if (response.success && Array.isArray(response.data) && response.data.length) {
        orders = response.data.map(order => ({
          id: order.order_number,
          date: new Date(order.created_at).toLocaleDateString('en-IN', { day: '2-digit', month: 'short', year: 'numeric' }),
          status: order.order_status,
          items: [],
          total: Number(order.final_amount)
        }));
      }
    } catch (error) {
      showToast('⚠️ Could not load order history');
    }
  }

  const statusClasses = {
    'Delivered': 'status-delivered',
    'Shipped': 'status-shipped',
    'Processing': 'status-processing',
    'Cancelled': 'status-processing'
  };

  list.innerHTML = orders.map(o => `
    <div class="order-card">
      <div class="order-header">
        <div>
          <div class="order-id-text">${o.id}</div>
          <div class="order-date">${o.date}</div>
        </div>
        <span class="status-badge ${statusClasses[o.status] || 'status-processing'}">${o.status}</span>
      </div>
      <div class="order-items">
        ${o.items.map(it => `
          <div class="order-item-row">
            <div class="oir-img">${it.emoji || '🛍️'}</div>
            <div class="oir-info">
              <div class="oir-name">${it.name}</div>
              <div class="oir-meta">Size: ${it.size || 'Free'} × ${it.qty || 1}</div>
            </div>
            <div class="oir-price">${formatPrice(it.price || 0)}</div>
          </div>
        `).join('')}
      </div>
      <div class="order-footer">
        <div class="order-total">Total: ${formatPrice(o.total)}</div>
        <button class="reorder-btn" onclick="showToast('🔄 Items added to cart!')">Reorder</button>
      </div>
    </div>
  `).join('');
}

function renderWishlist() {
  const grid = document.getElementById('wishlistGrid');
  const empty = document.getElementById('wishEmpty');
  if (!grid) return;

  const wp = products.filter(p => wishlist.has(p.id));
  if (!wp.length) {
    grid.innerHTML = '';
    empty.style.display = 'block';
    return;
  }

  empty.style.display = 'none';
  grid.innerHTML = wp.map(p => `
    <div class="wish-card">
      <div class="wish-img"><span style="font-size:36px">${p.emoji}</span></div>
      <div class="wish-info">
        <div class="wish-name">${p.name}</div>
        <div class="wish-price">${formatPrice(p.price)}</div>
        <button class="add-btn btn-full" onclick="addCart(${p.id})">Add to Bag</button>
      </div>
    </div>
  `).join('');
}

function showProfileSection(sec, li) {
  document.querySelectorAll('.profile-section').forEach(s => s.classList.remove('active'));
  document.getElementById('ps-' + sec).classList.add('active');

  if (li) {
    document.querySelectorAll('.profile-nav li').forEach(l => l.classList.remove('active'));
    li.classList.add('active');
  }

  if (sec === 'orders') renderOrderHistory();
  if (sec === 'wishlist') renderWishlist();
}

async function saveProfile() {
  const fn = document.getElementById('pFirstName').value.trim();
  const ln = document.getElementById('pLastName').value.trim();
  const em = document.getElementById('pEmail').value.trim();
  const dob = document.getElementById('pDob').value;
  const gender = document.querySelector('#ps-info select').value;

  if (!fn || !ln || !em) {
    showToast('❌ Please complete your profile');
    return;
  }

  const full = fn + ' ' + ln;
  document.getElementById('profileNameDisplay').textContent = full;
  document.getElementById('profileBigName').textContent = full;
  document.getElementById('profileEmailDisplay').textContent = em;
  document.getElementById('profileBigEmail').textContent = em;
  document.getElementById('profileAvatar').textContent = fn[0] || 'P';
  document.getElementById('bigAvatar').textContent = fn[0] || 'P';

  if (currentUser) {
    try {
      const response = await apiPut('auth.php?action=update-profile', {
        first_name: fn,
        last_name: ln,
        phone: document.getElementById('pPhone').value.trim(),
        date_of_birth: dob,
        gender
      });
      if (response.success) {
        currentUser = currentUser || {};
        currentUser.first_name = fn;
        currentUser.last_name = ln;
        currentUser.phone = document.getElementById('pPhone').value.trim();
        currentUser.date_of_birth = dob;
        currentUser.gender = gender;
        localStorage.setItem('banaviUser', JSON.stringify(currentUser));
        refreshProfileUI();
        showToast('✅ Profile updated successfully!');
      } else {
        showToast(response.message || '⚠️ Profile update failed');
      }
    } catch (error) {
      showToast('⚠️ Unable to save profile');
    }
  } else {
    showToast('✅ Profile updated locally');
  }
}

function switchAuth(tab, el) {
  document.querySelectorAll('.auth-tab').forEach(t => t.classList.remove('active'));
  el.classList.add('active');
  document.querySelectorAll('.auth-form').forEach(f => f.classList.remove('active'));
  document.getElementById('af-' + tab).classList.add('active');
}

async function doLogin() {
  const email = document.getElementById('loginEmail').value.trim();
  const password = document.getElementById('loginPassword').value.trim();

  if (!email || !password) {
    showToast('❌ Enter email and password');
    return;
  }

  try {
    const response = await apiPost('auth.php?action=login', { email, password });
    if (response.success) {
      currentUser = response.data;
      localStorage.setItem('banaviUser', JSON.stringify(currentUser));
      refreshProfileUI();
      showToast('✅ Welcome back, ' + currentUser.first_name + '!');
      setTimeout(() => goTo('home'), 800);
    } else {
      showToast(response.message || ' Login failed');
    }
  } catch (error) {
    showToast(' Unable to sign in');
  }
}

async function doSignup() {
  const firstName = document.getElementById('signupFirstName').value.trim();
  const lastName = document.getElementById('signupLastName').value.trim();
  const email = document.getElementById('signupEmail').value.trim();
  const phone = document.getElementById('signupPhone').value.trim();
  const password = document.getElementById('signupPassword').value.trim();

  if (!firstName || !lastName || !email || !phone || !password) {
    showToast('❌ Fill all signup fields');
    return;
  }

  try {
    const response = await apiPost('auth.php?action=signup', {
      first_name: firstName,
      last_name: lastName,
      email,
      phone,
      password
    });
    if (response.success) {
      currentUser = response.data;
      localStorage.setItem('banaviUser', JSON.stringify(currentUser));
      refreshProfileUI();
      showToast('🎉 Account created successfully');
      setTimeout(() => goTo('home'), 800);
    } else {
      showToast(response.message || '❌ Signup failed');
    }
  } catch (error) {
    showToast('⚠️ Unable to create account');
  }
}
