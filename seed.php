<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$pdo = db();

$driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
if ($driver === 'sqlite') {
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        email VARCHAR(190) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS equipment (
        id VARCHAR(80) PRIMARY KEY,
        user_id INTEGER NOT NULL,
        type VARCHAR(20) NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE (user_id, type, name)
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS production_records (
        id VARCHAR(80) PRIMARY KEY,
        user_id INTEGER NOT NULL,
        type VARCHAR(20) NOT NULL,
        production_date DATE NOT NULL,
        shift VARCHAR(20) NOT NULL,
        equipment_id VARCHAR(80) NULL,
        equipment_name VARCHAR(100) NOT NULL,
        mix_code VARCHAR(80) NULL,
        recipe_code VARCHAR(80) NULL,
        mix_name VARCHAR(190) NULL,
        pellet_application VARCHAR(190) NULL,
        color VARCHAR(100) NOT NULL,
        batch_count DECIMAL(12,3) NULL,
        batch_weight_kg DECIMAL(14,4) NULL,
        quantity_kg DECIMAL(14,2) NOT NULL,
        created_at DATETIME NOT NULL,
        updated_at DATETIME NOT NULL
    )");
    $pdo->exec("CREATE TABLE IF NOT EXISTS material_states (
        user_id INTEGER PRIMARY KEY,
        state_json TEXT NOT NULL,
        updated_at DATETIME NOT NULL
    )");
}

$email = 'admin@demo.com';
$stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
$stmt->execute([$email]);
$userId = $stmt->fetchColumn();

if (!$userId) {
    $stmt = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
    $stmt->execute([$email, password_hash('12345678', PASSWORD_DEFAULT)]);
    $userId = $pdo->lastInsertId();
}
$userId = (int) $userId;

$equipments = [
    ['id' => 'mixer-1', 'type' => 'Mixer', 'name' => 'High Speed Mixer A1'],
    ['id' => 'mixer-2', 'type' => 'Mixer', 'name' => 'Cooling Mixer B2'],
    ['id' => 'pelletizer-1', 'type' => 'Pelletizer', 'name' => 'Twin Screw Extruder P1'],
    ['id' => 'pelletizer-2', 'type' => 'Pelletizer', 'name' => 'Pelletizing Line P2'],
];

foreach ($equipments as $eq) {
    if ($driver === 'sqlite') {
        $stmt = $pdo->prepare('INSERT INTO equipment (id, user_id, type, name) VALUES (?,?,?,?) ON CONFLICT(id) DO UPDATE SET name=excluded.name');
    } else {
        $stmt = $pdo->prepare('INSERT INTO equipment (id, user_id, type, name) VALUES (?,?,?,?) ON DUPLICATE KEY UPDATE name=VALUES(name)');
    }
    $stmt->execute([$eq['id'], $userId, $eq['type'], $eq['name']]);
}

$mixes = [
    ['code' => 'MP-101', 'recipe' => 'KH-01', 'name' => 'PVC Rigid Pipe Dark Grey', 'color' => '#475569'],
    ['code' => 'MP-102', 'recipe' => 'KH-02', 'name' => 'PVC Soft Gasket White', 'color' => '#0284c7'],
    ['code' => 'MP-103', 'recipe' => 'KH-03', 'name' => 'PVC Fittings Black', 'color' => '#1e293b'],
    ['code' => 'MP-104', 'recipe' => 'KH-04', 'name' => 'PVC Medical Hose Clear', 'color' => '#10b981'],
];

$applications = [
    'Conduit Pipe Extrusion',
    'Pressure Pipe Fitting',
    'Electrical Cable Insulation',
    'Flexible Hose Compound'
];

$shifts = ['Morning', 'Night'];
$today = new DateTime('2026-08-18');

for ($i = 6; $i >= 0; $i--) {
    $dateObj = (clone $today)->modify("-$i days");
    $dateStr = $dateObj->format('Y-m-d');
    
    foreach ($shifts as $shift) {
        $mix = $mixes[array_rand($mixes)];
        $eqMixer = $equipments[rand(0, 1)];
        $batches = rand(8, 25);
        $weight = rand(50, 100);
        $qty = $batches * $weight;
        $recId = 'rec-mix-' . $dateStr . '-' . strtolower($shift) . '-' . rand(100, 999);
        $now = $dateStr . ' ' . sprintf('%02d:%02d:00', rand(8, 20), rand(0, 59));

        if ($driver === 'sqlite') {
            $stmt = $pdo->prepare('INSERT INTO production_records (id,user_id,type,production_date,shift,equipment_id,equipment_name,mix_code,recipe_code,mix_name,pellet_application,color,batch_count,batch_weight_kg,quantity_kg,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON CONFLICT(id) DO UPDATE SET quantity_kg=excluded.quantity_kg');
        } else {
            $stmt = $pdo->prepare('INSERT INTO production_records (id,user_id,type,production_date,shift,equipment_id,equipment_name,mix_code,recipe_code,mix_name,pellet_application,color,batch_count,batch_weight_kg,quantity_kg,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE quantity_kg=VALUES(quantity_kg)');
        }
        $stmt->execute([$recId, $userId, 'Mixer', $dateStr, $shift, $eqMixer['id'], $eqMixer['name'], $mix['code'], $mix['recipe'], $mix['name'], null, $mix['color'], $batches, $weight, $qty, $now, $now]);

        $app = $applications[array_rand($applications)];
        $eqPellet = $equipments[rand(2, 3)];
        $qtyPellet = rand(500, 2500);
        $recIdP = 'rec-pel-' . $dateStr . '-' . strtolower($shift) . '-' . rand(100, 999);

        if ($driver === 'sqlite') {
            $stmt = $pdo->prepare('INSERT INTO production_records (id,user_id,type,production_date,shift,equipment_id,equipment_name,mix_code,recipe_code,mix_name,pellet_application,color,batch_count,batch_weight_kg,quantity_kg,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON CONFLICT(id) DO UPDATE SET quantity_kg=excluded.quantity_kg');
        } else {
            $stmt = $pdo->prepare('INSERT INTO production_records (id,user_id,type,production_date,shift,equipment_id,equipment_name,mix_code,recipe_code,mix_name,pellet_application,color,batch_count,batch_weight_kg,quantity_kg,created_at,updated_at) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?) ON DUPLICATE KEY UPDATE quantity_kg=VALUES(quantity_kg)');
        }
        $stmt->execute([$recIdP, $userId, 'Pelletizer', $dateStr, $shift, $eqPellet['id'], $eqPellet['name'], null, null, null, $app, $mix['color'], null, null, $qtyPellet, $now, $now]);
    }
}

echo "Successfully seeded sample production data for the last 7 days up to today (2026-08-18) for user admin@demo.com!\n";
