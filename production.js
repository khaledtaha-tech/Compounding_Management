const state = {
  records: [],
  equipment: { mixers: [], pelletizers: [] },
  recipes: [],
  filterDate: '',
  searchQuery: '',
  showAll: false,
  editingId: null
};

const $ = (id) => document.getElementById(id);
const form = $('productionForm');
const recordsBody = $('recordsBody');
const emptyState = $('emptyState');


async function apiFetch(url, options = {}) {
  const headers = new Headers(options.headers || {});
  const response = await fetch(url, { ...options, headers, credentials: 'same-origin' });
  if (response.status === 401) {
    window.location.href = 'login.php';
    throw new Error('Your session has expired.');
  }
  return response;
}

function todayISO() {
  const now = new Date();
  const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
  return local.toISOString().slice(0, 10);
}

function yesterdayISO() {
  const now = new Date();
  now.setDate(now.getDate() - 1);
  const local = new Date(now.getTime() - now.getTimezoneOffset() * 60000);
  return local.toISOString().slice(0, 10);
}

function currentMonthISO() {
  return todayISO().slice(0, 7);
}

function formatDate(dateString) {
  if (!dateString) return '';
  const [year, month, day] = dateString.split('-');
  return `${day}/${month}/${year}`;
}

function round2(value) {
  return Math.round((Number(value) + Number.EPSILON) * 100) / 100;
}

function formatKg(value) {
  return `${round2(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })} kg`;
}

function escapeHtml(value) {
  return String(value ?? '')
    .replaceAll('&', '&amp;')
    .replaceAll('<', '&lt;')
    .replaceAll('>', '&gt;')
    .replaceAll('"', '&quot;')
    .replaceAll("'", '&#039;');
}

const THEMES = {
  arctic: { mode: 'light', color: '#2563eb' },
  indigo: { mode: 'light', color: '#4f46e5' },
  violet: { mode: 'light', color: '#8b5cf6' },
  midnight: { mode: 'dark', color: '#0b1220' },
  graphite: { mode: 'dark', color: '#14131b' }
};

function setTheme(theme) {
  const selected = THEMES[theme] ? theme : 'arctic';
  document.documentElement.dataset.theme = selected;
  localStorage.setItem('production-theme', selected);
  if ($('themeSelect')) $('themeSelect').value = selected;
  const meta = $('themeColorMeta');
  if (meta) meta.setAttribute('content', THEMES[selected].color);
}

function initTheme() {
  let saved = localStorage.getItem('production-theme');
  if (saved === 'light') saved = 'arctic';
  if (saved === 'dark') saved = 'midnight';
  if (saved && THEMES[saved]) return setTheme(saved);
  const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
  setTheme(prefersDark ? 'midnight' : 'arctic');
}

function equipmentListForType(type) {
  return type === 'Mixer' ? state.equipment.mixers : state.equipment.pelletizers;
}

function historyValues(selector) {
  const seen = new Set();
  const values = [];
  for (const record of state.records) {
    const value = String(selector(record) || '').trim();
    const key = value.toLowerCase();
    if (!value || seen.has(key)) continue;
    seen.add(key);
    values.push(value);
  }
  return values;
}

function fillHistoryList(id, values) {
  $(id).innerHTML = values.map((value) => `<option value="${escapeHtml(value)}"></option>`).join('');
}

function renderHistorySuggestions() {
  const unique = (values) => [...new Map(values.filter(Boolean).map((value) => [String(value).trim().toLowerCase(), String(value).trim()])).values()];
  fillHistoryList('mixCodeHistory', unique([...historyValues((r) => r.type === 'Mixer' ? r.mixCode : ''), ...state.recipes.map((r) => r.code)]));
  fillHistoryList('mixNameHistory', unique([...historyValues((r) => r.type === 'Mixer' ? r.mixName : ''), ...state.recipes.map((r) => r.name)]));
  fillHistoryList('applicationHistory', historyValues((r) => r.type === 'Pelletizer' ? r.application : ''));
  fillHistoryList('colorHistory', historyValues((r) => r.color));
}

async function loadRecipeSuggestions() {
  try {
    const response = await apiFetch('api.php?action=material_state');
    const data = await response.json();
    state.recipes = Array.isArray(data?.state?.recipes) ? data.state.recipes : [];
    renderHistorySuggestions();
  } catch (error) {
    console.warn('Recipe suggestions unavailable:', error);
  }
}

function showHistoryPicker(input) {
  try {
    if (typeof input.showPicker === 'function') input.showPicker();
  } catch (_) {}
}

function normalizeQuantityInput() {
  const input = $('quantityKg');
  if (!input.value) return;
  const value = Number(input.value);
  if (!Number.isFinite(value)) return;
  input.value = round2(value).toFixed(2);
}

function renderEquipmentOptions() {
  const currentMixer = $('mixerId').value;
  const currentPelletizer = $('pelletizerId').value;

  $('mixerId').innerHTML = state.equipment.mixers
    .map((item) => `<option value="${escapeHtml(item.id)}">${escapeHtml(item.name)}</option>`)
    .join('');

  $('pelletizerId').innerHTML = state.equipment.pelletizers
    .map((item) => `<option value="${escapeHtml(item.id)}">${escapeHtml(item.name)}</option>`)
    .join('');

  if (state.equipment.mixers.some((item) => item.id === currentMixer)) $('mixerId').value = currentMixer;
  if (state.equipment.pelletizers.some((item) => item.id === currentPelletizer)) $('pelletizerId').value = currentPelletizer;

  renderEquipmentLists();
}

function renderEquipmentLists() {
  const buildList = (items, type) => {
    if (!items.length) return '<p class="equipment-empty">No equipment added.</p>';
    return items.map((item) => `
      <div class="equipment-item">
        <span>${escapeHtml(item.name)}</span>
        <button class="equipment-remove" type="button" data-remove-equipment="${escapeHtml(item.id)}" data-equipment-type="${type}">Remove</button>
      </div>
    `).join('');
  };

  $('mixerEquipmentList').innerHTML = buildList(state.equipment.mixers, 'Mixer');
  $('pelletizerEquipmentList').innerHTML = buildList(state.equipment.pelletizers, 'Pelletizer');
}

async function loadEquipment() {
  try {
    const response = await apiFetch('api.php?action=equipment');
    if (!response.ok) throw new Error('Failed to load equipment');
    const data = await response.json();
    state.equipment.mixers = Array.isArray(data.mixers) ? data.mixers : [];
    state.equipment.pelletizers = Array.isArray(data.pelletizers) ? data.pelletizers : [];
    renderEquipmentOptions();
  } catch (error) {
    console.error(error);
    setMessage('Unable to load equipment setup.', 'error');
  }
}

function setProductionType(type) {
  $('productionType').value = type;
  document.querySelectorAll('.segment').forEach((button) => {
    button.classList.toggle('active', button.dataset.type === type);
  });

  const isMixer = type === 'Mixer';
  $('mixerFields').classList.toggle('hidden', !isMixer);
  $('pelletizerFields').classList.toggle('hidden', isMixer);
  $('mixerId').required = isMixer;
  $('mixCode').required = isMixer;
  $('mixName').required = isMixer;
  $('pelletizerId').required = !isMixer;
  $('application').required = !isMixer;
}

function setMessage(message = '', type = '') {
  const el = $('formMessage');
  el.textContent = message;
  el.className = `form-message ${type}`.trim();
}

function resetForm() {
  state.editingId = null;
  $('recordId').value = '';
  form.reset();
  $('date').value = state.filterDate || todayISO();
  $('shift').value = 'Morning';
  setProductionType('Mixer');
  $('formTitle').textContent = 'Add Production Record';
  $('saveButton').textContent = 'Save Record';
  $('cancelEdit').classList.add('hidden');
  setMessage('');
  renderEquipmentOptions();
}

function ensureSelectHasLegacyOption(select, id, name) {
  if (!id || !name) return;
  if ([...select.options].some((option) => option.value === id)) return;
  const option = document.createElement('option');
  option.value = id;
  option.textContent = `${name} (historical)`;
  select.appendChild(option);
}

function editRecord(id) {
  const record = state.records.find((item) => item.id === id);
  if (!record) return;

  state.editingId = id;
  $('recordId').value = id;
  setProductionType(record.type);
  $('date').value = record.date;
  $('shift').value = record.shift;
  $('color').value = record.color || '';
  $('quantityKg').value = round2(record.quantityKg).toFixed(2);
  $('mixCode').value = record.mixCode || '';
  $('mixName').value = record.mixName || '';
  $('application').value = record.application || '';

  if (record.type === 'Mixer') {
    const legacyId = record.mixerId || `legacy-${record.id}`;
    ensureSelectHasLegacyOption($('mixerId'), legacyId, record.mixerName || 'Mixer');
    $('mixerId').value = legacyId;
  } else {
    const legacyId = record.pelletizerId || `legacy-${record.id}`;
    ensureSelectHasLegacyOption($('pelletizerId'), legacyId, record.pelletizerName || 'Pelletizer');
    $('pelletizerId').value = legacyId;
  }

  $('formTitle').textContent = 'Edit Production Record';
  $('saveButton').textContent = 'Update Record';
  $('cancelEdit').classList.remove('hidden');
  setMessage('Editing selected record.');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

function duplicateRecord(id) {
  const record = state.records.find((item) => item.id === id);
  if (!record) return;

  state.editingId = null;
  $('recordId').value = '';
  setProductionType(record.type);
  $('date').value = record.date;
  $('shift').value = record.shift;
  $('color').value = record.color || '';
  $('quantityKg').value = round2(record.quantityKg).toFixed(2);
  $('mixCode').value = record.mixCode || '';
  $('mixName').value = record.mixName || '';
  $('application').value = record.application || '';

  if (record.type === 'Mixer') {
    const equipmentId = record.mixerId || `legacy-${record.id}`;
    ensureSelectHasLegacyOption($('mixerId'), equipmentId, record.mixerName || 'Mixer');
    $('mixerId').value = equipmentId;
  } else {
    const equipmentId = record.pelletizerId || `legacy-${record.id}`;
    ensureSelectHasLegacyOption($('pelletizerId'), equipmentId, record.pelletizerName || 'Pelletizer');
    $('pelletizerId').value = equipmentId;
  }

  $('formTitle').textContent = 'Duplicate Production Record';
  $('saveButton').textContent = 'Save Duplicate';
  $('cancelEdit').classList.remove('hidden');
  setMessage('Duplicate loaded. Review the values, then save it as a new record.');
  window.scrollTo({ top: 0, behavior: 'smooth' });
}

async function deleteRecord(id) {
  const record = state.records.find((item) => item.id === id);
  if (!record) return;
  if (!confirm(`Delete this ${record.type.toLowerCase()} production record?`)) return;

  const response = await apiFetch(`api.php?action=records&id=${encodeURIComponent(id)}`, { method: 'DELETE' });
  if (!response.ok) {
    alert('Could not delete the record.');
    return;
  }
  if (state.editingId === id) resetForm();
  await loadRecords();
}

function visibleRecords() {
  let records = (state.showAll || !state.filterDate)
    ? state.records
    : state.records.filter((record) => record.date === state.filterDate);

  const query = state.searchQuery.trim().toLowerCase();
  if (!query) return records;

  return records.filter((record) => {
    const searchableText = [
      record.mixerName,
      record.pelletizerName,
      record.mixCode,
      record.mixName,
      record.color,
      record.application
    ]
      .filter(Boolean)
      .join(' ')
      .toLowerCase();

    return searchableText.includes(query);
  });
}

function productionBreakdown(records) {
  const mixer = records
    .filter((record) => record.type === 'Mixer')
    .reduce((sum, record) => sum + Number(record.quantityKg || 0), 0);
  const pelletizer = records
    .filter((record) => record.type === 'Pelletizer')
    .reduce((sum, record) => sum + Number(record.quantityKg || 0), 0);

  return { mixer, pelletizer, total: mixer + pelletizer };
}

function renderSummary(records) {
  const overall = productionBreakdown(records);
  const morning = productionBreakdown(records.filter((record) => record.shift === 'Morning'));
  const night = productionBreakdown(records.filter((record) => record.shift === 'Night' || record.shift === 'Evening'));

  $('dayTotal').textContent = formatKg(overall.total);
  $('mixerTotal').textContent = formatKg(overall.mixer);
  $('pelletizerTotal').textContent = formatKg(overall.pelletizer);
  $('recordCount').textContent = records.length.toLocaleString();
  $('selectedDayLabel').textContent = state.showAll ? 'All dates' : formatDate(state.filterDate);

  $('morningProduction').textContent = formatKg(morning.total);
  $('morningMixer').textContent = formatKg(morning.mixer);
  $('morningPelletizer').textContent = formatKg(morning.pelletizer);

  $('nightProduction').textContent = formatKg(night.total);
  $('nightMixer').textContent = formatKg(night.mixer);
  $('nightPelletizer').textContent = formatKg(night.pelletizer);

  $('shiftSummaryTotal').textContent = formatKg(overall.total);
  $('shiftSummaryMixer').textContent = formatKg(overall.mixer);
  $('shiftSummaryPelletizer').textContent = formatKg(overall.pelletizer);
}

function recordEquipmentName(record) {
  return record.type === 'Mixer'
    ? (record.mixerName || 'Mixer')
    : (record.pelletizerName || 'Pelletizer');
}

function recordMixDetail(record) {
  if (record.type === 'Mixer') {
    const parts = [record.mixCode, record.mixName].filter(Boolean);
    return parts.length ? parts.join(' — ') : '—';
  }
  return record.application || '—';
}

function renderRecords() {
  const records = visibleRecords();
  renderSummary(records);
  recordsBody.innerHTML = '';
  emptyState.classList.toggle('hidden', records.length !== 0);

  for (const record of records) {
    const row = document.createElement('tr');
    row.innerHTML = `
      <td>${formatDate(record.date)}</td>
      <td><span class="badge">${escapeHtml(record.shift)}</span></td>
      <td>${escapeHtml(record.type)}</td>
      <td><strong>${escapeHtml(recordEquipmentName(record))}</strong></td>
      <td>${escapeHtml(recordMixDetail(record))}</td>
      <td>${escapeHtml(record.color)}</td>
      <td class="qty">${formatKg(record.quantityKg)}</td>
      <td>
        <div class="actions">
          <button class="action-btn" type="button" data-duplicate="${escapeHtml(record.id)}">Duplicate</button>
          <button class="action-btn" type="button" data-edit="${escapeHtml(record.id)}">Edit</button>
          <button class="action-btn delete" type="button" data-delete="${escapeHtml(record.id)}">Delete</button>
        </div>
      </td>
    `;
    recordsBody.appendChild(row);
  }
}

async function loadRecords() {
  try {
    const response = await apiFetch('api.php?action=records');
    if (!response.ok) throw new Error('Failed to load data');
    state.records = await response.json();
    renderHistorySuggestions();
    renderRecords();
  } catch (error) {
    console.error(error);
    recordsBody.innerHTML = '';
    emptyState.classList.remove('hidden');
    emptyState.querySelector('h3').textContent = 'Unable to load production data';
    emptyState.querySelector('p').textContent = 'Check the server connection and try again.';
  }
}

function selectedEquipment(type) {
  const select = type === 'Mixer' ? $('mixerId') : $('pelletizerId');
  const option = select.options[select.selectedIndex];
  return { id: select.value, name: option?.textContent?.replace(' (historical)', '') || '' };
}

form.addEventListener('submit', async (event) => {
  event.preventDefault();
  setMessage('Saving...');

  const type = $('productionType').value;
  const equipment = selectedEquipment(type);
  const payload = {
    type,
    date: $('date').value,
    shift: $('shift').value,
    color: $('color').value.trim(),
    quantityKg: round2($('quantityKg').value),
    mixerId: type === 'Mixer' ? equipment.id : '',
    mixerName: type === 'Mixer' ? equipment.name : '',
    pelletizerId: type === 'Pelletizer' ? equipment.id : '',
    pelletizerName: type === 'Pelletizer' ? equipment.name : '',
    mixCode: type === 'Mixer' ? $('mixCode').value.trim() : '',
    mixName: type === 'Mixer' ? $('mixName').value.trim() : '',
    application: type === 'Pelletizer' ? $('application').value.trim() : ''
  };

  const isEditing = Boolean(state.editingId);
  const url = isEditing ? `api.php?action=records&id=${encodeURIComponent(state.editingId)}` : 'api.php?action=records';
  const method = isEditing ? 'PUT' : 'POST';

  try {
    const response = await apiFetch(url, {
      method,
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    const data = response.status === 204 ? null : await response.json();
    if (!response.ok) throw new Error(data?.error || 'Could not save record.');

    state.filterDate = payload.date;
    state.showAll = false;
    $('filterDate').value = payload.date;
    resetForm();
    await loadRecords();
    setMessage(isEditing ? 'Record updated successfully.' : 'Record saved successfully.', 'success');
  } catch (error) {
    setMessage(error.message, 'error');
  }
});

function openModal(id) {
  $(id).classList.remove('hidden');
  document.body.classList.add('modal-open');
}

function closeModal(id) {
  $(id).classList.add('hidden');
  if (document.querySelectorAll('.modal-backdrop:not(.hidden)').length === 0) {
    document.body.classList.remove('modal-open');
  }
}

function openAddEquipment(type) {
  $('equipmentType').value = type;
  $('addEquipmentTitle').textContent = `Add ${type}`;
  $('equipmentName').placeholder = type === 'Mixer' ? 'e.g. Mixer 6' : 'e.g. Pelletizer 4';
  $('equipmentName').value = '';
  $('equipmentMessage').textContent = '';
  $('equipmentMessage').className = 'form-message';
  openModal('addEquipmentModal');
  setTimeout(() => $('equipmentName').focus(), 0);
}

async function removeEquipment(type, id) {
  const item = equipmentListForType(type).find((entry) => entry.id === id);
  if (!item) return;
  if (!confirm(`Remove "${item.name}" from the equipment list? Historical production records will remain unchanged.`)) return;

  try {
    const response = await apiFetch(`api.php?action=equipment&id=${encodeURIComponent(id)}`, { method: 'DELETE' });
    const data = response.status === 204 ? null : await response.json();
    if (!response.ok) throw new Error(data?.error || 'Could not remove equipment.');
    await loadEquipment();
  } catch (error) {
    alert(error.message);
  }
}

$('equipmentForm').addEventListener('submit', async (event) => {
  event.preventDefault();
  const type = $('equipmentType').value;
  const name = $('equipmentName').value.trim();
  const message = $('equipmentMessage');
  message.textContent = 'Saving...';
  message.className = 'form-message';

  try {
    const response = await apiFetch('api.php?action=equipment', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ type, name })
    });
    const data = await response.json();
    if (!response.ok) throw new Error(data?.error || 'Could not save equipment.');

    await loadEquipment();
    const targetSelect = type === 'Mixer' ? $('mixerId') : $('pelletizerId');
    targetSelect.value = data.id;
    closeModal('addEquipmentModal');
    setProductionType(type);
    setMessage(`${data.name} added and selected.`, 'success');
  } catch (error) {
    message.textContent = error.message;
    message.className = 'form-message error';
  }
});

document.querySelectorAll('.segment').forEach((button) => {
  button.addEventListener('click', () => setProductionType(button.dataset.type));
});

document.querySelectorAll('[data-add-equipment]').forEach((button) => {
  button.addEventListener('click', () => openAddEquipment(button.dataset.addEquipment));
});

recordsBody.addEventListener('click', (event) => {
  const duplicateId = event.target.dataset.duplicate;
  const editId = event.target.dataset.edit;
  const deleteId = event.target.dataset.delete;
  if (duplicateId) duplicateRecord(duplicateId);
  if (editId) editRecord(editId);
  if (deleteId) deleteRecord(deleteId);
});

$('mixerEquipmentList').addEventListener('click', (event) => {
  const id = event.target.dataset.removeEquipment;
  if (id) removeEquipment(event.target.dataset.equipmentType, id);
});

$('pelletizerEquipmentList').addEventListener('click', (event) => {
  const id = event.target.dataset.removeEquipment;
  if (id) removeEquipment(event.target.dataset.equipmentType, id);
});

$('cancelEdit').addEventListener('click', resetForm);
$('recordsSearch').addEventListener('input', (event) => {
  state.searchQuery = event.target.value;
  renderRecords();
});
$('filterDate').addEventListener('change', (event) => {
  state.filterDate = event.target.value;
  state.showAll = false;
  renderRecords();
});
$('showAll').addEventListener('click', () => {
  state.showAll = true;
  renderRecords();
});
$('themeSelect').addEventListener('change', (event) => {
  setTheme(event.target.value);
});

['mixCode', 'mixName', 'application', 'color'].forEach((id) => {
  $(id).addEventListener('click', (event) => showHistoryPicker(event.currentTarget));
  $(id).addEventListener('focus', (event) => showHistoryPicker(event.currentTarget));
});

$('mixCode').addEventListener('change', () => {
  const recipe = state.recipes.find((r) => String(r.code || '').trim().toLowerCase() === $('mixCode').value.trim().toLowerCase());
  if (recipe && !$('mixName').value.trim()) $('mixName').value = recipe.name || '';
});
$('mixName').addEventListener('change', () => {
  const recipe = state.recipes.find((r) => String(r.name || '').trim().toLowerCase() === $('mixName').value.trim().toLowerCase());
  if (recipe && !$('mixCode').value.trim()) $('mixCode').value = recipe.code || '';
});

$('quantityKg').addEventListener('blur', normalizeQuantityInput);
$('quantityKg').addEventListener('change', normalizeQuantityInput);

$('manageEquipment').addEventListener('click', () => openModal('equipmentModal'));
$('closeEquipmentModal').addEventListener('click', () => closeModal('equipmentModal'));
$('closeAddEquipmentModal').addEventListener('click', () => closeModal('addEquipmentModal'));
$('cancelAddEquipment').addEventListener('click', () => closeModal('addEquipmentModal'));

['equipmentModal', 'addEquipmentModal'].forEach((id) => {
  $(id).addEventListener('click', (event) => {
    if (event.target === $(id)) closeModal(id);
  });
});

document.addEventListener('keydown', (event) => {
  if (event.key !== 'Escape') return;
  if (!$('addEquipmentModal').classList.contains('hidden')) closeModal('addEquipmentModal');
  else if (!$('equipmentModal').classList.contains('hidden')) closeModal('equipmentModal');
});

function setReportMessage(message = '', type = '') {
  const el = $('reportMessage');
  el.textContent = message;
  el.className = `report-message ${type}`.trim();
}

function pdfHeader(pdf, title, period) {
  pdf.setFillColor(37, 99, 235);
  pdf.rect(0, 0, pdf.internal.pageSize.getWidth(), 22, 'F');
  pdf.setTextColor(255, 255, 255);
  pdf.setFont('helvetica', 'bold');
  pdf.setFontSize(15);
  pdf.text('Compounding Section', 14, 10);
  pdf.setFontSize(10);
  pdf.text(title, 14, 17);
  pdf.text(period, pdf.internal.pageSize.getWidth() - 14, 17, { align: 'right' });
  pdf.setTextColor(20, 35, 60);
}

function reportRows(records) {
  return records.map((r) => [
    formatDate(r.date), r.shift, r.type, recordEquipmentName(r), recordMixDetail(r), r.color,
    round2(r.quantityKg).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })
  ]);
}

function exportDailyPdf(date) {
  const records = state.records.filter((r) => r.date === date);
  if (!records.length) throw new Error('No production records exist for the selected date.');
  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
  pdfHeader(pdf, 'Daily Production Report', formatDate(date));
  const totals = productionBreakdown(records);
  pdf.setFontSize(10);
  pdf.setFont('helvetica', 'bold');
  pdf.text(`Mixer: ${formatKg(totals.mixer)}     Pelletizer: ${formatKg(totals.pelletizer)}     Total: ${formatKg(totals.total)}`, 14, 30);
  const fontSize = Math.max(5, Math.min(8, 70 / Math.max(records.length, 1)));
  pdf.autoTable({
    startY: 35,
    head: [['Date', 'Shift', 'Type', 'Equipment', 'Mix / Application', 'Color', 'Production (kg)']],
    body: reportRows(records),
    theme: 'grid',
    styles: { fontSize, cellPadding: 1.6, overflow: 'linebreak' },
    headStyles: { fillColor: [37, 99, 235], fontSize: 7, fontStyle: 'bold' },
    columnStyles: { 6: { halign: 'right' } },
    margin: { left: 14, right: 14, bottom: 8 }
  });
  pdf.save(`daily-production-${date}.pdf`);
}

function exportMonthlyPdf(month) {
  const records = state.records.filter((r) => String(r.date).startsWith(month));
  if (!records.length) throw new Error('No production records exist for the selected month.');
  const { jsPDF } = window.jspdf;
  const pdf = new jsPDF({ orientation: 'portrait', unit: 'mm', format: 'a4' });
  pdfHeader(pdf, 'Monthly Production Report', month);
  const totals = productionBreakdown(records);
  pdf.setFontSize(10); pdf.setFont('helvetica', 'bold');
  pdf.text(`Mixer: ${formatKg(totals.mixer)}   Pelletizer: ${formatKg(totals.pelletizer)}   Total: ${formatKg(totals.total)}`, 14, 30);
  const byDay = new Map();
  records.forEach((r) => {
    if (!byDay.has(r.date)) byDay.set(r.date, []);
    byDay.get(r.date).push(r);
  });
  const dailyRows = [...byDay.entries()].sort(([a],[b]) => a.localeCompare(b)).map(([date, rows]) => {
    const t = productionBreakdown(rows);
    return [formatDate(date), formatKg(t.mixer), formatKg(t.pelletizer), formatKg(t.total)];
  });
  pdf.autoTable({ startY: 35, head: [['Date','Mixer','Pelletizer','Total']], body: dailyRows, theme:'grid', styles:{fontSize:8}, headStyles:{fillColor:[37,99,235]} });
  pdf.autoTable({ startY: pdf.lastAutoTable.finalY + 7, head: [['Date','Shift','Type','Equipment','Mix / Application','Color','kg']], body: reportRows(records), theme:'grid', styles:{fontSize:6.5,cellPadding:1.4}, headStyles:{fillColor:[37,99,235],fontSize:6.5}, columnStyles:{6:{halign:'right'}} });
  pdf.save(`monthly-production-${month}.pdf`);
}

async function runReport(exporter, button) {
  const originalText = button.textContent;
  button.disabled = true;
  button.textContent = 'Preparing PDF...';
  setReportMessage('Preparing report...');
  try {
    exporter();
    setReportMessage('PDF report exported successfully.', 'success');
  } catch (error) {
    setReportMessage(error.message, 'error');
  } finally {
    button.disabled = false;
    button.textContent = originalText;
  }
}

$('exportDailyPdf').addEventListener('click', () => {
  const date = $('dailyReportDate').value;
  if (!date) return setReportMessage('Please select a report date.', 'error');
  runReport(() => exportDailyPdf(date), $('exportDailyPdf'));
});

$('exportMonthlyPdf').addEventListener('click', () => {
  const month = $('monthlyReportMonth').value;
  if (!month) return setReportMessage('Please select a report month.', 'error');
  runReport(() => exportMonthlyPdf(month), $('exportMonthlyPdf'));
});

function exportExcelBackup() {
  if (typeof XLSX === 'undefined') throw new Error('Excel library could not be loaded.');
  const production = state.records.map((r) => ({
    'Record ID': r.id, 'Type': r.type, 'Date': r.date, 'Shift': r.shift,
    'Equipment ID': r.type === 'Mixer' ? r.mixerId : r.pelletizerId,
    'Equipment Name': recordEquipmentName(r), 'Mix Code': r.mixCode || '', 'Mix Name': r.mixName || '',
    'Pellet Application': r.application || '', 'Color': r.color, 'Quantity (kg)': Number(r.quantityKg),
    'Created At': r.createdAt || '', 'Updated At': r.updatedAt || ''
  }));
  const equipment = [
    ...state.equipment.mixers.map((e) => ({ Type: 'Mixer', 'Equipment ID': e.id, 'Equipment Name': e.name })),
    ...state.equipment.pelletizers.map((e) => ({ Type: 'Pelletizer', 'Equipment ID': e.id, 'Equipment Name': e.name }))
  ];
  const wb = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(production), 'Production');
  XLSX.utils.book_append_sheet(wb, XLSX.utils.json_to_sheet(equipment), 'Equipment');
  XLSX.writeFile(wb, `Mixer_Production_Backup_${todayISO()}.xlsx`);
}

async function importProductionBackup(file) {
  if (typeof XLSX === 'undefined') throw new Error('Excel library could not be loaded.');
  const wb = XLSX.read(await file.arrayBuffer(), { type: 'array' });
  const findSheet = (name) => wb.SheetNames.find((s) => s.trim().toLowerCase() === name.toLowerCase());
  const productionSheet = findSheet('Production');
  const equipmentSheet = findSheet('Equipment');
  if (!productionSheet || !equipmentSheet) throw new Error('This is not a complete Mixer Production backup.');
  const equipment = XLSX.utils.sheet_to_json(wb.Sheets[equipmentSheet], { defval: '' });
  const records = XLSX.utils.sheet_to_json(wb.Sheets[productionSheet], { defval: '' });
  for (const e of equipment) {
    const type = String(e.Type || '').trim();
    const name = String(e['Equipment Name'] || '').trim();
    if (!['Mixer','Pelletizer'].includes(type) || !name) continue;
    await apiFetch('api.php?action=equipment', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify({ id:String(e['Equipment ID']||''), type, name }) });
  }
  for (const r of records) {
    const type = String(r.Type || '').trim();
    if (!['Mixer','Pelletizer'].includes(type)) continue;
    const payload = {
      id: String(r['Record ID'] || ''), type, date: String(r.Date || '').slice(0,10),
      shift: String(r.Shift || 'Morning') === 'Evening' ? 'Night' : String(r.Shift || 'Morning'),
      mixerId: type === 'Mixer' ? String(r['Equipment ID'] || '') : '', mixerName: type === 'Mixer' ? String(r['Equipment Name'] || '') : '',
      pelletizerId: type === 'Pelletizer' ? String(r['Equipment ID'] || '') : '', pelletizerName: type === 'Pelletizer' ? String(r['Equipment Name'] || '') : '',
      mixCode: String(r['Mix Code'] || ''), mixName: String(r['Mix Name'] || ''), application: String(r['Pellet Application'] || ''),
      color: String(r.Color || ''), quantityKg: Number(r['Quantity (kg)'] || 0)
    };
    const response = await apiFetch('api.php?action=records', { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
    if (!response.ok) { const data = await response.json(); throw new Error(data.error || 'Import failed.'); }
  }
  await loadEquipment();
  await loadRecords();
  setReportMessage(`Backup imported successfully: ${records.length} production records.`, 'success');
}

$('exportExcelBackup').addEventListener('click', () => { try { exportExcelBackup(); } catch (e) { setReportMessage(e.message, 'error'); } });
$('importExcelBackup').addEventListener('click', () => $('productionBackupFile').click());
$('productionBackupFile').addEventListener('change', async (event) => {
  const file = event.target.files[0]; if (!file) return;
  if (!confirm('Import this backup? Existing records with the same IDs will be updated.')) { event.target.value=''; return; }
  try { setReportMessage('Importing backup...'); await importProductionBackup(file); }
  catch (e) { setReportMessage(e.message, 'error'); }
  finally { event.target.value=''; }
});

async function initialize() {
  state.filterDate = todayISO();
  $('filterDate').value = state.filterDate;
  $('dailyReportDate').value = yesterdayISO();
  $('monthlyReportMonth').value = currentMonthISO();
  await loadEquipment();
  await loadRecipeSuggestions();
  resetForm();
  await loadRecords();
}

initTheme();
initialize().catch((error) => console.error('Application initialization failed:', error));
