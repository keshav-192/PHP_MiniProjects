<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $firstname = trim($_POST['firstname']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $gender = $_POST['gender'];
    $stakeholder_type = $_POST['stakeholder_type'];
    $academic_year = $_POST['academic_year'];
    
    // Check if email already exists
    $check_stmt = $conn->prepare("SELECT id FROM stakeholders WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        $check_stmt->close();
        redirect('index.php', 'email already registered');
    }
    $check_stmt->close();

    // Insert stakeholder data into database
    $stmt = $conn->prepare("INSERT INTO stakeholders (firstname, phone, email, gender, stakeholder_type, academic_year) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $firstname, $phone, $email, $gender, $stakeholder_type, $academic_year);
    
    if ($stmt->execute()) {
        $_SESSION['stakeholder_id'] = $stmt->insert_id;
        $_SESSION['stakeholder_data'] = [
            'firstname' => $firstname,
            'phone' => $phone,
            'email' => $email,
            'gender' => $gender,
            'stakeholder_type' => $stakeholder_type,
            'academic_year' => $academic_year
        ];
        
        // Redirect based on stakeholder type
        if ($stakeholder_type === 'Student') {
            redirect('student_details.php', 'Personal details saved successfully!');
        } else {
            // For Faculty and Alumni, go directly to their specific feedback form
            redirect('general_feedback.php', 'Personal details saved successfully!');
        }
    } else {
        redirect('index.php', 'Error saving data: ' . $stmt->error);
    }
    
    $stmt->close();
} else {
    redirect('index.php', 'Invalid request method.');
}

$conn->close();
?>