<?php
// Simple email uniqueness check
header('Content-Type: application/json');

$servername = 'localhost';
$username   = 'root';
$password   = '';
$dbname     = 'college_admission';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

if (!isset($_GET['email']) || trim($_GET['email']) === '') {
    echo json_encode(['success' => false, 'message' => 'Email is required']);
    $conn->close();
    exit;
}

$email = $conn->real_escape_string(trim($_GET['email']));

$sql    = "SELECT id FROM students WHERE email = '$email'";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    echo json_encode(['success' => true, 'exists' => true, 'message' => 'Email already registered']);
} else {
    echo json_encode(['success' => true, 'exists' => false]);
}

$conn->close();
