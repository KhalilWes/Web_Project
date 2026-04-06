<?php
declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/db.php';
require_once __DIR__ . '/../backend/includes/auth.php';

requireAdminAuth('login.php');

function e(string $value): string
{
	return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
}

$errorMessage = '';
$reservations = [];
$voyages = [];
$csrfToken = getCsrfToken();
$flashSuccess = $_SESSION['admin_flash_success'] ?? '';
$flashError = $_SESSION['admin_flash_error'] ?? '';
unset($_SESSION['admin_flash_success'], $_SESSION['admin_flash_error']);

try {
	$sql = '
		SELECT
			r.id,
			r.name AS guest_name,
			v.destination,
			v.departure_date,
			r.status
		FROM reservations r
		INNER JOIN voyages v ON v.id = r.voyage_id
		ORDER BY r.id DESC
	';

	$statement = $pdo->prepare($sql);
	$statement->execute();
	$reservations = $statement->fetchAll();
} catch (PDOException $exception) {
	error_log('Dashboard query failed: ' . $exception->getMessage());
	$errorMessage = 'Unable to load reservations right now.';
}

try {
	$voyagesStatement = $pdo->prepare(
		'SELECT id, destination, departure_date FROM voyages ORDER BY id DESC'
	);
	$voyagesStatement->execute();
	$voyages = $voyagesStatement->fetchAll();
} catch (PDOException $exception) {
	error_log('Voyages query failed: ' . $exception->getMessage());
	if ($errorMessage === '') {
		$errorMessage = 'Unable to load voyages right now.';
	}
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin Dashboard</title>
</head>
<body>
	<main>
		<h1>Reservations</h1>

		<?php if ($flashSuccess !== ''): ?>
			<p><?= e((string) $flashSuccess) ?></p>
		<?php endif; ?>

		<?php if ($flashError !== ''): ?>
			<p><?= e((string) $flashError) ?></p>
		<?php endif; ?>

		<?php if ($errorMessage !== ''): ?>
			<p><?= e($errorMessage) ?></p>
		<?php endif; ?>

		<?php if (empty($reservations)): ?>
			<p>No reservations found.</p>
		<?php else: ?>
			<table>
				<thead>
					<tr>
						<th>Guest Name</th>
						<th>Destination</th>
						<th>Date</th>
						<th>Status</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($reservations as $reservation): ?>
						<tr>
							<td><?= e((string) $reservation['guest_name']) ?></td>
							<td><?= e((string) $reservation['destination']) ?></td>
							<td><?= e((string) $reservation['departure_date']) ?></td>
							<td><?= e((string) $reservation['status']) ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<h2>Manage Voyages</h2>
		<?php if (empty($voyages)): ?>
			<p>No voyages found.</p>
		<?php else: ?>
			<table>
				<thead>
					<tr>
						<th>ID</th>
						<th>Destination</th>
						<th>Departure Date</th>
						<th>Update</th>
						<th>Delete</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($voyages as $voyage): ?>
						<tr>
							<td><?= (int) $voyage['id'] ?></td>
							<td><?= e((string) $voyage['destination']) ?></td>
							<td><?= e((string) $voyage['departure_date']) ?></td>
							<td>
								<form action="../backend/actions/update_voyage.php" method="post">
									<input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
									<input type="hidden" name="voyage_id" value="<?= (int) $voyage['id'] ?>">
									<input type="text" name="destination" value="<?= e((string) $voyage['destination']) ?>" required>
									<input type="date" name="departure_date" value="<?= e((string) $voyage['departure_date']) ?>" required>
									<button type="submit">Save</button>
								</form>
							</td>
							<td>
								<form action="../backend/actions/delete_voyage.php" method="post" onsubmit="return confirm('Delete this voyage?');">
									<input type="hidden" name="csrf_token" value="<?= e($csrfToken) ?>">
									<input type="hidden" name="voyage_id" value="<?= (int) $voyage['id'] ?>">
									<input type="hidden" name="confirm_delete" value="yes">
									<button type="submit">Delete</button>
								</form>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		<?php endif; ?>

		<p><a href="logout.php">Log out</a></p>
	</main>
</body>
</html>
