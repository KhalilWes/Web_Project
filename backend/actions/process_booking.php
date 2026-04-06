<?php
declare(strict_types=1);

require_once __DIR__ . '/../includes/db.php';

function respondWithJson(int $statusCode, array $payload): void
{
    http_response_code($statusCode);
    header('Content-Type: application/json; charset=UTF-8');
    echo json_encode($payload, JSON_UNESCAPED_SLASHES);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respondWithJson(405, [
        'success' => false,
        'message' => 'Method not allowed. Use POST.',
    ]);
}

$nameRaw = $_POST['name'] ?? '';
$emailRaw = $_POST['email'] ?? '';
$voyageIdRaw = $_POST['voyage_id'] ?? '';
$travelersRaw = $_POST['travelers'] ?? '';

$name = trim((string) filter_var($nameRaw, FILTER_SANITIZE_FULL_SPECIAL_CHARS));
$email = filter_var($emailRaw, FILTER_VALIDATE_EMAIL);
$voyageId = filter_var($voyageIdRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
$travelers = filter_var($travelersRaw, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

if ($name === '' || !$email || !$voyageId || !$travelers) {
    respondWithJson(422, [
        'success' => false,
        'message' => 'Invalid booking data. Check name, email, voyage, and travelers.',
    ]);
}

try {
    $checkVoyage = $pdo->prepare('SELECT id FROM voyages WHERE id = :id LIMIT 1');
    $checkVoyage->execute([':id' => $voyageId]);

    if (!$checkVoyage->fetch()) {
        respondWithJson(404, [
            'success' => false,
            'message' => 'Selected voyage was not found.',
        ]);
    }

    $insert = $pdo->prepare(
        'INSERT INTO reservations (name, email, voyage_id, travelers, status) VALUES (:name, :email, :voyage_id, :travelers, :status)'
    );

    $insert->execute([
        ':name' => $name,
        ':email' => $email,
        ':voyage_id' => $voyageId,
        ':travelers' => $travelers,
        ':status' => 'pending',
    ]);

    respondWithJson(201, [
        'success' => true,
        'message' => 'Booking created successfully.',
        'reservation_id' => (int) $pdo->lastInsertId(),
    ]);
} catch (PDOException $exception) {
    error_log('Process booking failed: ' . $exception->getMessage());
    respondWithJson(500, [
        'success' => false,
        'message' => 'Unable to process booking right now.',
    ]);
}
