<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if stakeholder data exists
    if (!isset($_SESSION['stakeholder_id'])) {
        redirect('index.php', 'Please complete previous steps first.');
    }
    
    // Validate and collect feedback data
    $stakeholder_id = $_SESSION['stakeholder_id'];
    $stakeholder_type = $_POST['stakeholder_type'];
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
    
    // Insert feedback into database (without student_id)
    $stmt = $conn->prepare("INSERT INTO feedback_responses (stakeholder_type, stakeholder_id, q1, q2, q3, q4, q5, q6, q7, q8, q9, suggestions) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("siiiiiiiiiis", $stakeholder_type, $stakeholder_id, $q1, $q2, $q3, $q4, $q5, $q6, $q7, $q8, $q9, $suggestions);
    
    if ($stmt->execute()) {
        // Clear session data
        unset($_SESSION['stakeholder_id']);
        unset($_SESSION['stakeholder_data']);
        
        redirect('thank_you.php', 'Thank you for submitting your feedback!');
    } else {
        redirect('general_feedback.php', 'Error saving feedback: ' . $stmt->error);
    }
    
    $stmt->close();
} else {
    redirect('general_feedback.php', 'Invalid request method.');
}

$conn->close();
?>