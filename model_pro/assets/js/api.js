// ===== BANAVI - BACKEND API HELPERS =====

const API_BASE = 'backend/api';

function buildUrl(path, params = {}) {
  const query = new URLSearchParams(params).toString();
  return `${API_BASE}/${path}${query ? '?' + query : ''}`;
}

async function apiGet(path, params = {}) {
  const url = buildUrl(path, params);
  const response = await fetch(url, { credentials: 'same-origin' });
  return await response.json();
}

async function apiPost(path, payload = {}) {
  const url = buildUrl(path);
  const response = await fetch(url, {
    method: 'POST',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  return await response.json();
}

async function apiPut(path, payload = {}) {
  const url = buildUrl(path);
  const response = await fetch(url, {
    method: 'PUT',
    credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload)
  });
  return await response.json();
}

async function apiDelete(path, params = {}) {
  const url = buildUrl(path, params);
  const response = await fetch(url, { method: 'DELETE', credentials: 'same-origin' });
  return await response.json();
}

function formatPrice(value) {
  return '₹' + Number(value).toLocaleString('en-IN');
}
