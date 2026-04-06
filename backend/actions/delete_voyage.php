<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';

requireAdminAuth('../../admin/login.php');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../../admin/dashboard.php');
    exit;
}

$csrfToken = $_POST['csrf_token'] ?? '';
$voyageId = filter_input(INPUT_POST, 'voyage_id', FILTER_VALIDATE_INT);
$confirmDelete = $_POST['confirm_delete'] ?? '';

if (!validateCsrfToken((string) $csrfToken)) {
    $_SESSION['admin_flash_error'] = 'Invalid request token.';
    header('Location: ../../admin/dashboard.php');
    exit;
}

if (!$voyageId || $confirmDelete !== 'yes') {
    $_SESSION['admin_flash_error'] = 'Delete request was not confirmed.';
    header('Location: ../../admin/dashboard.php');
    exit;
}

try {
    $statement = $pdo->prepare('DELETE FROM voyages WHERE id = :id');
    $statement->execute([':id' => $voyageId]);

    if ($statement->rowCount() > 0) {
        $_SESSION['admin_flash_success'] = 'Voyage deleted successfully.';
    } else {
        $_SESSION['admin_flash_error'] = 'Voyage not found.';
    }
} catch (PDOException $exception) {
    error_log('Delete voyage failed: ' . $exception->getMessage());
    $_SESSION['admin_flash_error'] = 'Unable to delete voyage right now.';
}

header('Location: ../../admin/dashboard.php');
exit;
