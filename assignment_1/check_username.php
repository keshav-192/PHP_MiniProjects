<?php
declare(strict_types=1);
require __DIR__ . '/config.php';

header('Content-Type: application/json');

$username = trim($_GET['username'] ?? '');

if ($username === '' || !preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
  echo json_encode(['ok' => false, 'available' => false]);
  exit;
}

$stmt = $mysqli->prepare('SELECT 1 FROM users WHERE username = ? LIMIT 1');
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
$exists = $stmt->num_rows > 0;
$stmt->close();

echo json_encode(['ok' => true, 'available' => !$exists]);
exit;
