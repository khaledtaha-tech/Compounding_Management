<?php
require __DIR__ . '/bootstrap.php';
$user = require_auth();
$stmt = db()->prepare('SELECT COUNT(*) records_count, COALESCE(SUM(quantity_kg),0) total_kg FROM production_records WHERE user_id=?');
$stmt->execute([$user['id']]);
$production = $stmt->fetch();
$stmt = db()->prepare('SELECT COUNT(*) FROM equipment WHERE user_id=?');
$stmt->execute([$user['id']]);
$equipmentCount = (int) $stmt->fetchColumn();
$stmt = db()->prepare('SELECT state_json FROM material_states WHERE user_id=?');
$stmt->execute([$user['id']]);
$materialState = json_decode((string) ($stmt->fetchColumn() ?: '{}'), true) ?: [];
$recipeCount = count($materialState['recipes'] ?? []);
$materialCount = count($materialState['rawMaterials'] ?? []);
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Compounding Management</title><link rel="stylesheet" href="site.css"></head>
<body><header class="site-header"><div><p class="eyebrow">COMPOUNDING SECTION</p><h1>Compounding Management System</h1></div><div class="account"><span><?= htmlspecialchars($user['email']) ?></span><a href="logout.php">Sign Out</a></div></header>
<main class="dashboard"><div class="welcome"><h2>Everything in one place</h2><p>Production records, recipes, raw-material stock, coverage and backups.</p></div>
<section class="kpi-grid"><div><span>Production Records</span><strong><?= number_format((int)$production['records_count']) ?></strong></div><div><span>Total Production</span><strong><?= number_format((float)$production['total_kg'],2) ?> kg</strong></div><div><span>Recipes</span><strong><?= number_format($recipeCount) ?></strong></div><div><span>Raw Materials</span><strong><?= number_format($materialCount) ?></strong></div><div><span>Equipment</span><strong><?= number_format($equipmentCount) ?></strong></div></section>
<div class="module-grid">
<a class="module-card blue" href="production.php"><span class="module-icon">P</span><div><h2>Production Tracker</h2><p>Mixer and pelletizer records, daily and monthly summaries, equipment and PDF reports.</p></div><b>Open →</b></a>
<a class="module-card violet" href="materials.php"><span class="module-icon">M</span><div><h2>Materials & Recipes</h2><p>PVC recipes, raw materials, stock coverage, Excel import/export and recipe PDF.</p></div><b>Open →</b></a>
</div></main></body></html>
