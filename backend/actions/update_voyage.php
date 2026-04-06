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
$destinationRaw = $_POST['destination'] ?? '';
$departureDateRaw = $_POST['departure_date'] ?? '';

$destination = trim((string) filter_var($destinationRaw, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$departureDate = trim((string) $departureDateRaw);

if (!validateCsrfToken((string) $csrfToken)) {
    $_SESSION['admin_flash_error'] = 'Invalid request token.';
    header('Location: ../../admin/dashboard.php');
    exit;
}

if (!$voyageId || $destination === '' || $departureDate === '') {
    $_SESSION['admin_flash_error'] = 'Please provide valid voyage data.';
    header('Location: ../../admin/dashboard.php');
    exit;
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $departureDate)) {
    $_SESSION['admin_flash_error'] = 'Departure date must be in YYYY-MM-DD format.';
    header('Location: ../../admin/dashboard.php');
    exit;
}

try {
    $statement = $pdo->prepare(
        'UPDATE voyages SET destination = :destination, departure_date = :departure_date WHERE id = :id'
    );

    $statement->execute([
        ':destination' => $destination,
        ':departure_date' => $departureDate,
        ':id' => $voyageId,
    ]);

    if ($statement->rowCount() > 0) {
        $_SESSION['admin_flash_success'] = 'Voyage updated successfully.';
    } else {
        $_SESSION['admin_flash_error'] = 'No changes saved or voyage not found.';
    }
} catch (PDOException $exception) {
    error_log('Update voyage failed: ' . $exception->getMessage());
    $_SESSION['admin_flash_error'] = 'Unable to update voyage right now.';
}

header('Location: ../../admin/dashboard.php');
exit;
