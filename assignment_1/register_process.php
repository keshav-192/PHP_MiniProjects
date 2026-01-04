<?php
declare(strict_types=1);
session_start();
require __DIR__ . '/config.php';

function respond(string $msg, string $to = 'register.php'): void {
  $_SESSION['flash'] = $msg;
  header("Location: {$to}");
  exit;
}

$name = trim($_POST['name'] ?? '');
$username = trim($_POST['username'] ?? '');
$email = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$confirm = $_POST['confirm'] ?? '';

if ($name === '' || $username === '' || $email === '' || $password === '' || $confirm === '') {
  respond('All fields are required.');
}

// Name: only letters and spaces
if (!preg_match("/^[A-Za-z][A-Za-z\s]{2,99}$/", $name)) {
  respond('Invalid name. Use letters and spaces only.');
}
if (!preg_match('/^[A-Za-z0-9_]{3,32}$/', $username)) {
  respond('Invalid username.');
}
// Email: basic format + domain from popular provider or institutional domain
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
  respond('Invalid email address.') ;
}

$domain = strtolower(substr(strrchr($email, '@') ?: '', 1));
$allowedProviders = [
  'gmail.com',
  'yahoo.com',
  'outlook.com',
  'hotmail.com',
  'live.com',
  'icloud.com',
  'proton.me',
];
$isInstitutional = (bool)preg_match('/\.(ac|edu)(\.[a-z.]+)?$/i', $domain);

if (!in_array($domain, $allowedProviders, true) && !$isInstitutional) {
  respond('Use a valid provider or institutional email domain.');
}
if ($password !== $confirm) {
  respond('Passwords do not match.');
}
// Strong password only: upper, lower, digit, symbol, length >= 8
$complex = preg_match('/[a-z]/',$password)
  && preg_match('/[A-Z]/',$password)
  && preg_match('/\d/',$password)
  && preg_match('/[^A-Za-z0-9]/',$password)
  && strlen($password) >= 8;
if (!$complex) {
  respond('Password must use upper, lower, number, symbol and be at least 8 characters.');
}

// Check email uniqueness
$stmt = $mysqli->prepare("SELECT 1 FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  $stmt->close();
  respond('Email already registered.');
}
$stmt->close();

// Check username uniqueness
$stmt = $mysqli->prepare("SELECT 1 FROM users WHERE username = ? LIMIT 1");
$stmt->bind_param('s', $username);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
  $stmt->close();
  respond('Username already exists, try another.');
}
$stmt->close();

// Hash password
$hash = password_hash($password, PASSWORD_DEFAULT);

// Insert user
$ins = $mysqli->prepare("INSERT INTO users (name, username, email, password_hash) VALUES (?,?,?,?)");
$ins->bind_param('ssss', $name, $username, $email, $hash);
if (!$ins->execute()) {
  $ins->close();
  respond('Failed to register. Please try again.');
}
$ins->close();

$_SESSION['flash_success'] = 'Account created successfully. Please log in.';
header('Location: login.php');
exit;
