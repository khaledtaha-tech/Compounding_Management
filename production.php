<?php require __DIR__ . '/bootstrap.php'; $user = require_auth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta id="themeColorMeta" name="theme-color" content="#2563eb" />
  <title>Production Tracker</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="production.css" />
  <link rel="stylesheet" href="site.css" />
  <style>
    .daily-shift-summary {
      display: grid;
      grid-template-columns: repeat(3, minmax(0, 1fr));
      gap: 12px;
      padding: 16px 22px;
      border-bottom: 1px solid var(--border);
    }
    .daily-shift-card {
      min-width: 0;
      padding: 14px 16px;
      border: 1px solid var(--border);
      border-radius: 14px;
      background: var(--panel-strong);
    }
    .daily-shift-card > span {
      display: block;
      margin-bottom: 6px;
      color: var(--muted);
      font-size: 0.78rem;
      font-weight: 800;
      letter-spacing: 0.02em;
    }
    .daily-shift-card > strong {
      display: block;
      font-family: 'Manrope', sans-serif;
      font-size: 1.35rem;
      line-height: 1.2;
    }
    .daily-shift-breakdown {
      display: flex;
      flex-wrap: wrap;
      gap: 5px 12px;
      margin-top: 7px;
      color: var(--muted);
      font-size: 0.78rem;
      font-weight: 700;
    }
    .daily-shift-card.total {
      border-color: color-mix(in srgb, var(--primary) 38%, var(--border));
      background: color-mix(in srgb, var(--primary) 9%, var(--panel-strong));
    }
    @media (max-width: 760px) {
      .daily-shift-summary { grid-template-columns: 1fr; padding: 14px 16px; }
    }

    .pending-panel {
      margin: 0 22px 18px;
      padding: 16px 18px;
      border: 1px solid color-mix(in srgb, var(--primary) 35%, var(--border));
      border-radius: 16px;
      background: color-mix(in srgb, var(--primary) 6%, var(--panel-strong));
    }
    .pending-panel.hidden { display: none; }
    .pending-panel-head {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      gap: 16px;
      margin-bottom: 12px;
      flex-wrap: wrap;
    }
    .pending-panel-head h3 { margin: 2px 0 4px; font-size: 1.05rem; }
    .pending-panel-head small { color: var(--muted); }
    .pending-panel-actions { display: flex; gap: 10px; flex-shrink: 0; }
    .pending-field { width: 100%; }
    .pending-panel .table-wrap { margin-top: 0; }
    @media (max-width: 760px) {
      .pending-panel { margin: 0 16px 16px; }
      .pending-panel-head { flex-direction: column; }
    }

    body.auth-locked .app-shell,
    body.auth-locked .modal-backdrop { display: none !important; }
    .auth-screen {
      min-height: 100vh;
      display: grid;
      place-items: center;
      padding: 24px;
      background: var(--bg);
    }
    .auth-screen.hidden { display: none; }
    .auth-card {
      width: min(100%, 440px);
      padding: 30px;
      border: 1px solid var(--border);
      border-radius: 24px;
      background: var(--panel);
      box-shadow: var(--shadow);
    }
    .auth-brand { margin-bottom: 22px; }
    .auth-brand h1 { margin: 6px 0 8px; font-size: 1.8rem; }
    .auth-brand p:last-child { margin: 0; color: var(--muted); }
    .auth-form { display: grid; gap: 14px; }
    .auth-form label { display: grid; gap: 7px; }
    .auth-form label > span { font-size: 0.82rem; font-weight: 800; color: var(--muted); }
    .auth-form input { width: 100%; }
    .auth-form.hidden { display: none; }
    .auth-divider {
      display: flex;
      align-items: center;
      gap: 12px;
      margin: 18px 0;
      color: var(--muted);
      font-size: 0.78rem;
      font-weight: 800;
    }
    .auth-divider::before, .auth-divider::after { content: ''; flex: 1; height: 1px; background: var(--border); }
    .google-button { width: 100%; }
    .auth-switch { margin: 18px 0 0; text-align: center; color: var(--muted); font-size: 0.86rem; }
    .auth-message { min-height: 20px; margin: 12px 0 0; text-align: center; font-size: 0.84rem; font-weight: 700; }
    .auth-message.error { color: #dc2626; }
    .auth-message.success { color: var(--primary); }
    .user-session {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 7px 9px 7px 12px;
      border: 1px solid var(--border);
      border-radius: 14px;
      background: var(--panel-strong);
    }
    .user-session span {
      max-width: 190px;
      overflow: hidden;
      text-overflow: ellipsis;
      white-space: nowrap;
      color: var(--muted);
      font-size: 0.78rem;
      font-weight: 800;
    }
    .sign-out-button { padding: 8px 11px; font-size: 0.78rem; }
    @media (max-width: 760px) {
      .auth-card { padding: 22px; border-radius: 20px; }
      .user-session { width: 100%; justify-content: space-between; }
      .user-session span { max-width: 210px; }
    }
  </style>
</head>
<body>

  <div class="app-shell">
    <header class="topbar">
      <div>
        <p class="eyebrow">DAILY PRODUCTION</p>
        <h1>Compounding Production Tracker</h1>
        <p class="subtitle">Record production by equipment, mix, date and shift.</p>
      </div>
      <div class="topbar-actions">
        <nav class="unified-nav" aria-label="Main modules">
          <a href="index.php">Home</a>
          <a class="active" href="production.php">Production</a>
          <a href="materials.php">Materials & Recipes</a>
        </nav>

        <div class="user-session" title="Signed-in user">
          <span id="userIdentity"><?= htmlspecialchars($user['email']) ?></span>
          <a class="secondary-button sign-out-button" href="logout.php">Sign Out</a>
        </div>
        <button id="manageEquipment" class="secondary-button top-action" type="button">Equipment Setup</button>
        <label class="theme-picker" for="themeSelect">
          <span>Theme</span>
          <select id="themeSelect" aria-label="Choose application theme">
            <option value="arctic">Arctic Blue · Light</option>
            <option value="indigo">Pearl Indigo · Light</option>
            <option value="violet">Soft Violet · Light</option>
            <option value="midnight">Midnight Blue · Dark</option>
            <option value="graphite">Graphite Violet · Dark</option>
          </select>
        </label>
      </div>
    </header>

    <main>
      <section class="summary-grid">
        <article class="summary-card accent">
          <span>Selected Day Total</span>
          <strong id="dayTotal">0 kg</strong>
          <small id="selectedDayLabel">Today</small>
        </article>
        <article class="summary-card">
          <span>Mixer Production</span>
          <strong id="mixerTotal">0 kg</strong>
          <small>Selected day</small>
        </article>
        <article class="summary-card">
          <span>Pelletizer Production</span>
          <strong id="pelletizerTotal">0 kg</strong>
          <small>Selected day</small>
        </article>
        <article class="summary-card">
          <span>Records</span>
          <strong id="recordCount">0</strong>
          <small>Selected day</small>
        </article>
      </section>

      <section class="panel reports-panel">
        <div class="reports-heading">
          <div>
            <p class="eyebrow">PDF REPORTS</p>
            <h2>Export Production Reports</h2>
            <p class="reports-subtitle">Daily report defaults to yesterday for easy management sharing.</p>
          </div>
        </div>

        <div class="reports-grid">
          <div class="report-box">
            <div>
              <span class="report-kicker">DAILY REPORT</span>
              <strong>Daily Production PDF</strong>
              <small>Single-page A4 portrait report with only the selected day production.</small>
            </div>
            <label>
              <span>Report date</span>
              <input id="dailyReportDate" type="date" />
            </label>
            <button id="exportDailyPdf" class="report-button" type="button">Export Daily PDF</button>
          </div>

          <div class="report-box">
            <div>
              <span class="report-kicker">MONTHLY REPORT</span>
              <strong>Monthly Production PDF</strong>
              <small>Monthly totals, daily summary and production details only.</small>
            </div>
            <label>
              <span>Report month</span>
              <input id="monthlyReportMonth" type="month" />
            </label>
            <button id="exportMonthlyPdf" class="report-button secondary-report" type="button">Export Monthly PDF</button>
          </div>
        </div>
        <div class="backup-actions">
          <button id="exportExcelBackup" class="secondary-button" type="button">Export Excel Backup</button>
          <button id="importExcelBackup" class="secondary-button" type="button">Import Excel Backup</button>
          <input id="productionBackupFile" class="hidden" type="file" accept=".xlsx,.xls" />
        </div>
        <p id="reportMessage" class="report-message" aria-live="polite"></p>
      </section>

      <section class="workspace-grid">
        <article class="panel form-panel">
          <div class="panel-heading">
            <div>
              <p class="eyebrow">NEW ENTRY</p>
              <h2 id="formTitle">Add Production Record</h2>
            </div>
            <button id="cancelEdit" class="text-button hidden" type="button">Cancel edit</button>
          </div>

          <div class="segmented" role="tablist" aria-label="Production type">
            <button class="segment active" type="button" data-type="Mixer">Mixer</button>
            <button class="segment" type="button" data-type="Pelletizer">Pelletizer</button>
          </div>

          <form id="productionForm">
            <input type="hidden" id="recordId" />
            <input type="hidden" id="productionType" value="Mixer" />

            <div class="form-grid">
              <label>
                <span>Date</span>
                <input id="date" type="date" required />
              </label>

              <label>
                <span>Shift</span>
                <select id="shift" required>
                  <option value="Morning">Morning</option>
                  <option value="Night">Night</option>
                </select>
              </label>

              <div id="mixerFields" class="field-group full-width">
                <label class="full-width">
                  <span>Mixer</span>
                  <div class="select-action-row">
                    <select id="mixerId"></select>
                    <button class="mini-add-button" type="button" data-add-equipment="Mixer">+ Add Mixer</button>
                  </div>
                </label>

                <label class="full-width">
                  <span>Mix / Recipe</span>
                  <select id="recipeSelect" required>
                    <option value="">Choose a production mix</option>
                  </select>
                  <small class="field-help">The list comes directly from Materials & Recipes.</small>
                </label>

                <label>
                  <span>Production Code</span>
                  <input id="mixCode" class="readonly-field" type="text" readonly placeholder="MP-01 / MF-01" />
                </label>

                <label>
                  <span>Recipe Code</span>
                  <input id="recipeCode" class="readonly-field" type="text" readonly placeholder="KH-600" />
                </label>

                <label class="full-width">
                  <span>Mix Name</span>
                  <input id="mixName" class="readonly-field" type="text" readonly placeholder="Selected recipe name" />
                </label>

                <label>
                  <span>Batch Weight (kg)</span>
                  <input id="batchWeightKg" class="readonly-field" type="number" step="0.0001" readonly placeholder="0.00" />
                </label>

                <label>
                  <span>No. of Batches</span>
                  <input id="batchCount" type="number" min="0.001" step="any" placeholder="0" inputmode="decimal" />
                </label>
              </div>

              <div id="pelletizerFields" class="field-group full-width hidden">
                <label class="full-width">
                  <span>Pelletizer</span>
                  <div class="select-action-row">
                    <select id="pelletizerId"></select>
                    <button class="mini-add-button" type="button" data-add-equipment="Pelletizer">+ Add Pelletizer</button>
                  </div>
                </label>

                <label class="full-width">
                  <span>Pellet Application</span>
                  <input id="application" type="text" list="applicationHistory" placeholder="e.g. PVC Injection Pellets" autocomplete="off" />
                </label>
              </div>

              <label>
                <span>Color</span>
                <input id="color" type="text" list="colorHistory" placeholder="e.g. White" autocomplete="off" required />
              </label>

              <label>
                <span>Production (kg)</span>
                <input id="quantityKg" class="readonly-field" type="number" min="0.01" step="0.01" placeholder="0.00" readonly required />
              </label>
            </div>

            <datalist id="applicationHistory"></datalist>
            <datalist id="colorHistory"></datalist>

            <div class="form-actions">
              <button class="primary-button" type="submit" id="saveButton">Save Record</button>
              <span id="formMessage" class="form-message" aria-live="polite"></span>
            </div>
          </form>
        </article>

        <article class="panel records-panel">
          <div class="records-toolbar">
            <div>
              <p class="eyebrow">PRODUCTION LOG</p>
              <h2>Daily Records</h2>
            </div>
            <div class="filter-row">
              <label>
                <span>Search records</span>
                <input id="recordsSearch" type="search" placeholder="Search mixer, code, mix, color..." autocomplete="off" />
              </label>
              <label>
                <span>View date</span>
                <input id="filterDate" type="date" />
              </label>
              <button id="showAll" class="secondary-button" type="button">Show all</button>
              <button id="duplicatePreviousDay" class="secondary-button" type="button">Duplicate Previous Day</button>
            </div>
          </div>

          <div class="daily-shift-summary" aria-label="Selected day production summary">
            <article class="daily-shift-card">
              <span>Morning Production</span>
              <strong id="morningProduction">0.00 kg</strong>
              <div class="daily-shift-breakdown">
                <span>Mixer: <b id="morningMixer">0.00 kg</b></span>
                <span>Pelletizer: <b id="morningPelletizer">0.00 kg</b></span>
              </div>
            </article>
            <article class="daily-shift-card">
              <span>Night Production</span>
              <strong id="nightProduction">0.00 kg</strong>
              <div class="daily-shift-breakdown">
                <span>Mixer: <b id="nightMixer">0.00 kg</b></span>
                <span>Pelletizer: <b id="nightPelletizer">0.00 kg</b></span>
              </div>
            </article>
            <article class="daily-shift-card total">
              <span>Total Production</span>
              <strong id="shiftSummaryTotal">0.00 kg</strong>
              <div class="daily-shift-breakdown">
                <span>Mixer: <b id="shiftSummaryMixer">0.00 kg</b></span>
                <span>Pelletizer: <b id="shiftSummaryPelletizer">0.00 kg</b></span>
              </div>
            </article>
          </div>

          <div id="pendingDuplicatesPanel" class="pending-panel hidden">
            <div class="pending-panel-head">
              <div>
                <p class="eyebrow">REVIEW BEFORE SAVING</p>
                <h3>Duplicated from <span id="pendingSourceDate">-</span></h3>
                <small>Edit any value below, remove records you don't need, then save them all.</small>
              </div>
              <div class="pending-panel-actions">
                <button id="discardPending" class="secondary-button" type="button">Discard All</button>
                <button id="savePending" class="primary-button" type="button">Save All (<span id="pendingCount">0</span>)</button>
              </div>
            </div>
            <div class="table-wrap">
              <table>
                <thead>
                  <tr>
                    <th>Shift</th>
                    <th>Section</th>
                    <th>Equipment</th>
                    <th>Mix / Application</th>
                    <th>Color</th>
                    <th>Batches</th>
                    <th>Production</th>
                    <th class="actions-column">Remove</th>
                  </tr>
                </thead>
                <tbody id="pendingRecordsBody"></tbody>
              </table>
            </div>
            <p id="pendingMessage" class="form-message" aria-live="polite"></p>
          </div>

          <div class="table-wrap">
            <table>
              <thead>
                <tr>
                  <th>Date</th>
                  <th>Shift</th>
                  <th>Section</th>
                  <th>Equipment</th>
                  <th>Mix / Application</th>
                  <th>Color</th>
                  <th>Batches</th>
                  <th>Production</th>
                  <th class="actions-column">Actions</th>
                </tr>
              </thead>
              <tbody id="recordsBody"></tbody>
            </table>
            <div id="emptyState" class="empty-state hidden">
              <div class="empty-icon">↗</div>
              <h3>No production records yet</h3>
              <p>Add your first mixer or pelletizer entry.</p>
            </div>
          </div>
        </article>
      </section>
    </main>
  </div>

  <div id="equipmentModal" class="modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="equipmentModalTitle">
    <div class="modal-card equipment-modal-card">
      <div class="modal-header">
        <div>
          <p class="eyebrow">EQUIPMENT SETUP</p>
          <h2 id="equipmentModalTitle">Manage Equipment</h2>
        </div>
        <button id="closeEquipmentModal" class="modal-close" type="button" aria-label="Close">×</button>
      </div>

      <div class="equipment-columns">
        <section>
          <div class="equipment-section-head">
            <h3>Mixers</h3>
            <button class="mini-add-button" type="button" data-add-equipment="Mixer">+ Add Mixer</button>
          </div>
          <div id="mixerEquipmentList" class="equipment-list"></div>
        </section>
        <section>
          <div class="equipment-section-head">
            <h3>Pelletizers</h3>
            <button class="mini-add-button" type="button" data-add-equipment="Pelletizer">+ Add Pelletizer</button>
          </div>
          <div id="pelletizerEquipmentList" class="equipment-list"></div>
        </section>
      </div>
    </div>
  </div>

  <div id="addEquipmentModal" class="modal-backdrop hidden" role="dialog" aria-modal="true" aria-labelledby="addEquipmentTitle">
    <div class="modal-card small-modal-card">
      <div class="modal-header">
        <div>
          <p class="eyebrow">ADD EQUIPMENT</p>
          <h2 id="addEquipmentTitle">Add Mixer</h2>
        </div>
        <button id="closeAddEquipmentModal" class="modal-close" type="button" aria-label="Close">×</button>
      </div>
      <form id="equipmentForm">
        <input id="equipmentType" type="hidden" value="Mixer" />
        <label>
          <span>Equipment Name</span>
          <input id="equipmentName" type="text" placeholder="e.g. Mixer 6" autocomplete="off" required />
        </label>
        <div class="modal-actions">
          <button id="cancelAddEquipment" class="secondary-button" type="button">Cancel</button>
          <button class="primary-button compact-primary" type="submit">Save Equipment</button>
        </div>
        <p id="equipmentMessage" class="form-message" aria-live="polite"></p>
      </form>
    </div>
  </div>

  <script src="vendor/jspdf.umd.min.js"></script>
  <script src="vendor/jspdf.plugin.autotable.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
  <script src="production.js?v=5.0.0"></script>
</body>
</html>
