<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/db.php';
require_once __DIR__ . '/../backend/includes/auth.php';

if (adminIsAuthenticated()) {
    header('Location: dashboard.php');
    exit;
}

$loginError = '';
$csrfToken = getCsrfToken();
$emailValue = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $postedCsrfToken = (string) ($_POST['csrf_token'] ?? '');
    $emailValue = trim((string) ($_POST['email'] ?? ''));
    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'] ?? '';

    if (!validateCsrfToken($postedCsrfToken)) {
        $loginError = 'Invalid request token. Please refresh and try again.';
    } elseif (!$email || $password === '') {
        $loginError = 'Please enter a valid email and password.';
    } else {
        $query = $pdo->prepare('SELECT id, password_hash FROM admins WHERE email = :email LIMIT 1');
        $query->execute([':email' => $email]);
        $admin = $query->fetch();

        if ($admin && password_verify($password, (string) $admin['password_hash'])) {
            regenerateSessionAfterLogin();
            $_SESSION['admin_id'] = (int) $admin['id'];

            header('Location: dashboard.php');
            exit;
        }

        $loginError = 'Invalid credentials.';
    }
}

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
</head>
<body>
    <main>
        <h1>Admin Login</h1>

        <?php if ($loginError !== ''): ?>
            <p><?= e($loginError) ?></p>
        <?php endif; ?>

        <form action="login.php" method="post">
            <input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">

            <label for="email">Email</label>
            <input id="email" type="email" name="email" value="<?= e($emailValue) ?>" required>

            <label for="password">Password</label>
            <input id="password" type="password" name="password" required>

            <button type="submit">Sign In</button>
        </form>
    </main>
</body>
</html>
