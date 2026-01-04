<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Normalize register number to uppercase so that case is not significant
    $register_no = isset($_POST['register_no']) ? strtoupper(trim($_POST['register_no'])) : '';

    if ($register_no === '') {
        echo json_encode(['success' => false, 'exists' => false, 'message' => 'No register number provided']);
        exit();
    }

    $stmt = $conn->prepare('SELECT id FROM students WHERE register_no = ?');
    $stmt->bind_param('s', $register_no);
    $stmt->execute();
    $stmt->store_result();

    $exists = $stmt->num_rows > 0;
    $stmt->close();

    echo json_encode(['success' => true, 'exists' => $exists]);
    exit();
}

echo json_encode(['success' => false, 'exists' => false, 'message' => 'Invalid request']);
exit();
