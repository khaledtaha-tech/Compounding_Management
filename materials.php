<?php require __DIR__ . '/bootstrap.php'; $user = require_auth(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Material Planner Pro</title>
<style>
:root{
  --bg:#f4f7fb;--panel:#fff;--line:#dfe7f1;--text:#1d2939;--muted:#667085;
  --primary:#2f6fed;--primary2:#eaf1ff;--danger:#d64545;--success:#16835d;
  --warning:#d98600;--shadow:0 10px 28px rgba(41,67,109,.08);--r:16px
}
*{box-sizing:border-box}
body{margin:0;font-family:Inter,Segoe UI,Arial,sans-serif;background:var(--bg);color:var(--text);min-width:1100px}
header{height:68px;background:#fff;border-bottom:1px solid var(--line);display:flex;align-items:center;justify-content:space-between;padding:0 24px;position:sticky;top:0;z-index:10}
.brand{font-size:20px;font-weight:800}
nav{display:flex;gap:8px;align-items:center}
nav button{border:0;background:#eef3f8;color:#344054;padding:11px 18px;border-radius:10px;font-weight:700;cursor:pointer}
nav button.active{background:var(--primary);color:#fff}
.save-area{display:flex;align-items:center;gap:8px;margin-right:8px}
.save-status{font-size:11px;font-weight:800;padding:7px 10px;border-radius:999px}
.save-status.saved{background:#eaf8f2;color:var(--success)}
.save-status.unsaved{background:#fff4df;color:var(--warning)}
.material-checks{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px}
.material-check{display:flex;align-items:center;gap:9px;border:1px solid var(--line);border-radius:10px;padding:10px;background:#fff;cursor:pointer}
.material-check input{width:17px;height:17px;accent-color:var(--primary)}
.material-check span{font-size:12px;font-weight:700}
.multi-note{font-size:11px;color:var(--muted);margin-top:8px}
.results-stack{display:flex;flex-direction:column;gap:10px;margin-top:12px}
.combo-wrap{position:relative}
.combo-input{padding-right:34px}
.combo-arrow{position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--muted);font-size:12px}
.combo-list{position:absolute;left:0;right:0;top:44px;z-index:50;background:#fff;border:1px solid var(--line);border-radius:10px;box-shadow:var(--shadow);max-height:220px;overflow:auto;display:none}
.combo-list.open{display:block}
.combo-option{padding:10px 12px;font-size:12px;cursor:pointer;border-bottom:1px solid #edf1f5}
.combo-option:last-child{border-bottom:0}
.combo-option:hover{background:var(--primary2);color:var(--primary)}
.combo-empty{padding:12px;color:var(--muted);font-size:11px;text-align:center}
.cloud-status{font-size:11px;font-weight:800;padding:7px 10px;border-radius:999px;background:#eef3f8;color:#526176}
.cloud-status.online{background:#eaf8f2;color:var(--success)}
.cloud-status.syncing{background:#fff4df;color:var(--warning)}
.cloud-status.error{background:#fff0f0;color:var(--danger)}
.auth-user{font-size:11px;color:var(--muted);max-width:190px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.auth-overlay{position:fixed;inset:0;background:rgba(20,32,50,.48);display:none;align-items:center;justify-content:center;z-index:999}
.auth-overlay.open{display:flex}
.auth-card{width:390px;background:#fff;border-radius:18px;padding:24px;box-shadow:0 25px 70px rgba(20,32,50,.24)}
.auth-card h2{font-size:22px;margin:0 0 6px}.auth-field{margin-bottom:12px}.auth-actions{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:14px}
.toast{position:fixed;right:22px;bottom:22px;background:#1d2939;color:#fff;padding:12px 16px;border-radius:10px;box-shadow:var(--shadow);display:none;z-index:1000;font-size:12px}.toast.show{display:block}
.page{display:none;padding:18px}
.page.active{display:block}
.grid{display:grid;grid-template-columns:minmax(650px,1fr) 380px;gap:16px}
.panel{background:var(--panel);border:1px solid var(--line);border-radius:var(--r);box-shadow:var(--shadow);padding:18px}
h2{margin:0 0 5px;font-size:18px} .sub{color:var(--muted);font-size:12px;margin-bottom:15px}
table{width:100%;border-collapse:collapse;table-layout:fixed}
th{background:#f2f6fb;color:#526176;font-size:11px;text-transform:uppercase;padding:11px;border-bottom:1px solid var(--line);text-align:left}
td{padding:10px;border-bottom:1px solid #edf1f5;font-size:13px}
input,select{width:100%;height:39px;border:1px solid var(--line);border-radius:9px;padding:0 10px;font-size:13px;outline:none;background:#fff}
input:focus,select:focus{border-color:var(--primary);box-shadow:0 0 0 3px rgba(47,111,237,.10)}
.check{width:18px;height:18px;accent-color:var(--primary)}
.btn{height:40px;border:0;border-radius:10px;padding:0 14px;font-weight:700;cursor:pointer}
.btn:disabled{opacity:.5;cursor:not-allowed}
.primary{background:var(--primary);color:#fff}.soft{background:var(--primary2);color:var(--primary)}.danger{background:#fff0f0;color:var(--danger)}
.full{width:100%}.row{display:flex;gap:9px;align-items:center}.space{justify-content:space-between}
.recipe-select-card{border:1px solid var(--line);border-radius:13px;padding:14px;margin-bottom:10px;background:#fff;display:grid;grid-template-columns:28px 1fr 190px 135px;gap:12px;align-items:center}
.recipe-select-card.selected{border-color:var(--primary);background:#f8fbff}
.recipe-select-card h3{margin:0 0 4px;font-size:14px}.recipe-select-card p{margin:0;color:var(--muted);font-size:11px}
.label{font-size:11px;font-weight:700;color:#526176;margin-bottom:5px}
.metric-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:10px;margin-top:14px}
.metric{background:#f5f8fc;border:1px solid var(--line);border-radius:12px;padding:12px}
.metric span{display:block;color:var(--muted);font-size:10px;margin-bottom:5px}.metric strong{font-size:16px}
.result{border:1px solid var(--line);border-radius:14px;padding:14px;margin-top:12px;background:#fbfdff}
.days{font-size:27px;font-weight:800;color:var(--primary)}
.result-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin-top:10px}
.mini{background:#f1f5f9;border-radius:9px;padding:9px}.mini span{display:block;font-size:10px;color:var(--muted);margin-bottom:4px}.mini strong{font-size:12px}
.badge{display:inline-flex;padding:5px 9px;border-radius:999px;font-size:10px;font-weight:800;background:#eaf1ff;color:var(--primary)}
.recipe-selector-wrap{display:grid;grid-template-columns:180px minmax(320px,1fr);gap:12px;align-items:center;margin-bottom:15px}.recipe-selector-wrap select{font-weight:700;background:#f8fbff}.recipe-tools{display:grid;grid-template-columns:minmax(180px,1fr) 180px auto auto auto auto auto;gap:8px;margin-bottom:14px}.recipe-list-empty{width:100%;padding:24px;border:1px dashed var(--line);border-radius:12px;text-align:center;color:var(--muted)}
.recipe-tabs button{border:1px solid var(--line);background:#fff;padding:9px 12px;border-radius:9px;font-weight:700;cursor:pointer}
.recipe-tabs button.active{background:var(--primary);color:#fff;border-color:var(--primary)}
.formgrid{display:grid;grid-template-columns:2fr 1fr;gap:12px;margin-bottom:14px}
.recipe-identity-grid{display:grid;grid-template-columns:180px 150px minmax(280px,2fr) minmax(150px,1fr) minmax(130px,.8fr);gap:12px;margin-bottom:14px}
.recipe-code-input{text-transform:uppercase;font-weight:800;letter-spacing:.5px;background:#f8fbff}
.production-code-input{text-transform:uppercase;font-weight:900;letter-spacing:.5px;background:#eef4ff;color:var(--primary)}
.recipe-code-row{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:8px}
.recipe-code-row .btn{height:39px;white-space:nowrap}
.recipe-code-badge{display:inline-flex;align-items:center;padding:4px 8px;border-radius:8px;background:#eaf1ff;color:var(--primary);font-size:10px;font-weight:800;letter-spacing:.4px;margin-right:7px}
.note{background:#f8fbff;border:1px solid #dce8fb;border-radius:11px;padding:12px;color:#5f6f84;font-size:11px;line-height:1.5}
.num{text-align:right}.center{text-align:center}.hidden{display:none}
.recipe-total-row td{border-top:2px solid #b8cdf8;border-bottom:0;padding:11px 12px;background:#f7faff;vertical-align:middle}
.recipe-total-row .totals-label{color:#526176;font-size:11px;font-weight:800;text-transform:uppercase;letter-spacing:.55px}
.table-total{text-align:right;white-space:nowrap}
.table-total span{display:block;color:#667085;font-size:9px;font-weight:800;text-transform:uppercase;letter-spacing:.45px;margin-bottom:4px}
.table-total strong{display:block;color:#1d2939;font-size:16px;line-height:1.1}
.table-total.batch strong,.table-total.phr strong{color:var(--primary)}
.export-overlay{position:fixed;inset:0;background:rgba(20,32,50,.52);display:none;align-items:center;justify-content:center;padding:24px;z-index:1002}
.export-overlay.open{display:flex}
.export-card{width:min(680px,100%);max-height:88vh;background:#fff;border-radius:18px;box-shadow:0 25px 70px rgba(20,32,50,.28);display:flex;flex-direction:column;overflow:hidden}
.export-head{display:flex;align-items:flex-start;justify-content:space-between;gap:18px;padding:22px 24px 14px;border-bottom:1px solid var(--line)}
.export-head h2{font-size:21px;margin-bottom:5px}.export-head .sub{margin:0}
.icon-btn{width:36px;height:36px;border:0;border-radius:10px;background:#eef3f8;color:#526176;font-size:22px;line-height:1;cursor:pointer}
.export-select-all{display:flex;align-items:center;gap:11px;padding:13px 24px;background:#f7faff;border-bottom:1px solid var(--line);font-size:13px;font-weight:800;cursor:pointer}
.export-select-all input,.export-recipe-item input{width:18px;height:18px;accent-color:var(--primary);flex:0 0 auto}
.export-list{padding:12px 24px;overflow:auto;display:flex;flex-direction:column;gap:8px}
.export-recipe-item{display:grid;grid-template-columns:20px minmax(0,1fr) auto;align-items:center;gap:12px;padding:12px 14px;border:1px solid var(--line);border-radius:11px;background:#fff;cursor:pointer}
.export-recipe-item:hover,.export-recipe-item.checked{border-color:#aac4fa;background:#f8fbff}
.export-recipe-title{font-size:13px;font-weight:800;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.export-recipe-meta{color:var(--muted);font-size:10px;margin-top:4px}
.export-recipe-total{color:#344054;font-size:11px;font-weight:800;white-space:nowrap}
.export-foot{display:flex;align-items:center;justify-content:space-between;gap:12px;padding:14px 24px 20px;border-top:1px solid var(--line);background:#fff}
.export-count{color:var(--muted);font-size:11px;font-weight:700}.export-actions{display:flex;gap:8px}
#recipesPage .recipe-tools{display:flex;flex-wrap:wrap}
#recipesPage .recipe-tools>input:not([type="file"]){flex:1 1 260px}
#recipesPage .recipe-tools>#recipeCategoryFilter{flex:0 0 180px}
.import-overlay{position:fixed;inset:0;background:rgba(20,32,50,.48);display:none;align-items:center;justify-content:center;z-index:1001}
.import-overlay.open{display:flex}
.import-card{width:520px;background:#fff;border-radius:18px;padding:24px;box-shadow:0 25px 70px rgba(20,32,50,.24)}
.import-card h2{margin:0 0 6px;font-size:22px}
.import-options{display:grid;grid-template-columns:1fr;gap:10px;margin-top:18px}
.import-option{height:48px;border:0;border-radius:11px;font-weight:800;cursor:pointer;font-size:14px}
.import-option.recipes{background:var(--primary);color:#fff}
.import-option.materials{background:#eaf8f2;color:var(--success)}
.import-option.both{background:#fff4df;color:var(--warning)}
.import-cancel{margin-top:10px;background:#eef3f8;color:#526176}
@media(max-width:1250px){.grid{grid-template-columns:1fr 340px}.recipe-select-card{grid-template-columns:28px 1fr 170px 120px}.recipe-identity-grid{grid-template-columns:180px 150px 1fr}}
@media(max-width:760px){.recipe-identity-grid,.formgrid{grid-template-columns:1fr}}
</style>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script src="vendor/jspdf.umd.min.js"></script>
<script src="vendor/jspdf.plugin.autotable.min.js"></script>
<link rel="stylesheet" href="site.css">
</head>
<body>
<header>
  <div class="brand">Material Planner Pro</div>
  <nav>
    <div class="unified-nav"><a href="index.php">Home</a><a href="production.php">Production</a><a class="active" href="materials.php">Materials & Recipes</a></div>
    <span id="cloudStatus" class="cloud-status">Connecting...</span>
    <span id="authUser" class="auth-user"><?= htmlspecialchars($user['email']) ?></span>
    <a href="logout.php" class="btn danger" style="display:grid;place-items:center;text-decoration:none">Logout</a>
    <div class="save-area">
      <span id="saveStatus" class="save-status saved">Saved</span>
      <button id="saveNowBtn" class="btn primary">Save</button>
    </div>
    <button id="navDashboard" class="active">Stock Coverage</button>
    <button id="navRecipes">PVC Recipes</button>
    <button id="navMaterials">Raw Materials</button>
  </nav>
</header>

<section id="dashboardPage" class="page active">
  <div class="grid">
    <div class="panel">
      <h2>Select Recipes for Calculation</h2>
      <div class="sub">Check the recipes to include, then enter the expected daily production for each selected recipe.</div>
      <div id="recipeSelection"></div>
      <div class="metric-grid">
        <div class="metric"><span>Selected Recipes</span><strong id="selectedCount">0</strong></div>
        <div class="metric"><span>Total Daily Production</span><strong id="totalDailyProduction">0 kg</strong></div>
        <div class="metric"><span>Tracked Materials</span><strong id="trackedCount">0</strong></div>
      </div>
    </div>

    <div>
      <div class="panel" style="margin-bottom:16px">
        <h2>Material Stock</h2>
        <div class="sub">Enter only materials that you want to calculate.</div>
        <table>
          <thead><tr><th>Material</th><th class="num">Stock kg</th><th style="width:50px"></th></tr></thead>
          <tbody id="stockBody"></tbody>
        </table>
        <button id="addStock" class="btn soft full" style="margin-top:10px">+ Add Material</button>
      </div>

      <div class="panel">
        <h2>Coverage Calculation</h2>
        <div class="sub">Daily consumption is calculated automatically from each selected recipe.</div>
        <div class="row space">
          <div>
            <strong style="font-size:13px">Select Materials</strong>
            <div class="multi-note">Choose up to 5 materials for one calculation.</div>
          </div>
          <button id="calculate" class="btn primary">Calculate Selected</button>
        </div>
        <div id="materialChecks" class="material-checks"></div>
        <div class="note" style="margin-top:12px">
          Consumption = Daily production of recipe × ingredient percentage of total recipe weight.
        </div>
        <div id="resultBox" class="results-stack"></div>
      </div>
    </div>
  </div>
</section>

<section id="recipesPage" class="page">
  <div class="panel">
    <div class="row space">
      <div>
        <h2>PVC Pipe Recipes</h2>
        <div class="sub">Enter the actual batch exactly as used on the mixer. The application also converts every ingredient automatically to a 100 kg resin basis for comparison.</div>
      </div>
      <span class="badge" id="recipeTotalBadge">0 Recipes</span>
    </div>

    <div class="recipe-tools">
      <input id="recipeSearch" placeholder="Search recipes...">
      <select id="recipeCategoryFilter">
        <option value="">All Categories</option>
      </select>
      <button id="newRecipe" class="btn primary">+ New Recipe</button>
      <button id="duplicateRecipe" class="btn soft">Duplicate</button>
      <button id="deleteRecipe" class="btn danger">Delete</button>
      <button id="exportPdfBtn" class="btn primary">Export PDF</button>
      <button id="importExcelBtn" class="btn soft">Import Excel</button>
      <button id="downloadTemplateBtn" class="btn soft">Excel Template</button>
      <button id="exportBackupBtn" class="btn soft">Export Excel Backup</button>
      <button id="importBackupBtn" class="btn soft">Import Full Backup</button>
      <input id="excelFileInput" type="file" accept=".xlsx,.xls" class="hidden">
      <input id="backupFileInput" type="file" accept=".xlsx,.xls" class="hidden">
    </div>

    <div class="recipe-selector-wrap">
      <div class="label">Select Recipe</div>
      <select id="recipeSelector"></select>
    </div>

    <div class="recipe-identity-grid">
      <div>
        <div class="label">Recipe Code</div>
        <div class="recipe-code-row">
          <input id="recipeCode" class="recipe-code-input" placeholder="Example: KH-120" maxlength="30">
          <button id="generateRecipeCode" type="button" class="btn soft">Generate</button>
        </div>
      </div>
      <div>
        <div class="label">Production Code</div>
        <input id="productionCode" class="production-code-input" placeholder="MP-01 / MF-01" maxlength="20">
      </div>
      <div>
        <div class="label">Recipe Name</div>
        <input id="recipeName">
      </div>
      <div>
        <div class="label">Recipe Category</div>
        <input id="recipeCategory" placeholder="Example: Pressure Pipe">
      </div>
      <div>
        <div class="label">Color</div>
        <input id="recipeColor" placeholder="Example: Grey">
      </div>
    </div>

    <div class="formgrid">
      <div>
        <div class="label">Actual PVC Resin in Batch (kg)</div>
        <input id="pvcBase" type="number" step="0.01">
      </div>
      <div>
        <div class="label">Ingredients Count</div>
        <input id="ingredientsCount" readonly>
      </div>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width:24%">Material</th>
          <th style="width:24%">Grade / Trade Name</th>
          <th class="num" style="width:16%">Actual kg / Batch</th>
          <th class="num" style="width:14%">% of Batch</th>
          <th class="num" style="width:12%">PHR</th>
          <th style="width:55px"></th>
        </tr>
      </thead>
      <tbody id="ingredientBody"></tbody>
      <tfoot>
        <tr class="recipe-total-row">
          <td colspan="2" class="totals-label">Recipe Totals</td>
          <td class="num">
            <div class="table-total batch">
              <span>Batch Weight</span>
              <strong><span id="totalBatchValue" style="display:inline;color:inherit;font:inherit;letter-spacing:0;text-transform:none;margin:0">0</span> kg</strong>
            </div>
          </td>
          <td class="num">
            <div class="table-total">
              <span>Total Percentage</span>
              <strong id="totalPercentValue">100.00%</strong>
            </div>
          </td>
          <td class="num">
            <div class="table-total phr">
              <span>PHR</span>
              <strong id="totalPhrValue">0</strong>
            </div>
          </td>
          <td></td>
        </tr>
      </tfoot>
    </table>
    <div class="row" style="margin-top:14px">
      <button id="addIngredient" class="btn soft">+ Add Ingredient</button>
    </div>
  </div>
</section>


<section id="materialsPage" class="page">
  <div class="panel">
    <div class="row space">
      <div>
        <h2>Raw Materials</h2>
        <div class="sub">Manage the master list of raw materials, grades, origin, company, and available stock.</div>
      </div>
      <span class="badge" id="materialTotalBadge">0 Materials</span>
    </div>

    <div class="recipe-tools" style="grid-template-columns:minmax(220px,1fr) auto auto auto">
      <input id="materialSearch" placeholder="Search materials, grades, countries, or companies...">
      <select id="materialSortBy" style="font-weight:700;background:#f8fbff">
        <option value="name">Sort by: Name A-Z</option>
        <option value="name-desc">Sort by: Name Z-A</option>
        <option value="stock">Sort by: Stock (High to Low)</option>
        <option value="stock-asc">Sort by: Stock (Low to High)</option>
      </select>
      <button id="newMaterial" class="btn primary">+ New Material</button>
      <button id="syncMaterials" class="btn soft">Sync from Recipes</button>
    </div>

    <table>
      <thead>
        <tr>
          <th style="width:22%">Material Name</th>
          <th style="width:22%">Grade / Trade Name</th>
          <th style="width:16%">Country</th>
          <th style="width:22%">Company</th>
          <th class="num" style="width:13%">Stock kg</th>
          <th style="width:55px"></th>
        </tr>
      </thead>
      <tbody id="rawMaterialBody"></tbody>
    </table>
    <div id="rawMaterialEmpty" class="recipe-list-empty hidden">No raw materials match the current search.</div>
  </div>
</section>

<div id="importOverlay" class="import-overlay">
  <div class="import-card">
    <h2>Import Excel</h2>
    <div class="sub">Choose exactly what you want to import from the selected Excel file.</div>
    <div class="import-options">
      <button type="button" class="import-option recipes" data-import-type="recipes">Import Recipes Only</button>
      <button type="button" class="import-option materials" data-import-type="rawMaterials">Import Raw Materials Only</button>
      <button type="button" class="import-option both" data-import-type="both">Import Recipes + Raw Materials</button>
      <button type="button" id="cancelImportChoice" class="import-option import-cancel">Cancel</button>
    </div>
  </div>
</div>

<div id="exportOverlay" class="export-overlay" aria-hidden="true">
  <div class="export-card" role="dialog" aria-modal="true" aria-labelledby="exportDialogTitle">
    <div class="export-head">
      <div>
        <h2 id="exportDialogTitle">Export Recipes to PDF</h2>
        <div class="sub">Choose the recipes to include in the print-ready report.</div>
      </div>
      <button id="closeExportDialog" type="button" class="icon-btn" aria-label="Close">&times;</button>
    </div>
    <label class="export-select-all">
      <input id="exportSelectAll" type="checkbox">
      <span>Select All Recipes</span>
    </label>
    <div id="exportRecipeList" class="export-list"></div>
    <div class="export-foot">
      <span id="exportSelectedCount" class="export-count">0 recipes selected</span>
      <div class="export-actions">
        <button id="cancelExportDialog" type="button" class="btn soft">Cancel</button>
        <button id="generatePdfBtn" type="button" class="btn primary">Export Selected PDF</button>
      </div>
    </div>
  </div>
</div>

<div id="toast" class="toast"></div>
<script>
const defaults = {
  activeRecipe:0,
  recipes:[
    {
      code:"KH-120",
      productionCode:"MF-01",
      name:"Orange PVC Fitting",
      category:"Fitting",
      color:"Orange",
      pvcBase:250,
      selected:true,
      dailyProduction:3000,
      ingredients:[
        {material:"PVC Resin",grade:"PVC K-57",kg:250},
        {material:"Calcium Carbonate",grade:"",kg:7.5},
        {material:"Stabilizer",grade:"SAG-1015",kg:13.75},
        {material:"Calcium Stearate",grade:"",kg:2.5},
        {material:"Lubricant",grade:"Finalux G322",kg:2.5},
        {material:"Lubricant",grade:"Finalux G1",kg:1.25},
        {material:"Impact Modifier",grade:"LS-601",kg:2.5},
        {material:"Pigment",grade:"Orange 1000R19085",kg:2.5},
        {material:"PE Wax",grade:"K-220-1P",kg:0.5},
        {material:"Processing Aid",grade:"LP-551",kg:0.42}
      ]
    }
  ],
  stocks:[
    {material:"Stabilizer",grade:"SAG-1015",kg:8000}
  ],
  rawMaterials:[]
}
let state;
const LOCAL_CACHE_KEY="pvcPlannerCloudStableV1";
try{state=JSON.parse(localStorage.getItem(LOCAL_CACHE_KEY))||structuredClone(defaults)}catch(e){state=structuredClone(defaults)}

let isDirty=false;

function toast(message){
  const el=document.getElementById("toast");
  el.textContent=message;el.classList.add("show");
  setTimeout(()=>el.classList.remove("show"),2200);
}
function setCloudStatus(text,kind=""){
  const el=document.getElementById("cloudStatus");
  el.textContent=text;el.className="cloud-status "+kind;
}
function updateSaveStatus(){
  const el=document.getElementById("saveStatus");
  if(!el)return;
  el.textContent=isDirty?"Unsaved Changes":"Saved";
  el.className="save-status "+(isDirty?"unsaved":"saved");
}
function cacheState(){localStorage.setItem(LOCAL_CACHE_KEY,JSON.stringify(state))}
function save(){isDirty=true;cacheState();updateSaveStatus()}

function stableStateFromCloud(payload){
  if(!payload)return structuredClone(defaults);
  if(Array.isArray(payload.recipes)&&Array.isArray(payload.rawMaterials))return payload;
  if(payload.stableState?.recipes)return payload.stableState;
  if(payload.state?.recipes)return payload.state;
  if(payload.recipes){
    const recipes=payload.recipes.map((r,idx)=>({
      code:(r.code ?? r.recipeCode ?? "").trim().toUpperCase(),
      productionCode:normalizedProductionCode(r.productionCode ?? r.mixCode ?? ""),
      name:r.name||`Recipe ${idx+1}`,
      category:r.category||"General",
      color:String(r.color||inferRecipeColor(r.name||"")).trim(),
      pvcBase:Number(r.pvcBase ?? r.actualResinKg)||100,
      selected:Array.isArray(payload.selectedRecipeIds)?payload.selectedRecipeIds.includes(r.id):idx===0,
      dailyProduction:Number((payload.recipeDailyProds||{})[r.id] ?? r.dailyProduction ?? r.expectedDailyProductionKg)||0,
      ingredients:(r.ingredients||[]).map(i=>({
        material:(i.material ?? i.materialName ?? i.name ?? "").trim(),
        grade:(i.grade ?? "").trim(),
        kg:Number(i.kg ?? i.actualKg)||0
      }))
    }));
    const stocks=(payload.stocks||[]).map(s=>{
      const label=(s.materialName||s.grade||s.material||"").trim();
      let match=null;
      recipes.some(r=>r.ingredients.some(i=>{
        if((i.grade||i.material).trim().toLowerCase()===label.toLowerCase()){match=i;return true}
        return false;
      }));
      return {material:match?.material||(!match?label:""),grade:match?.grade||"",kg:Number(s.kg ?? s.stockKg)||0};
    });
    return {activeRecipe:0,recipes:recipes.length?recipes:structuredClone(defaults.recipes),stocks};
  }
  return structuredClone(defaults);
}

async function saveNow(){
  cacheState();
  try{
    setCloudStatus("Saving...","syncing");
    const response=await fetch("api.php?action=material_state",{method:"PUT",credentials:"same-origin",headers:{"Content-Type":"application/json"},body:JSON.stringify({state})});
    if(response.status===401){location.href="login.php";return false}
    const data=await response.json();
    if(!response.ok)throw new Error(data.error||"Save failed.");
    isDirty=false;updateSaveStatus();setCloudStatus("Hostinger Saved","online");toast("Saved successfully.");return true;
  }catch(error){
    console.error(error);setCloudStatus("Save Error","error");toast("Save failed: "+error.message);return false;
  }
}

async function loadCloudState(){
  setCloudStatus("Loading...","syncing");
  const response=await fetch("api.php?action=material_state",{credentials:"same-origin"});
  if(response.status===401){location.href="login.php";return}
  const data=await response.json();
  if(!response.ok)throw new Error(data.error||"Load failed.");
  if(data.state){
    state=stableStateFromCloud(data.state);
  }else{
    state=structuredClone(defaults);
    isDirty=true;
    await saveNow();
  }
  migrateState();cacheState();isDirty=false;updateSaveStatus();renderDashboard();renderRecipes();renderRawMaterials();setCloudStatus("Hostinger Connected","online");
}
window.addEventListener("beforeunload",e=>{if(isDirty){e.preventDefault();e.returnValue=""}});
function total(r){return r.ingredients.reduce((s,i)=>s+(+i.kg||0),0)}
function fmt(n,d=4){
  const num=Number(n);
  if(!Number.isFinite(num)||num===0)return "0";
  return Number(num.toFixed(d)).toLocaleString(undefined,{maximumFractionDigits:d});
}
function inputNumber(n,d=6){
  const num=Number(n);
  if(!Number.isFinite(num)||num===0)return "0";
  return Number(num.toFixed(d)).toString();
}
function formatKgInput(n){
  const num=Number(String(n??"").replace(/,/g,""));
  if(!Number.isFinite(num))return "0.00";
  const abs=Math.abs(num);
  const decimals=(abs===0||abs>=1)?2:(abs>=0.001?3:4);
  return num.toLocaleString("en-US",{minimumFractionDigits:decimals,maximumFractionDigits:decimals});
}
function parseKgInput(value){
  const num=Number(String(value??"").replace(/,/g,"").trim());
  return Number.isFinite(num)?num:0;
}
function norm(s){return (s||"").trim().toLowerCase()}
function esc(s){return String(s).replace(/[&<>"']/g,m=>({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[m]))}


function itemMaterial(item){
  return (item.material ?? item.name ?? "").trim();
}
function itemGrade(item){
  return (item.grade ?? "").trim();
}
function itemKey(item){
  return (itemMaterial(item).toLowerCase()+"||"+itemGrade(item).toLowerCase());
}
function fullItemLabel(item){
  const m=itemMaterial(item),g=itemGrade(item);
  return m&&g?m+" — "+g:(g||m||"Unnamed Material");
}
function compactItemLabel(item){
  return itemGrade(item)||itemMaterial(item)||"Unnamed Material";
}
function makeMaterialId(){
  return "rm-"+Date.now().toString(36)+"-"+Math.random().toString(36).slice(2,8);
}

function normalizedProductionCode(value){
  const text=String(value||"").trim().toUpperCase().replace(/\s+/g,"");
  const match=text.match(/^(MP|MF)-?(\d+)$/);
  return match?`${match[1]}-${String(Number(match[2])).padStart(2,"0")}`:text;
}
function embeddedProductionCode(name){
  const match=String(name||"").match(/(?:^|[\s,;-])(MP|MF)\s*-\s*(\d+)(?=$|[\s,;.-])/i);
  return match?normalizedProductionCode(`${match[1]}-${match[2]}`):"";
}
function cleanRecipeName(name){
  return String(name||"")
    .replace(/([\s,;-])(MP|MF)\s*-\s*\d+(?=$|[\s,;.-])/ig,"")
    .replace(/\s+,/g,",")
    .replace(/,{2,}/g,",")
    .replace(/[\s,;-]+$/g,"")
    .replace(/\s{2,}/g," ")
    .trim();
}
function inferRecipeColor(name){
  const text=String(name||"").toLowerCase();
  if(text.includes("orange"))return "Orange";
  if(text.includes("white"))return "White";
  if(text.includes("black"))return "Black";
  if(text.includes("grey")||text.includes("gray"))return "Grey";
  return "";
}

function migrateState(){
  state.recipes=(state.recipes||[]).map(r=>{
    const originalName=String(r.name||"").trim();
    const productionCode=normalizedProductionCode(r.productionCode||embeddedProductionCode(originalName));
    const name=productionCode?cleanRecipeName(originalName):originalName;
    return {
      ...r,
      code:String(r.code ?? r.recipeCode ?? "").trim().toUpperCase(),
      productionCode,
      name,
      color:String(r.color||inferRecipeColor(name)).trim(),
      ingredients:(r.ingredients||[]).map(i=>({
        ...i,
        material:itemMaterial(i),
        grade:itemGrade(i),
        kg:Number(i.kg)||0
      }))
    };
  });
  state.stocks=(state.stocks||[]).map(s=>({
    ...s,
    material:itemMaterial(s),
    grade:itemGrade(s),
    kg:Number(s.kg)||0
  }));

  const existing=Array.isArray(state.rawMaterials)?state.rawMaterials:[];
  const materialMap=new Map();
  existing.forEach(m=>{
    const normalized={
      id:m.id||makeMaterialId(),
      material:itemMaterial(m),
      grade:itemGrade(m),
      country:String(m.country||"").trim(),
      company:String(m.company||"").trim(),
      stockKg:Number(m.stockKg ?? m.kg)||0
    };
    if(normalized.material||normalized.grade) materialMap.set(itemKey(normalized),normalized);
  });

  state.recipes.forEach(r=>(r.ingredients||[]).forEach(i=>{
    if(!itemMaterial(i)&&!itemGrade(i))return;
    const key=itemKey(i);
    if(!materialMap.has(key)){
      const stock=state.stocks.find(s=>itemKey(s)===key);
      materialMap.set(key,{
        id:makeMaterialId(),material:itemMaterial(i),grade:itemGrade(i),
        country:"",company:"",stockKg:Number(stock?.kg)||0
      });
    }
  }));
  state.stocks.forEach(s=>{
    if(!itemMaterial(s)&&!itemGrade(s))return;
    const key=itemKey(s);
    if(materialMap.has(key)){
      const current=materialMap.get(key);
      if(!current.stockKg)current.stockKg=Number(s.kg)||0;
    }else{
      materialMap.set(key,{id:makeMaterialId(),material:itemMaterial(s),grade:itemGrade(s),country:"",company:"",stockKg:Number(s.kg)||0});
    }
  });
  state.rawMaterials=[...materialMap.values()];
}
migrateState();
function renderDashboard(){
  const box=document.getElementById("recipeSelection");box.innerHTML="";
  state.recipes.forEach((r,i)=>{
    const d=document.createElement("div");
    d.className="recipe-select-card"+(r.selected?" selected":"");
    d.innerHTML=`
      <input class="check" type="checkbox" ${r.selected?"checked":""}>
      <div><h3>${r.productionCode?`<span class="recipe-code-badge">${esc(r.productionCode)}</span>`:""}${r.code?`<span class="recipe-code-badge">${esc(r.code)}</span>`:""}${esc(r.name)}</h3><p>${r.ingredients.length} ingredients · Total batch ${fmt(total(r))} kg</p></div>
      <div><div class="label">Daily Production (kg/day)</div><input type="number" min="0" step="1" value="${r.dailyProduction||0}"></div>
      <div><div class="label">Material Consumption</div><button class="btn soft full">View Recipe</button></div>`;
    d.querySelector(".check").addEventListener("change",e=>{r.selected=e.target.checked;save();renderDashboard()});
    d.querySelector('input[type="number"]').addEventListener("input",e=>{r.dailyProduction=+e.target.value||0;save();updateSummary()});
    d.querySelector("button").addEventListener("click",()=>{state.activeRecipe=i;save();showPage("recipes");renderRecipes()});
    box.appendChild(d);
  });
  renderStocks();renderMaterials();updateSummary();
}
function updateSummary(){
  const sel=state.recipes.filter(r=>r.selected);
  document.getElementById("selectedCount").textContent=sel.length;
  document.getElementById("totalDailyProduction").textContent=fmt(sel.reduce((s,r)=>s+(+r.dailyProduction||0),0),0)+" kg";
  document.getElementById("trackedCount").textContent=state.stocks.length;
}
function allMaterialItems(){
  const map=new Map();
  state.recipes.forEach(r=>(r.ingredients||[]).forEach(i=>{
    if(itemMaterial(i)||itemGrade(i)) map.set(itemKey(i),{material:itemMaterial(i),grade:itemGrade(i)});
  }));
  return [...map.values()].sort((a,b)=>fullItemLabel(a).localeCompare(fullItemLabel(b)));
}

function renderStocks(){
  const body=document.getElementById("stockBody");
  body.innerHTML="";
  const allItems=allMaterialItems();

  state.stocks.forEach((s,index)=>{
    const usedByOthers=new Set(state.stocks.filter((_,i)=>i!==index).map(itemKey));
    const available=allItems.filter(item=>!usedByOthers.has(itemKey(item))||itemKey(item)===itemKey(s));

    const tr=document.createElement("tr");
    tr.innerHTML=`
      <td>
        <div class="combo-wrap">
          <input class="combo-input" value="${esc(compactItemLabel(s))}" placeholder="Select material / grade" autocomplete="off">
          <span class="combo-arrow">▼</span>
          <div class="combo-list"></div>
        </div>
      </td>
      <td><input class="num" type="number" min="0" step="0.01" value="${Number(s.kg)||0}"></td>
      <td class="center"><button class="btn danger">×</button></td>`;

    const comboInput=tr.querySelector(".combo-input");
    const comboList=tr.querySelector(".combo-list");
    const qty=tr.querySelector('input[type="number"]');

    function fillOptions(filterText=""){
      const q=filterText.trim().toLowerCase();
      const filtered=available.filter(item=>fullItemLabel(item).toLowerCase().includes(q));
      comboList.innerHTML="";
      if(!filtered.length){
        comboList.innerHTML='<div class="combo-empty">No matching material</div>';
        return;
      }
      filtered.forEach(item=>{
        const option=document.createElement("div");
        option.className="combo-option";
        option.innerHTML=`<strong>${esc(itemMaterial(item)||"—")}</strong><br>
          <span style="color:var(--muted)">${esc(itemGrade(item)||"No grade / trade name")}</span>`;
        option.addEventListener("mousedown",e=>{
          e.preventDefault();
          s.material=itemMaterial(item);
          s.grade=itemGrade(item);
          comboInput.value=compactItemLabel(s);
          comboList.classList.remove("open");
          save();
          renderDashboard();
        });
        comboList.appendChild(option);
      });
    }

    comboInput.addEventListener("focus",()=>{fillOptions("");comboList.classList.add("open")});
    comboInput.addEventListener("input",e=>{fillOptions(e.target.value);comboList.classList.add("open")});
    comboInput.addEventListener("blur",()=>setTimeout(()=>{
      comboList.classList.remove("open");
      comboInput.value=compactItemLabel(s);
    },120));

    qty.addEventListener("input",e=>{s.kg=Number(e.target.value)||0;save()});
    tr.querySelector("button").addEventListener("click",()=>{
      state.stocks.splice(index,1);
      save();
      renderDashboard();
    });
    body.appendChild(tr);
  });
}

function renderMaterials(){renderMaterialChecks()}

function syncRawMaterialsFromRecipes(){
  const before=(state.rawMaterials||[]).length;
  migrateState();
  const added=state.rawMaterials.length-before;
  save();
  renderRawMaterials();
  toast(added>0?added+" material(s) added from recipes.":"Raw materials are already synchronized.");
}

function renderRawMaterials(){
  const body=document.getElementById("rawMaterialBody");
  if(!body)return;
  const search=(document.getElementById("materialSearch")?.value||"").trim().toLowerCase();
  const sortBy=document.getElementById("materialSortBy")?.value||"name";
  let rows=(state.rawMaterials||[]).map((m,index)=>({m,index})).filter(({m})=>{
    const text=[m.material,m.grade,m.country,m.company].join(" ").toLowerCase();
    return !search||text.includes(search);
  });
   
  // Apply sorting
  rows.sort(({m:m1},{m:m2})=>{
    if(sortBy==="name"){
      const a=(m1.material||"").toLowerCase();
      const b=(m2.material||"").toLowerCase();
      return a.localeCompare(b);
    }else if(sortBy==="name-desc"){
      const a=(m1.material||"").toLowerCase();
      const b=(m2.material||"").toLowerCase();
      return b.localeCompare(a);
    }else if(sortBy==="stock"){
      return (Number(m2.stockKg||0))-(Number(m1.stockKg||0));
    }else if(sortBy==="stock-asc"){
      return (Number(m1.stockKg||0))-(Number(m2.stockKg||0));
    }
    return 0;
  });
   
  body.innerHTML="";
  document.getElementById("materialTotalBadge").textContent=(state.rawMaterials||[]).length+" Material"+((state.rawMaterials||[]).length===1?"":"s");
  document.getElementById("rawMaterialEmpty").classList.toggle("hidden",rows.length>0);

  rows.forEach(({m,index})=>{
    const tr=document.createElement("tr");
    tr.innerHTML=`
      <td><input value="${esc(m.material||"")}" placeholder="Material name"></td>
      <td><input value="${esc(m.grade||"")}" placeholder="Grade / Trade Name"></td>
      <td><input value="${esc(m.country||"")}" placeholder="Country"></td>
      <td><input value="${esc(m.company||"")}" placeholder="Company"></td>
      <td><input class="num" type="text" inputmode="decimal" value="${formatKgInput(m.stockKg||0)}"></td>
      <td class="center"><button class="btn danger">×</button></td>`;
    const inputs=tr.querySelectorAll("input");
    inputs[0].oninput=e=>{m.material=e.target.value;save()};
    inputs[1].oninput=e=>{m.grade=e.target.value;save()};
    inputs[2].oninput=e=>{m.country=e.target.value;save()};
    inputs[3].oninput=e=>{m.company=e.target.value;save()};
    inputs[4].oninput=e=>{m.stockKg=parseKgInput(e.target.value);save()};
    inputs[4].onblur=e=>{m.stockKg=parseKgInput(e.target.value);e.target.value=formatKgInput(m.stockKg);save()};
    tr.querySelector("button").onclick=()=>{
      const label=fullItemLabel(m);
      if(!confirm(`Delete raw material "${label}"? Existing recipes will not be deleted.`))return;
      state.rawMaterials.splice(index,1);
      save();renderRawMaterials();
    };
    body.appendChild(tr);
  });
}

function renderMaterialChecks(){
  const box=document.getElementById("materialChecks");
  if(!box)return;
  const previous=[...box.querySelectorAll('input:checked')].map(x=>x.value);
  const tracked=state.stocks.filter(s=>itemMaterial(s)||itemGrade(s));
  box.innerHTML="";

  if(!tracked.length){
    box.innerHTML='<div class="note" style="grid-column:1/-1">Add materials to Material Stock first.</div>';
    return;
  }

  tracked.forEach(s=>{
    const key=itemKey(s);
    const label=document.createElement("label");
    label.className="material-check";
    label.innerHTML=`<input type="checkbox" value="${esc(key)}" ${previous.includes(key)?"checked":""}>
      <span>${esc(compactItemLabel(s))}</span>`;
    label.querySelector("input").addEventListener("change",e=>{
      if(box.querySelectorAll('input:checked').length>5){
        e.target.checked=false;
        alert("Maximum 5 materials can be calculated together.");
      }
    });
    box.appendChild(label);
  });
}
function renderRecipes(){
  if(!state.recipes.length){
    state.recipes.push({
      code:"",
      productionCode:"",
      name:"New PVC Recipe",
      category:"General",
      color:"",
      pvcBase:100,
      selected:false,
      dailyProduction:0,
      ingredients:[
        {material:"PVC Resin",grade:"",kg:100},
        {material:"Stabilizer",grade:"",kg:0}
      ]
    });
    state.activeRecipe=0;
  }

  if(state.activeRecipe<0||state.activeRecipe>=state.recipes.length)state.activeRecipe=0;
  state.recipes.forEach(r=>{if(!r.category)r.category="General";if(r.code==null)r.code="";if(r.productionCode==null)r.productionCode="";if(r.color==null)r.color=""});

  const search=(document.getElementById("recipeSearch").value||"").trim().toLowerCase();
  const selectedCategory=document.getElementById("recipeCategoryFilter").value||"";
  const categories=[...new Set(state.recipes.map(r=>(r.category||"General").trim()).filter(Boolean))].sort();

  const categorySelect=document.getElementById("recipeCategoryFilter");
  categorySelect.innerHTML='<option value="">All Categories</option>'+
    categories.map(c=>`<option value="${esc(c)}">${esc(c)}</option>`).join("");
  if(categories.includes(selectedCategory))categorySelect.value=selectedCategory;

  document.getElementById("recipeTotalBadge").textContent=
    state.recipes.length+" Recipe"+(state.recipes.length===1?"":"s");

  const matches=state.recipes.map((r,i)=>({r,i})).filter(({r})=>{
    const text=((r.productionCode||"")+" "+(r.code||"")+" "+(r.name||"")+" "+(r.category||"")+" "+(r.color||"")).toLowerCase();
    return(!search||text.includes(search))&&(!selectedCategory||(r.category||"General")===selectedCategory);
  });

  const selector=document.getElementById("recipeSelector");
  selector.innerHTML="";
  if(!matches.length){
    selector.innerHTML='<option value="">No recipes match the current filter</option>';
    return;
  }
  if(!matches.some(x=>x.i===state.activeRecipe))state.activeRecipe=matches[0].i;
  matches.forEach(({r,i})=>{
    const option=document.createElement("option");
    option.value=i;
    option.textContent=(r.productionCode?"["+r.productionCode+"] ":"")+(r.code?"["+r.code+"] ":"")+r.name+" — "+(r.category||"General");
    selector.appendChild(option);
  });
  selector.value=String(state.activeRecipe);

  const r=state.recipes[state.activeRecipe];
  document.getElementById("recipeCode").value=r.code||"";
  document.getElementById("productionCode").value=r.productionCode||"";
  document.getElementById("recipeName").value=r.name||"";
  document.getElementById("recipeCategory").value=r.category||"General";
  document.getElementById("recipeColor").value=r.color||"";
  document.getElementById("pvcBase").value=inputNumber(r.pvcBase||0);
  document.getElementById("ingredientsCount").value=r.ingredients.length;

  const body=document.getElementById("ingredientBody");
  body.innerHTML="";
  const t=total(r);

  const rawMaterials=(state.rawMaterials||[]).filter(m=>itemMaterial(m)||itemGrade(m));
  const materialNames=[...new Set(rawMaterials.map(itemMaterial).filter(Boolean))]
    .sort((a,b)=>a.localeCompare(b));

  r.ingredients.forEach((ing,i)=>{
    const kg=+ing.kg||0;
    const pct=t?kg/t*100:0;
    const phr=r.pvcBase?kg/r.pvcBase*100:0;
    const currentMaterial=itemMaterial(ing);
    const currentGrade=itemGrade(ing);

    // Keep old recipe values visible even if a raw-material record is later removed.
    const materialOptions=[...materialNames];
    if(currentMaterial&&!materialOptions.some(x=>norm(x)===norm(currentMaterial))) materialOptions.push(currentMaterial);
    materialOptions.sort((a,b)=>a.localeCompare(b));

    const gradeNamesForMaterial=material=>[...new Set(
      rawMaterials
        .filter(m=>norm(itemMaterial(m))===norm(material))
        .map(itemGrade)
    )].sort((a,b)=>a.localeCompare(b));

    const gradeOptions=gradeNamesForMaterial(currentMaterial);
    if(currentGrade&&!gradeOptions.some(x=>norm(x)===norm(currentGrade))) gradeOptions.push(currentGrade);

    const tr=document.createElement("tr");
    tr.innerHTML=`
      <td><select class="ingredient-material" aria-label="Material">
        <option value="">Select material</option>
        ${materialOptions.map(name=>`<option value="${esc(name)}" ${norm(name)===norm(currentMaterial)?"selected":""}>${esc(name)}</option>`).join("")}
      </select></td>
      <td><select class="ingredient-grade" aria-label="Grade / Trade Name">
        <option value="">${gradeOptions.length?"No grade / trade name":"Select material first"}</option>
        ${gradeOptions.filter(Boolean).map(grade=>`<option value="${esc(grade)}" ${norm(grade)===norm(currentGrade)?"selected":""}>${esc(grade)}</option>`).join("")}
      </select></td>
      <td><input class="num ingredient-kg" type="number" step="any" value="${formatKgInput(ing.kg)}"></td>
      <td class="num">${fmt(pct,4)}%</td>
      <td class="num">${fmt(phr,4)}</td>
      <td><button class="btn danger">×</button></td>`;

    const materialSelect=tr.querySelector(".ingredient-material");
    const gradeSelect=tr.querySelector(".ingredient-grade");
    const kgInput=tr.querySelector(".ingredient-kg");

    function rebuildGradeOptions(selectedMaterial,preferredGrade=""){
      const grades=gradeNamesForMaterial(selectedMaterial);
      gradeSelect.innerHTML=`<option value="">${grades.length?"No grade / trade name":"No grades registered"}</option>`+
        grades.filter(Boolean).map(grade=>`<option value="${esc(grade)}">${esc(grade)}</option>`).join("");
      const matching=grades.find(grade=>norm(grade)===norm(preferredGrade));
      gradeSelect.value=matching||"";
      ing.grade=gradeSelect.value;
    }

    materialSelect.onchange=e=>{
      ing.material=e.target.value;
      rebuildGradeOptions(ing.material,"");
      save();
    };
    gradeSelect.onchange=e=>{ing.grade=e.target.value;save()};
    kgInput.oninput=e=>{ing.kg=e.target.value;save()};
    kgInput.onblur=e=>{ing.kg=+e.target.value||0;save();renderRecipes()};
    kgInput.onkeydown=e=>{if(e.key==="Enter"){e.preventDefault();e.target.blur()}};

    tr.querySelector("button").onclick=()=>{
      r.ingredients.splice(i,1);
      save();
      renderRecipes();
    };
    body.appendChild(tr);
  });

  const totalPhr=r.pvcBase?t/r.pvcBase*100:0;
  document.getElementById("totalBatchValue").textContent=fmt(t,4);
  document.getElementById("totalPercentValue").textContent=t?"100.00%":"0.00%";
  document.getElementById("totalPhrValue").textContent=fmt(totalPhr,4);
}

function normalizeExcelHeader(value){
  return String(value||"").trim().toLowerCase().replace(/\s+/g," ").replace(/[()]/g,"");
}

function importRecipesFromRows(rows,showMessage=true){
  if(!rows.length) throw new Error("The Excel sheet is empty.");
  const keys=Object.keys(rows[0]);
  const keyMap={};
  keys.forEach(k=>keyMap[normalizeExcelHeader(k)]=k);
  function findKey(names){
    for(const name of names){
      const key=keyMap[normalizeExcelHeader(name)];
      if(key) return key;
    }
    return null;
  }
  const codeKey=findKey(["Recipe Code","Code","Mix Code","Formula Code"]);
  const productionCodeKey=findKey(["Production Code","Production Mix Code","MP/MF Code","Operational Code"]);
  const recipeKey=findKey(["Recipe Name","Recipe"]);
  const categoryKey=findKey(["Recipe Category","Category"]);
  const colorKey=findKey(["Color","Recipe Color","Mix Color"]);
  const pvcBaseKey=findKey(["Actual PVC Resin in Batch (kg)","PVC Resin Base (kg)","PVC Resin Base","PVC Base"]);
  const dailyKey=findKey(["Daily Production (kg/day)","Daily Production","Production kg/day"]);
  const materialKey=findKey(["Material","Ingredient","Raw Material"]);
  const gradeKey=findKey(["Grade / Trade Name","Grade","Trade Name","Commercial Name"]);
  const kgKey=findKey(["Actual kg / Batch","kg / Batch","kg/Batch","Quantity kg","kg"]);
  if(!recipeKey || !kgKey || (!materialKey && !gradeKey)){
    throw new Error("Required columns: Recipe Name, Actual kg / Batch, and at least Material or Grade / Trade Name.");
  }
  const grouped=new Map();
  rows.forEach(row=>{
    const recipeName=String(row[recipeKey]??"").trim();
    const material=materialKey?String(row[materialKey]??"").trim():"";
    const grade=gradeKey?String(row[gradeKey]??"").trim():"";
    if(!recipeName || (!material && !grade)) return;
    const incomingRecipeCode=codeKey?String(row[codeKey]??"").trim().toUpperCase():"";
    const incomingProductionCode=normalizedProductionCode(productionCodeKey?row[productionCodeKey]:embeddedProductionCode(recipeName));
    const cleanName=incomingProductionCode?cleanRecipeName(recipeName):recipeName;
    const groupKey=incomingRecipeCode?`recipe:${incomingRecipeCode}`:(incomingProductionCode?`production:${incomingProductionCode}`:`name:${cleanName.toLowerCase()}`);
    if(!grouped.has(groupKey)){
      grouped.set(groupKey,{
        code:incomingRecipeCode,
        productionCode:incomingProductionCode,
        name:cleanName,
        category:categoryKey?String(row[categoryKey]??"General").trim()||"General":"General",
        color:colorKey?String(row[colorKey]??"").trim():inferRecipeColor(cleanName),
        pvcBase:pvcBaseKey?Number(row[pvcBaseKey])||0:0,
        dailyProduction:dailyKey?Number(row[dailyKey])||0:0,
        selected:false,
        ingredients:[]
      });
    }
    const ingredientKg=Number(row[kgKey])||0;
    grouped.get(groupKey).ingredients.push({material,grade,kg:ingredientKg});
    const resinText=(material+" "+grade).toLowerCase();
    const looksLikeResin=resinText.includes("pvc")||resinText.includes("resin")||resinText.includes("k57")||resinText.includes("k-57")||resinText.includes("k67")||resinText.includes("k-67");
    if(looksLikeResin && ingredientKg>0) grouped.get(groupKey).pvcBase=ingredientKg;
  });
  const imported=[...grouped.values()].filter(r=>r.ingredients.length);
  imported.forEach(r=>{
    if(!r.pvcBase || r.pvcBase<=0){
      const resin=r.ingredients.find(i=>{
        const text=((i.material||"")+" "+(i.grade||"")).toLowerCase();
        return text.includes("pvc")||text.includes("resin");
      });
      r.pvcBase=resin?Number(resin.kg)||100:100;
    }
  });
  if(!imported.length) throw new Error("No valid recipe rows were found.");
  let added=0,updated=0;
  const firstNewRecipeIndex=state.recipes.length;

  imported.forEach(recipe=>{
    const existingIndex=state.recipes.findIndex(r=>
      (recipe.code&&String(r.code||"").trim().toUpperCase()===recipe.code)
      ||(!recipe.code&&recipe.productionCode&&normalizedProductionCode(r.productionCode)===recipe.productionCode)
      ||(!recipe.code&&!recipe.productionCode&&(r.name||"").trim().toLowerCase()===recipe.name.trim().toLowerCase())
    );

    if(existingIndex>=0){
      const current=state.recipes[existingIndex];
      state.recipes[existingIndex]={
        ...current,
        ...recipe,
        code:recipe.code||current.code||"",
        productionCode:recipe.productionCode||current.productionCode||"",
        color:recipe.color||current.color||inferRecipeColor(recipe.name),
        selected:current.selected??recipe.selected,
        dailyProduction:recipe.dailyProduction||current.dailyProduction||0
      };
      updated++;
      return;
    }

    state.recipes.push(recipe);
    added++;
  });

  // Move to the first newly imported recipe only when new recipes were added.
  if(added>0) state.activeRecipe=firstNewRecipeIndex;

  save();
  renderRecipes();
  renderDashboard();

  if(showMessage){
    alert(`Recipes import completed.\nAdded: ${added}\nUpdated: ${updated}`);
  }

  return {added,updated};
}

function importRawMaterialsFromRows(rows){
  if(!rows.length) return {added:0,replaced:0};
  const keys=Object.keys(rows[0]);
  const keyMap={};
  keys.forEach(k=>keyMap[normalizeExcelHeader(k)]=k);
  function findKey(names){
    for(const name of names){
      const key=keyMap[normalizeExcelHeader(name)];
      if(key) return key;
    }
    return null;
  }
  const materialKey=findKey(["MATERIAL NAME","Material Name","Material"]);
  const gradeKey=findKey(["GRADE / TRADE NAME","Grade / Trade Name","Grade","Trade Name"]);
  const countryKey=findKey(["COUNTRY","Country","Country of Origin"]);
  const companyKey=findKey(["COMPANY","Company","Manufacturer","Supplier"]);
  const stockKey=findKey(["STOCK KG","Stock KG","Stock","Stock (kg)"]);
  if(!materialKey) throw new Error('Sheet "Raw materials" must contain a MATERIAL NAME column.');

  let added=0,replaced=0;
  rows.forEach(row=>{
    const material=String(row[materialKey]??"").trim();
    if(!material) return;
    const incoming={
      material,
      grade:gradeKey?String(row[gradeKey]??"").trim():"",
      country:countryKey?String(row[countryKey]??"").trim():"",
      company:companyKey?String(row[companyKey]??"").trim():"",
      stockKg:stockKey?parseKgInput(row[stockKey]):0
    };
    const existingIndex=state.rawMaterials.findIndex(m=>
      norm(m.material)===norm(material)&&norm(m.grade)===norm(incoming.grade)
    );
    if(existingIndex>=0){
      state.rawMaterials[existingIndex]={...state.rawMaterials[existingIndex],...incoming,id:state.rawMaterials[existingIndex].id||makeMaterialId()};
      replaced++;
    }else{
      state.rawMaterials.push({id:makeMaterialId(),...incoming});
      added++;
    }
  });
  return {added,replaced};
}

let pendingImportType="";

async function handleExcelImport(file,importType){
  if(typeof XLSX==="undefined") throw new Error("Excel library could not be loaded. Check the internet connection.");
  const buffer=await file.arrayBuffer();
  const workbook=XLSX.read(buffer,{type:"array"});

  const recipeSheetName=workbook.SheetNames.find(name=>name.trim().toLowerCase()==="recipes import");
  const rawSheetName=workbook.SheetNames.find(name=>name.trim().toLowerCase()==="raw materials");

  if(importType==="recipes"){
    if(!recipeSheetName) throw new Error('Sheet "Recipes Import" was not found. Raw materials were not changed.');
    const recipeRows=XLSX.utils.sheet_to_json(workbook.Sheets[recipeSheetName],{defval:""});
    importRecipesFromRows(recipeRows,true);
    return;
  }

  if(importType==="rawMaterials"){
    if(!rawSheetName) throw new Error('Sheet "Raw materials" was not found. Recipes were not changed.');
    const rawRows=XLSX.utils.sheet_to_json(workbook.Sheets[rawSheetName],{defval:""});
    const rawResult=importRawMaterialsFromRows(rawRows);
    save();
    renderRawMaterials();
    renderRecipes();
    renderDashboard();
    alert(`Raw materials import completed.\nAdded: ${rawResult.added}\nUpdated from Excel: ${rawResult.replaced}\nExisting materials not present in Excel were kept.`);
    return;
  }

  if(importType==="both"){
    if(!recipeSheetName || !rawSheetName){
      const missing=[];
      if(!recipeSheetName) missing.push('Recipes Import');
      if(!rawSheetName) missing.push('Raw materials');
      throw new Error(`Required sheet(s) not found: ${missing.join(", ")}. Nothing was imported.`);
    }
    const recipeRows=XLSX.utils.sheet_to_json(workbook.Sheets[recipeSheetName],{defval:""});
    const rawRows=XLSX.utils.sheet_to_json(workbook.Sheets[rawSheetName],{defval:""});
    const recipeResult=importRecipesFromRows(recipeRows,false);
    const rawResult=importRawMaterialsFromRows(rawRows);
    save();
    renderRawMaterials();
    renderRecipes();
    renderDashboard();
    alert(`Combined import completed.\n\nRecipes added: ${recipeResult.added}\nRecipes updated: ${recipeResult.updated}\nRaw materials added: ${rawResult.added}\nRaw materials updated from Excel: ${rawResult.replaced}\n\nExisting raw materials not present in Excel were kept.`);
    return;
  }

  throw new Error("Choose what you want to import first.");
}

function downloadExcelTemplate(){
  if(typeof XLSX==="undefined"){
    alert("Excel library could not be loaded.");
    return;
  }
  const data=[
    ["Recipe Code","Production Code","Recipe Name","Recipe Category","Color","Actual PVC Resin in Batch (kg)","Daily Production (kg/day)","Material","Grade / Trade Name","Actual kg / Batch"],
    ["KH-120","MF-01","Orange PVC Fitting","Fitting","Orange",250,3000,"PVC Resin","PVC K-57",250],
    ["KH-120","MF-01","Orange PVC Fitting","Fitting","Orange",250,3000,"Calcium Carbonate","",7.5],
    ["KH-120","MF-01","Orange PVC Fitting","Fitting","Orange",250,3000,"Stabilizer","SAG-1015",13.75]
  ];
  const sheet=XLSX.utils.aoa_to_sheet(data);
  sheet["!cols"]=[{wch:16},{wch:18},{wch:28},{wch:18},{wch:14},{wch:30},{wch:26},{wch:24},{wch:24},{wch:18}];
  const workbook=XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook,sheet,"Recipes Import");
  const rawData=[
    ["MATERIAL NAME","GRADE / TRADE NAME","COUNTRY","COMPANY","STOCK KG"],
    ["PVC Resin","PVC K-67","Saudi Arabia","SABIC",33000],
    ["Stabilizer","SAG-1015","UAE","Sun Ace",15500]
  ];
  const rawSheet=XLSX.utils.aoa_to_sheet(rawData);
  rawSheet["!cols"]=[{wch:24},{wch:26},{wch:20},{wch:24},{wch:16}];
  XLSX.utils.book_append_sheet(workbook,rawSheet,"Raw materials");
  XLSX.writeFile(workbook,"Material_Planner_Import_Template.xlsx");
}

function exportCompleteBackup(){
  if(typeof XLSX==="undefined"){alert("Excel library could not be loaded.");return}
  const wb=XLSX.utils.book_new();
  const keys=state.recipes.map((_,i)=>`R${String(i+1).padStart(4,"0")}`);
  const info=[{Field:"Backup Type",Value:"Material Planner Complete Backup"},{Field:"Backup Version",Value:2},{Field:"Exported At",Value:new Date().toISOString()},{Field:"Active Recipe Index",Value:state.activeRecipe||0}];
  const recipes=state.recipes.map((r,i)=>({"Recipe Key":keys[i],"Recipe Code":r.code||"","Production Code":r.productionCode||"","Recipe Name":r.name||"","Category":r.category||"General","Color":r.color||"","PVC Resin Base (kg)":Number(r.pvcBase)||0,"Selected":r.selected?"Yes":"No","Daily Production (kg/day)":Number(r.dailyProduction)||0}));
  const ingredients=[];
  state.recipes.forEach((r,i)=>(r.ingredients||[]).forEach((item,line)=>ingredients.push({"Recipe Key":keys[i],"Line No":line+1,"Material":itemMaterial(item),"Grade / Trade Name":itemGrade(item),"Actual kg / Batch":Number(item.kg)||0})));
  const materials=(state.rawMaterials||[]).map(m=>({"Material ID":m.id||makeMaterialId(),"Material Name":itemMaterial(m),"Grade / Trade Name":itemGrade(m),"Country":m.country||"","Company":m.company||"","Stock (kg)":Number(m.stockKg)||0}));
  const stocks=(state.stocks||[]).map(s=>({"Material":itemMaterial(s),"Grade / Trade Name":itemGrade(s),"Stock (kg)":Number(s.kg)||0}));
  [[info,"Backup Info"],[recipes,"Recipes"],[ingredients,"Ingredients"],[materials,"Raw Materials"],[stocks,"Stock"]].forEach(([rows,name])=>XLSX.utils.book_append_sheet(wb,XLSX.utils.json_to_sheet(rows),name));
  XLSX.writeFile(wb,`Material_Planner_Backup_${new Date().toISOString().slice(0,10)}.xlsx`);
}

async function importCompleteBackup(file){
  if(typeof XLSX==="undefined")throw new Error("Excel library could not be loaded.");
  const wb=XLSX.read(await file.arrayBuffer(),{type:"array"});
  const sheet=(name)=>{const key=wb.SheetNames.find(s=>s.trim().toLowerCase()===name.toLowerCase());return key?XLSX.utils.sheet_to_json(wb.Sheets[key],{defval:""}):null};
  const recipeRows=sheet("Recipes"),ingredientRows=sheet("Ingredients"),materialRows=sheet("Raw Materials"),stockRows=sheet("Stock"),infoRows=sheet("Backup Info");
  if(!recipeRows||!ingredientRows||!materialRows||!stockRows)throw new Error("This is not a complete Material Planner backup.");
  const ingredientMap=new Map();
  ingredientRows.forEach(row=>{
    const key=String(row["Recipe Key"]||"");
    if(!ingredientMap.has(key))ingredientMap.set(key,[]);
    ingredientMap.get(key).push({line:Number(row["Line No"])||0,material:String(row.Material||""),grade:String(row["Grade / Trade Name"]||""),kg:Number(row["Actual kg / Batch"])||0});
  });
  const recipes=recipeRows.map((row,index)=>({
    code:String(row["Recipe Code"]||"").trim().toUpperCase(),productionCode:normalizedProductionCode(row["Production Code"]||""),name:String(row["Recipe Name"]||`Recipe ${index+1}`),category:String(row.Category||"General"),color:String(row.Color||inferRecipeColor(row["Recipe Name"]||"")),
    pvcBase:Number(row["PVC Resin Base (kg)"])||100,selected:String(row.Selected||"").toLowerCase()==="yes",dailyProduction:Number(row["Daily Production (kg/day)"])||0,
    ingredients:(ingredientMap.get(String(row["Recipe Key"]||""))||[]).sort((a,b)=>a.line-b.line).map(({material,grade,kg})=>({material,grade,kg}))
  })).filter(r=>r.ingredients.length);
  if(!recipes.length)throw new Error("No valid recipes were found in the backup.");
  const rawMaterials=materialRows.map(row=>({id:String(row["Material ID"]||makeMaterialId()),material:String(row["Material Name"]||""),grade:String(row["Grade / Trade Name"]||""),country:String(row.Country||""),company:String(row.Company||""),stockKg:Number(row["Stock (kg)"])||0})).filter(m=>m.material||m.grade);
  const stocks=stockRows.map(row=>({material:String(row.Material||""),grade:String(row["Grade / Trade Name"]||""),kg:Number(row["Stock (kg)"])||0})).filter(s=>s.material||s.grade);
  const activeRow=(infoRows||[]).find(row=>String(row.Field).trim()==="Active Recipe Index");
  state={activeRecipe:Math.min(Math.max(0,Number(activeRow?.Value)||0),recipes.length-1),recipes,rawMaterials,stocks};
  migrateState();save();renderDashboard();renderRecipes();renderRawMaterials();
  await saveNow();
  alert(`Complete backup imported successfully.\nRecipes: ${recipes.length}\nRaw materials: ${rawMaterials.length}`);
}

function generateUnusedRecipeCode(){
  const used=new Set(
    state.recipes
      .map(r=>String(r.code||"").trim().toUpperCase())
      .filter(Boolean)
  );

  const available=[];
  for(let number=100;number<=990;number+=10){
    const code=`KH-${number}`;
    if(!used.has(code))available.push(code);
  }

  if(!available.length){
    alert("No unused KH recipe codes are available between KH-100 and KH-990.");
    return;
  }

  const code=available[Math.floor(Math.random()*available.length)];
  state.recipes[state.activeRecipe].code=code;
  document.getElementById("recipeCode").value=code;
  save();
  renderRecipes();
  renderDashboard();
  toast(`Generated recipe code: ${code}`);
}

function createNewRecipe(){
  state.recipes.push({
    code:"",
    productionCode:"",
    name:"New PVC Recipe "+(state.recipes.length+1),
    category:"General",
    color:"",
    pvcBase:100,
    selected:false,
    dailyProduction:0,
    ingredients:[
      {material:"PVC Resin",grade:"",kg:100},
      {material:"",grade:"",kg:0}
    ]
  });
  state.activeRecipe=state.recipes.length-1;
  document.getElementById("recipeSearch").value="";
  document.getElementById("recipeCategoryFilter").value="";
  save();
  renderRecipes();
}

function duplicateActiveRecipe(){
  const copy=JSON.parse(JSON.stringify(state.recipes[state.activeRecipe]));
  copy.code="";
  copy.productionCode="";
  copy.name=(copy.name||"Recipe")+" - Copy";
  copy.selected=false;
  state.recipes.push(copy);
  state.activeRecipe=state.recipes.length-1;
  document.getElementById("recipeSearch").value="";
  document.getElementById("recipeCategoryFilter").value="";
  save();
  renderRecipes();
}

function deleteActiveRecipe(){
  if(state.recipes.length===1){
    alert("At least one recipe must remain.");
    return;
  }
  const recipe=state.recipes[state.activeRecipe];
  if(!confirm(`Delete recipe "${recipe.name}"?`)) return;
  state.recipes.splice(state.activeRecipe,1);
  state.activeRecipe=Math.max(0,state.activeRecipe-1);
  save();
  renderRecipes();
  renderDashboard();
}

function updateExportSelection(){
  const boxes=[...document.querySelectorAll('.export-recipe-check')];
  const checked=boxes.filter(box=>box.checked);
  const selectAll=document.getElementById("exportSelectAll");
  selectAll.checked=boxes.length>0&&checked.length===boxes.length;
  selectAll.indeterminate=checked.length>0&&checked.length<boxes.length;
  document.getElementById("exportSelectedCount").textContent=
    checked.length+" recipe"+(checked.length===1?"":"s")+" selected";
  document.getElementById("generatePdfBtn").disabled=checked.length===0;
  boxes.forEach(box=>box.closest(".export-recipe-item").classList.toggle("checked",box.checked));
}

function openExportDialog(){
  if(document.activeElement&&typeof document.activeElement.blur==="function")document.activeElement.blur();
  const list=document.getElementById("exportRecipeList");
  list.innerHTML="";
  state.recipes.forEach((recipe,index)=>{
    const row=document.createElement("label");
    row.className="export-recipe-item";
    const batchWeight=total(recipe);
    row.innerHTML=`
      <input class="export-recipe-check" type="checkbox" value="${index}" ${index===state.activeRecipe?"checked":""}>
      <div>
        <div class="export-recipe-title">${recipe.productionCode?`<span class="recipe-code-badge">${esc(recipe.productionCode)}</span>`:""}${recipe.code?`<span class="recipe-code-badge">${esc(recipe.code)}</span>`:""}${esc(recipe.name||"Unnamed Recipe")}</div>
        <div class="export-recipe-meta">${esc(recipe.category||"General")} · ${(recipe.ingredients||[]).length} ingredients</div>
      </div>
      <div class="export-recipe-total">${fmt(batchWeight,2)} kg</div>`;
    row.querySelector("input").addEventListener("change",updateExportSelection);
    list.appendChild(row);
  });
  document.getElementById("exportOverlay").classList.add("open");
  document.getElementById("exportOverlay").setAttribute("aria-hidden","false");
  updateExportSelection();
}

function closeExportDialog(){
  document.getElementById("exportOverlay").classList.remove("open");
  document.getElementById("exportOverlay").setAttribute("aria-hidden","true");
}

function pdfSafeText(value){
  return String(value??"")
    .replace(/[–—]/g,"-")
    .replace(/[‘’]/g,"'")
    .replace(/[“”]/g,'"');
}

function fitPdfText(doc,value,maxWidth){
  const text=pdfSafeText(value)||"-";
  if(doc.getTextWidth(text)<=maxWidth)return text;
  let low=0,high=text.length;
  while(low<high){
    const mid=Math.ceil((low+high)/2);
    if(doc.getTextWidth(text.slice(0,mid)+"...")<=maxWidth)low=mid;
    else high=mid-1;
  }
  return text.slice(0,Math.max(1,low))+"...";
}

function pdfNumber(value,maxDecimals=4){
  const number=Number(value)||0;
  return number.toLocaleString("en-US",{
    minimumFractionDigits:2,
    maximumFractionDigits:maxDecimals
  });
}

function drawRecipePdfPage(doc,recipe){
  const pageWidth=doc.internal.pageSize.getWidth();
  const batchWeight=total(recipe);
  const totalPhr=recipe.pvcBase?batchWeight/recipe.pvcBase*100:0;
  const ingredients=(recipe.ingredients||[]).filter(item=>itemMaterial(item)||itemGrade(item)||Number(item.kg));
  const rowCount=Math.max(1,ingredients.length);
  const fontSize=rowCount>45?5:rowCount>32?5.8:rowCount>22?7.2:8.6;
  const cellPadding=rowCount>45?.35:rowCount>32?.55:rowCount>22?1.2:2.35;

  doc.setFillColor(47,111,237);
  doc.roundedRect(18,12,pageWidth-36,17,3,3,"F");
  doc.setTextColor(255,255,255);
  doc.setFont("helvetica","bold");
  doc.setFontSize(13);
  doc.text("PVC RECIPE REPORT",22,22.5);
  doc.setFont("helvetica","normal");
  doc.setFontSize(8.5);
  doc.text("Material Planner Pro",pageWidth-22,22.2,{align:"right"});

  doc.setTextColor(29,41,57);
  doc.setFont("helvetica","bold");
  doc.setFontSize(14);
  doc.text(fitPdfText(doc,recipe.name||"Unnamed Recipe",pageWidth-36),18,38);

  doc.setFillColor(247,250,255);
  doc.setDrawColor(223,231,241);
  doc.roundedRect(18,43,pageWidth-36,20,2.5,2.5,"FD");
  const metadata=[
    {x:22,label:"PRODUCTION CODE",value:recipe.productionCode||"-",width:29},
    {x:54,label:"RECIPE CODE",value:recipe.code||"-",width:27},
    {x:84,label:"CATEGORY",value:recipe.category||"General",width:28},
    {x:115,label:"COLOR",value:recipe.color||"-",width:29},
    {x:147,label:"PVC RESIN BASE",value:pdfNumber(recipe.pvcBase,2)+" kg",width:39}
  ];
  metadata.forEach(item=>{
    doc.setFont("helvetica","bold");
    doc.setFontSize(6.7);
    doc.setTextColor(102,112,133);
    doc.text(item.label,item.x,49.5);
    doc.setFontSize(9);
    doc.setTextColor(29,41,57);
    doc.text(fitPdfText(doc,item.value,item.width),item.x,57);
  });

  const rows=ingredients.length?ingredients.map(item=>{
    const kg=Number(item.kg)||0;
    return [
      pdfSafeText(itemMaterial(item)||"-"),
      pdfSafeText(itemGrade(item)||"-"),
      pdfNumber(kg),
      batchWeight?pdfNumber(kg/batchWeight*100,4)+"%":"0.00%",
      recipe.pvcBase?pdfNumber(kg/recipe.pvcBase*100,4):"0.00"
    ];
  }):[["No ingredients","-","0.00","0.00%","0.00"]];

  const pageCountBeforeTable=doc.getNumberOfPages();
  doc.autoTable({
    startY:68,
    margin:{left:18,right:18,bottom:19},
    tableWidth:pageWidth-36,
    head:[["MATERIAL","GRADE / TRADE NAME","ACTUAL KG /\nBATCH","% OF BATCH","PHR"]],
    body:rows,
    foot:[[
      {content:"RECIPE TOTALS",colSpan:2,styles:{halign:"center"}},
      pdfNumber(batchWeight)+" kg",
      batchWeight?"100.00%":"0.00%",
      pdfNumber(totalPhr)
    ]],
    theme:"grid",
    styles:{
      font:"helvetica",fontSize,cellPadding,overflow:"ellipsize",valign:"middle",halign:"center",
      textColor:[52,64,84],lineColor:[223,231,241],lineWidth:.18
    },
    headStyles:{
      fillColor:[47,111,237],textColor:[255,255,255],fontStyle:"bold",halign:"center",
      fontSize:Math.max(fontSize,7),cellPadding:Math.max(cellPadding,1.4)
    },
    footStyles:{
      fillColor:[234,241,255],textColor:[29,41,57],fontStyle:"bold",halign:"center",
      fontSize:Math.max(fontSize,7.2),cellPadding:Math.max(cellPadding,1.6),lineColor:[184,205,248]
    },
    alternateRowStyles:{fillColor:[248,250,253]},
    columnStyles:{
      0:{cellWidth:44},1:{cellWidth:52},2:{cellWidth:29},
      3:{cellWidth:25},4:{cellWidth:24}
    },
    pageBreak:"auto",
    rowPageBreak:"avoid",
    showHead:"firstPage",
    showFoot:"lastPage"
  });
  if(doc.getNumberOfPages()>pageCountBeforeTable){
    throw new Error(`Recipe "${recipe.name||"Unnamed Recipe"}" has too many ingredients to fit on one A4 page.`);
  }
}

async function exportSelectedRecipesPdf(){
  const selectedIndexes=[...document.querySelectorAll('.export-recipe-check:checked')]
    .map(box=>Number(box.value));
  if(!selectedIndexes.length)return;
  if(!window.jspdf||!window.jspdf.jsPDF){
    alert("The PDF component could not be loaded. Check the internet connection and try again.");
    return;
  }

  const button=document.getElementById("generatePdfBtn");
  const originalText=button.textContent;
  button.disabled=true;
  button.textContent="Preparing PDF...";
  await new Promise(resolve=>requestAnimationFrame(resolve));

  try{
    const {jsPDF}=window.jspdf;
    const doc=new jsPDF({orientation:"portrait",unit:"mm",format:"a4",compress:true});
    if(typeof doc.autoTable!=="function")throw new Error("The PDF table component could not be loaded.");
    doc.setProperties({
      title:"PVC Recipes Report",
      subject:"Selected PVC recipe formulations",
      author:"Material Planner Pro",
      creator:"Material Planner Pro"
    });

    selectedIndexes.forEach((recipeIndex,reportIndex)=>{
      if(reportIndex>0)doc.addPage("a4","portrait");
      drawRecipePdfPage(doc,state.recipes[recipeIndex]);
    });

    const pageTotal=doc.getNumberOfPages();
    const generatedDate=new Date().toLocaleDateString("en-GB",{day:"2-digit",month:"short",year:"numeric"});
    for(let page=1;page<=pageTotal;page++){
      doc.setPage(page);
      doc.setDrawColor(223,231,241);
      doc.line(18,281.5,192,281.5);
      doc.setFont("helvetica","normal");
      doc.setFontSize(7.5);
      doc.setTextColor(102,112,133);
      doc.text("Generated "+generatedDate,18,287);
      doc.text(`Page ${page} of ${pageTotal}`,192,287,{align:"right"});
    }

    const datePart=new Date().toISOString().slice(0,10);
    const singleRecipe=selectedIndexes.length===1?state.recipes[selectedIndexes[0]]:null;
    const codePart=singleRecipe&&singleRecipe.code?"_"+String(singleRecipe.code).replace(/[^a-z0-9_-]+/gi,"_"):"";
    doc.save(`PVC_Recipes${codePart}_${datePart}.pdf`);
    closeExportDialog();
    toast(selectedIndexes.length+" recipe"+(selectedIndexes.length===1?"":"s")+" exported to PDF.");
  }catch(error){
    alert("PDF export failed: "+error.message);
  }finally{
    button.textContent=originalText;
    updateExportSelection();
  }
}

function calculateOneMaterial(key){
  const selected=state.recipes.filter(r=>r.selected);
  let daily=0;
  const parts=[];

  selected.forEach(r=>{
    const t=total(r);
    const kg=r.ingredients.filter(i=>itemKey(i)===key).reduce((sum,i)=>sum+(+i.kg||0),0);
    const pct=t?kg/t*100:0;
    const cons=(+r.dailyProduction||0)*pct/100;
    if(cons>0){daily+=cons;parts.push({name:r.name,pct,cons})}
  });

  const stock=state.stocks.find(s=>itemKey(s)===key);
  const stockkg=stock?+stock.kg||0:0;
  const label=stock?compactItemLabel(stock):key;

  if(daily<=0){
    return `<div class="result"><strong>${esc(label)}</strong>
      <div class="note" style="margin-top:10px">This material is not used in the selected recipes.</div></div>`;
  }

  const days=stockkg/daily;
  return `<div class="result">
    <div class="row space">
      <div><strong>${esc(label)}</strong><div class="sub" style="margin:3px 0 0">Combined selected recipes</div></div>
      <div class="days">${fmt(days,1)} days</div>
    </div>
    <div class="result-grid">
      <div class="mini"><span>Available Stock</span><strong>${fmt(stockkg)} kg</strong></div>
      <div class="mini"><span>Total Daily Consumption</span><strong>${fmt(daily)} kg/day</strong></div>
      ${parts.map(p=>`<div class="mini"><span>${esc(p.name)} · ${fmt(p.pct,3)}%</span><strong>${fmt(p.cons)} kg/day</strong></div>`).join("")}
    </div>
  </div>`;
}
function calculate(){
  const result=document.getElementById("resultBox");
  const materials=[...document.querySelectorAll('#materialChecks input:checked')].map(x=>x.value);
  if(!materials.length){result.innerHTML='<div class="note">Select at least one material.</div>';return}
  if(!state.recipes.some(r=>r.selected)){result.innerHTML='<div class="note">Select at least one recipe for calculation.</div>';return}
  result.innerHTML=materials.map(calculateOneMaterial).join("");
}
function showPage(which){
  document.getElementById("dashboardPage").classList.toggle("active",which==="dashboard");
  document.getElementById("recipesPage").classList.toggle("active",which==="recipes");
  document.getElementById("materialsPage").classList.toggle("active",which==="materials");
  document.getElementById("navDashboard").classList.toggle("active",which==="dashboard");
  document.getElementById("navRecipes").classList.toggle("active",which==="recipes");
  document.getElementById("navMaterials").classList.toggle("active",which==="materials");
}
document.getElementById("navDashboard").onclick=()=>{showPage("dashboard");renderDashboard()};
document.getElementById("navRecipes").onclick=()=>{showPage("recipes");renderRecipes()};
document.getElementById("navMaterials").onclick=()=>{showPage("materials");renderRawMaterials()};
document.getElementById("addStock").onclick=()=>{state.stocks.push({material:"",grade:"",kg:0});save();renderDashboard()};
document.getElementById("calculate").onclick=calculate;
document.getElementById("saveNowBtn").onclick=async()=>{
  const ok=await saveNow();
  if(ok){const btn=document.getElementById("saveNowBtn");btn.textContent="Saved ✓";setTimeout(()=>btn.textContent="Save",1200)}
};
document.getElementById("generateRecipeCode").onclick=generateUnusedRecipeCode;
document.getElementById("recipeCode").oninput=e=>{
  const value=e.target.value.toUpperCase().replace(/\s+/g,"");
  e.target.value=value;
  state.recipes[state.activeRecipe].code=value;
  save();
};
document.getElementById("recipeCode").onblur=e=>{
  const code=e.target.value.trim().toUpperCase();
  const duplicate=state.recipes.some((r,i)=>i!==state.activeRecipe&&code&&String(r.code||"").toUpperCase()===code);
  if(duplicate){alert("This recipe code is already used. Please enter a unique code.");e.target.focus();return}
  state.recipes[state.activeRecipe].code=code;save();renderRecipes();renderDashboard();
};

document.getElementById("productionCode").oninput=e=>{
  const value=e.target.value.toUpperCase().replace(/\s+/g,"");
  e.target.value=value;
  state.recipes[state.activeRecipe].productionCode=value;
  save();
};
document.getElementById("productionCode").onblur=e=>{
  const code=normalizedProductionCode(e.target.value);
  if(code&&!/^(MP|MF)-\d{2,}$/.test(code)){
    alert("Production Code must use MP-01 for Pipe or MF-01 for Fitting.");
    e.target.focus();
    return;
  }
  const duplicate=state.recipes.some((r,i)=>i!==state.activeRecipe&&code&&normalizedProductionCode(r.productionCode)===code);
  if(duplicate){alert("This production code is already used. Please enter a unique code.");e.target.focus();return}
  state.recipes[state.activeRecipe].productionCode=code;
  e.target.value=code;
  save();renderRecipes();renderDashboard();
};

document.getElementById("recipeName").oninput=e=>{
  state.recipes[state.activeRecipe].name=e.target.value;
  save();
};
document.getElementById("recipeCategory").oninput=e=>{
  state.recipes[state.activeRecipe].category=e.target.value;
  save();
};
document.getElementById("recipeColor").oninput=e=>{
  state.recipes[state.activeRecipe].color=e.target.value;
  save();
};
document.getElementById("pvcBase").oninput=e=>{
  state.recipes[state.activeRecipe].pvcBase=+e.target.value||0;
  save();
  renderRecipes();
};
document.getElementById("addIngredient").onclick=()=>{
  state.recipes[state.activeRecipe].ingredients.push({material:"",grade:"",kg:0});
  save();
  renderRecipes();
};
document.getElementById("newRecipe").onclick=createNewRecipe;
document.getElementById("duplicateRecipe").onclick=duplicateActiveRecipe;
document.getElementById("deleteRecipe").onclick=deleteActiveRecipe;
document.getElementById("exportPdfBtn").onclick=openExportDialog;
document.getElementById("closeExportDialog").onclick=closeExportDialog;
document.getElementById("cancelExportDialog").onclick=closeExportDialog;
document.getElementById("generatePdfBtn").onclick=exportSelectedRecipesPdf;
document.getElementById("exportSelectAll").onchange=e=>{
  document.querySelectorAll('.export-recipe-check').forEach(box=>box.checked=e.target.checked);
  updateExportSelection();
};
document.getElementById("exportOverlay").onclick=e=>{
  if(e.target.id==="exportOverlay")closeExportDialog();
};
document.addEventListener("keydown",e=>{
  if(e.key==="Escape"&&document.getElementById("exportOverlay").classList.contains("open"))closeExportDialog();
});
document.getElementById("recipeSearch").oninput=renderRecipes;
document.getElementById("recipeCategoryFilter").onchange=renderRecipes;
document.getElementById("recipeSelector").onchange=e=>{
  if(e.target.value==="") return;
  state.activeRecipe=Number(e.target.value);
  save();
  renderRecipes();
};
document.getElementById("importExcelBtn").onclick=()=>{
  document.getElementById("importOverlay").classList.add("open");
};

document.querySelectorAll("[data-import-type]").forEach(button=>{
  button.onclick=()=>{
    pendingImportType=button.dataset.importType;
    document.getElementById("importOverlay").classList.remove("open");
    const input=document.getElementById("excelFileInput");
    input.value="";
    input.click();
  };
});

document.getElementById("cancelImportChoice").onclick=()=>{
  pendingImportType="";
  document.getElementById("importOverlay").classList.remove("open");
};

document.getElementById("importOverlay").onclick=e=>{
  if(e.target.id==="importOverlay"){
    pendingImportType="";
    e.currentTarget.classList.remove("open");
  }
};

document.getElementById("excelFileInput").onchange=async e=>{
  const file=e.target.files[0];
  if(!file || !pendingImportType){
    e.target.value="";
    return;
  }
  const selectedImportType=pendingImportType;
  try{
    await handleExcelImport(file,selectedImportType);
  }catch(error){
    alert("Import failed: "+error.message);
  }finally{
    pendingImportType="";
    e.target.value="";
  }
};
document.getElementById("downloadTemplateBtn").onclick=downloadExcelTemplate;
document.getElementById("exportBackupBtn").onclick=exportCompleteBackup;
document.getElementById("importBackupBtn").onclick=()=>document.getElementById("backupFileInput").click();
document.getElementById("backupFileInput").onchange=async e=>{
  const file=e.target.files[0];if(!file)return;
  if(!confirm("Import this complete backup? It will replace the current Materials & Recipes data after saving.")){e.target.value="";return}
  try{await importCompleteBackup(file)}catch(error){alert("Backup import failed: "+error.message)}finally{e.target.value=""}
};
document.getElementById("newMaterial").onclick=()=>{state.rawMaterials.push({id:makeMaterialId(),material:"",grade:"",country:"",company:"",stockKg:0});save();renderRawMaterials()};
document.getElementById("syncMaterials").onclick=syncRawMaterialsFromRecipes;
document.getElementById("materialSearch").oninput=renderRawMaterials;
document.getElementById("materialSortBy").onchange=renderRawMaterials;
renderDashboard();renderRecipes();renderRawMaterials();isDirty=false;updateSaveStatus();
loadCloudState().catch(error=>{console.error(error);setCloudStatus("Load Error","error");toast("Load failed: "+error.message)});
</script>
</body>
</html>
