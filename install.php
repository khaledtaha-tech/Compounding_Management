<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';

$message = '';
$error = '';
try {
    $pdo = db();
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(190) NOT NULL UNIQUE,
        password_hash VARCHAR(255) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS equipment (
        id VARCHAR(80) PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        type ENUM('Mixer','Pelletizer') NOT NULL,
        name VARCHAR(100) NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uq_equipment (user_id, type, name),
        CONSTRAINT fk_equipment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $pdo->exec("CREATE TABLE IF NOT EXISTS production_records (
        id VARCHAR(80) PRIMARY KEY,
        user_id INT UNSIGNED NOT NULL,
        type ENUM('Mixer','Pelletizer') NOT NULL,
        production_date DATE NOT NULL,
        shift ENUM('Morning','Night') NOT NULL,
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
        updated_at DATETIME NOT NULL,
        INDEX idx_production_user_date (user_id, production_date),
        CONSTRAINT fk_production_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
    $recipeCodeColumn = $pdo->query("SHOW COLUMNS FROM production_records LIKE 'recipe_code'")->fetch();
    if (!$recipeCodeColumn) {
        $pdo->exec("ALTER TABLE production_records ADD COLUMN recipe_code VARCHAR(80) NULL AFTER mix_code");
    }
    $batchCountColumn = $pdo->query("SHOW COLUMNS FROM production_records LIKE 'batch_count'")->fetch();
    if (!$batchCountColumn) {
        $pdo->exec("ALTER TABLE production_records ADD COLUMN batch_count DECIMAL(12,3) NULL AFTER color");
    }
    $batchWeightColumn = $pdo->query("SHOW COLUMNS FROM production_records LIKE 'batch_weight_kg'")->fetch();
    if (!$batchWeightColumn) {
        $pdo->exec("ALTER TABLE production_records ADD COLUMN batch_weight_kg DECIMAL(14,4) NULL AFTER batch_count");
    }
    $pdo->exec("CREATE TABLE IF NOT EXISTS material_states (
        user_id INT UNSIGNED PRIMARY KEY,
        state_json LONGTEXT NOT NULL,
        updated_at DATETIME NOT NULL,
        CONSTRAINT fk_material_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $count = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $count === 0) {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $password = (string) ($_POST['password'] ?? '');
        $confirm = (string) ($_POST['confirm'] ?? '');
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) throw new RuntimeException('Enter a valid email address.');
        if (strlen($password) < 8) throw new RuntimeException('Password must contain at least 8 characters.');
        if ($password !== $confirm) throw new RuntimeException('Passwords do not match.');
        $stmt = $pdo->prepare('INSERT INTO users (email, password_hash) VALUES (?, ?)');
        $stmt->execute([$email, password_hash($password, PASSWORD_DEFAULT)]);
        $message = 'Installation completed. You can sign in now.';
        $count = 1;
    } elseif ($count > 0) {
        $message = 'The application is already installed.';
    }
} catch (Throwable $e) {
    $error = $e->getMessage();
    $count = 0;
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Install</title><link rel="stylesheet" href="site.css"></head>
<body class="auth-page"><main class="auth-box"><div class="brand-mark">CM</div><h1>Compounding Management</h1><p class="muted">One-time installation</p>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<?php if ($message): ?><div class="alert success"><?= htmlspecialchars($message) ?></div><a class="primary-link" href="login.php">Open Login</a><?php endif; ?>
<?php if (!$message && !$error && $count === 0): ?><form method="post" class="auth-form"><label>Email<input type="email" name="email" required></label><label>Password<input type="password" name="password" minlength="8" required></label><label>Confirm Password<input type="password" name="confirm" minlength="8" required></label><button type="submit">Install & Create Account</button></form><?php endif; ?>
</main></body></html>
