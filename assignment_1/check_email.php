<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

header('Content-Type: application/json');

$email = trim($_GET['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
  echo json_encode(['ok' => false, 'available' => false]);
  exit;
}

$stmt = $mysqli->prepare('SELECT 1 FROM users WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

echo json_encode(['ok' => true, 'available' => !$exists]);
exit;
