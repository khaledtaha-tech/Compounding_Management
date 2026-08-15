<?php
declare(strict_types=1);
require __DIR__ . '/bootstrap.php';
if (current_user()) { header('Location: index.php'); exit; }
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $email = strtolower(trim((string) ($_POST['email'] ?? '')));
        $stmt = db()->prepare('SELECT id, email, password_hash FROM users WHERE email = ?');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user || !password_verify((string) ($_POST['password'] ?? ''), $user['password_hash'])) {
            throw new RuntimeException('Incorrect email or password.');
        }
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['email'] = $user['email'];
        header('Location: index.php');
        exit;
    } catch (Throwable $e) { $error = $e->getMessage(); }
}
?>
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Sign In</title><link rel="stylesheet" href="site.css"></head>
<body class="auth-page"><main class="auth-box"><div class="brand-mark">CM</div><p class="eyebrow">COMPOUNDING SECTION</p><h1>Management System</h1><p class="muted">Production, recipes and raw materials in one place.</p>
<?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
<form method="post" class="auth-form"><label>Email<input type="email" name="email" autocomplete="email" required></label><label>Password<input type="password" name="password" autocomplete="current-password" required></label><button type="submit">Sign In</button></form></main></body></html>

