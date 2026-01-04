<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';

function bounce(string $msg): void {
  $_SESSION['flash'] = $msg;
  header('Location: login.php');
  exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
  bounce('Username and password are required.');
}

if (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
  bounce('Invalid username format.');
}

$stmt = $mysqli->prepare("SELECT id, username, password_hash FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$res = $stmt->get_result();
$user = $res->fetch_assoc();
$stmt->close();

if (!$user) {
  bounce('Invalid credentials.');
}

if (!password_verify($password, $user['password_hash'])) {
  bounce('Invalid credentials.');
}

// Regenerate session ID at privilege change (OWASP)
session_regenerate_id(true);

$_SESSION['uid'] = (int)$user['id'];
$_SESSION['uname'] = $user['username'];

header('Location: home.php');
exit;
