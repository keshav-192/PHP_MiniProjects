<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if student data exists
    if (!isset($_SESSION['student_id'])) {
        redirect('index.php', 'Please complete previous steps first.');
    }
    
    // Validate and collect feedback data
    $student_id = $_SESSION['student_id'];
    $stakeholder_id = $_SESSION['stakeholder_id'];
    $stakeholder_type = 'Student';
    $q1 = $_POST['q1'];
    $q2 = $_POST['q2'];
    $q3 = $_POST['q3'];
    $q4 = $_POST['q4'];
    $q5 = $_POST['q5'];
    $q6 = $_POST['q6'];
    $q7 = $_POST['q7'];
    $q8 = $_POST['q8'];
    $q9 = $_POST['q9'];
    $suggestions = trim($_POST['suggestions']);
    
    // Insert feedback into database (with both student_id and stakeholder_id)
    $stmt = $conn->prepare("INSERT INTO feedback_responses (student_id, stakeholder_type, stakeholder_id, q1, q2, q3, q4, q5, q6, q7, q8, q9, suggestions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issiiiiiiiiis", $student_id, $stakeholder_type, $stakeholder_id, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $suggestions);
    
    if ($stmt->execute()) {
        // Clear session data
        unset($_SESSION['stakeholder_id']);
        unset($_SESSION['student_id']);
        unset($_SESSION['stakeholder_data']);
        unset($_SESSION['student_data']);
        
        redirect('thank_you.php', 'Thank you for submitting your feedback!');
    } else {
        redirect('feedback_form.php', 'Error saving feedback: ' . $stmt->error);
    }
    
    $stmt->close();
} else {
    redirect('feedback_form.php', 'Invalid request method.');
}

$conn->close();
?>