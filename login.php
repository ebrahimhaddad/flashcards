<?php
include 'config.php'; // provides $pdo, JWT_SECRET, jwt_encode()

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['username'] ?? '';
    $passcode = $_POST['password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM `users` WHERE `email` = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    $passwordValid = false;

    if ($user) {
        $storedHash = $user['passcode'];

        if (str_starts_with($storedHash, '$2y$') || str_starts_with($storedHash, '$2b$')) {
            // Already migrated to bcrypt
            $passwordValid = password_verify($passcode, $storedHash);
        } else {
            // Legacy sha256 hash — verify the old way, then upgrade silently
            $legacyHash = hash('sha256', $passcode);
            $passwordValid = hash_equals($storedHash, $legacyHash);

            if ($passwordValid) {
                $newHash = password_hash($passcode, PASSWORD_DEFAULT);
                $upgradeStmt = $pdo->prepare("UPDATE `users` SET `passcode` = ? WHERE `email` = ?");
                $upgradeStmt->execute([$newHash, $email]);
            }
        }
    }

    // Preserve the status gate from the old system — status must be > 0 to log in
    if ($passwordValid && $user['status'] > 0) {
        $payload = [
            'sub'      => $user['email'],   // users table has no id column; email is the PK
            'editor'   => $user['email'],
            'realname' => $user['realname'],
            'status'   => $user['status'],
            'iat'      => time(),
            'exp'      => time() + 3600, // 1 hour
        ];

        $jwt = jwt_encode($payload);

        setcookie('auth_token', $jwt, [
            'expires'  => time() + 3600,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Strict',
        ]);

        // Preserve login audit trail from the old system
        $logStmt = $pdo->prepare("INSERT INTO `loginlogs` (`email`) VALUES (?)");
        $logStmt->execute([$user['email']]);

        header('Location: /flash2025/flash/cards.php');
        // header('Location: https://abeling.ir/flashcards/cards.php');
        exit;
    } else {
        $error = 'نام کاربری یا رمز عبور نادرست است';
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <title>ورود ویراستاران</title>
    <link href="/bootstrap/bootstrap.min.css" rel="stylesheet">
</head>

<body dir="rtl">
    <div class="container" style="margin:2rem">
        <div class="row">
            <div class="col-lg-3"></div>
            <div class="col-lg-6">
                <h2>ورود ویراستاران</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <label>نام کاربری</label>
                        <input type="text" class="form-control" name="username" required>
                    </div>
                    <div class="mb-3">
                        <label>رمز عبور</label>
                        <input type="password" class="form-control" name="password" required>
                    </div>
                    <button type="submit" class="btn btn-success">ورود</button>
                </form>
                <p><a href="cards.php">ادامه بدون ورود</a></p>
            </div>
            <div class="col-lg-3"></div>
        </div>
    </div>
</body>

</html>