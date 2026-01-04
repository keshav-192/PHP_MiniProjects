<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if stakeholder data exists
    if (!isset($_SESSION['stakeholder_id'])) {
        redirect('index.php', 'Please fill out personal details first.');
    }
    
    // Validate and sanitize input (normalize register no to uppercase so case is ignored)
    $register_no = strtoupper(trim($_POST['register_no']));
    $programme_type = $_POST['programme_type'];
    $programme = $_POST['programme'];
    $branch = $_POST['branch'];
    $regulation = $_POST['regulation'];
    $stakeholder_id = $_SESSION['stakeholder_id'];
    
    // Check if register number already exists
    $check_stmt = $conn->prepare("SELECT id FROM students WHERE register_no = ?");
    $check_stmt->bind_param("s", $register_no);
    $check_stmt->execute();
    $result = $check_stmt->get_result();
    
    if ($result->num_rows > 0) {
        redirect('student_details.php', 'This register number already exists. Please use a different one.');
    }
    $check_stmt->close();
    
    // Insert student data into database
    $stmt = $conn->prepare("INSERT INTO students (register_no, branch, programme_type, programme, regulation, stakeholder_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $register_no, $branch, $programme_type, $programme, $regulation, $stakeholder_id);
    
    if ($stmt->execute()) {
        $_SESSION['student_id'] = $stmt->insert_id;
        $_SESSION['student_data'] = [
            'register_no' => $register_no,
            'branch' => $branch,
            'programme_type' => $programme_type,
            'programme' => $programme,
            'regulation' => $regulation
        ];
        
        redirect('feedback_form.php', 'Student details saved successfully!');
    } else {
        redirect('student_details.php', 'Error saving student data: ' . $stmt->error);
    }
    
    $stmt->close();
} else {
    redirect('student_details.php', 'Invalid request method.');
}

$conn->close();
?>