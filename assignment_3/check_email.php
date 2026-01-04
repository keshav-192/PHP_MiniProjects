<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = isset($_POST['email']) ? trim($_POST['email']) : '';

    if ($email === '') {
        echo json_encode(['success' => false, 'exists' => false, 'message' => 'No email provided']);
        exit();
    }

    $stmt = $conn->prepare('SELECT id FROM stakeholders WHERE email = ?');
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $stmt->store_result();

    $exists = $stmt->num_rows > 0;
    $stmt->close();

    echo json_encode(['success' => true, 'exists' => $exists]);
    exit();
}

echo json_encode(['success' => false, 'exists' => false, 'message' => 'Invalid request']);
exit();
