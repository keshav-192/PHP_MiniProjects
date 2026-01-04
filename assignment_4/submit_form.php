<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "college_admission";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Set response header
header('Content-Type: application/json');

// Create uploads directory if it doesn't exist
if (!file_exists('uploads')) {
    mkdir('uploads', 0777, true);
}

// Validate required fields
$required_fields = [
    'counseling_id', 'first_name', 'last_name', 'email', 'phone', 'address'
];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        echo json_encode(["success" => false, "message" => "All fields are required"]);
        exit;
    }
}

// Sanitize input data
$counseling_id = $conn->real_escape_string(trim($_POST['counseling_id']));
$first_name = $conn->real_escape_string(trim($_POST['first_name']));
$last_name = $conn->real_escape_string(trim($_POST['last_name']));
$email = $conn->real_escape_string(trim($_POST['email']));
$phone = $conn->real_escape_string(trim($_POST['phone']));
$address = $conn->real_escape_string(trim($_POST['address']));

// Normalize first and last names: first letter uppercase, rest lowercase
$first_name = ucwords(strtolower($first_name));
$last_name = ucwords(strtolower($last_name));

// Validate counseling ID pattern: exactly 6 chars -> 'VU' followed by 4 digits
if (!preg_match('/^VU\d{4}$/', $counseling_id)) {
    echo json_encode(["success" => false, "message" => "Counseling ID must be VU followed by 4 digits (e.g., VU1234)"]);
    exit;
}

// Validate address word limit (max 50 words)
$address_words = preg_split('/\s+/', trim($_POST['address']));
$address_words = array_filter($address_words, function($w) { return strlen($w) > 0; });
if (count($address_words) > 50) {
    echo json_encode(["success" => false, "message" => "Address must not exceed 50 words"]);
    exit;
}

// Validate address alphabet characters limit (max 1000 letters)
$letters_only_address = preg_replace('/[^A-Za-z]/', '', $_POST['address']);
if (strlen($letters_only_address) > 1000) {
    echo json_encode(["success" => false, "message" => "Address must not exceed 1000 alphabet characters"]);
    exit;
}

// Validate first name (letters and spaces only)
if (!preg_match('/^[A-Za-z\s]+$/', $first_name)) {
    echo json_encode(["success" => false, "message" => "First Name must contain only letters and spaces"]);
    exit;
}

// Validate last name (letters and spaces only)
if (!preg_match('/^[A-Za-z\s]+$/', $last_name)) {
    echo json_encode(["success" => false, "message" => "Last Name must contain only letters and spaces"]);
    exit;
}

// Validate email format and enforce allowed domains (global providers or institutional domains)
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["success" => false, "message" => "Invalid email format"]);
    exit;
}

$email_parts = explode('@', $email);
if (count($email_parts) !== 2) {
    echo json_encode(["success" => false, "message" => "Invalid email format"]);
    exit;
}

$domain = strtolower($email_parts[1]);

// Disallow obviously fake example like xyz.com explicitly
if ($domain === 'xyz.com') {
    echo json_encode(["success" => false, "message" => "Use gmail/yahoo/outlook/hotmail/icloud/proton.me or *.ac.in/*.edu domains."]);
    exit;
}

// Allow only known global .com providers for .com addresses
$allowedGlobalComDomains = [
    'gmail.com',
    'yahoo.com',
    'outlook.com',
    'hotmail.com',
    'icloud.com',
    'proton.me',
    'ymail.com',
    'rediffmail.com'
];

if (substr($domain, -4) === '.com') {
    if (!in_array($domain, $allowedGlobalComDomains)) {
        echo json_encode(["success" => false, "message" => "Use gmail/yahoo/outlook/hotmail/icloud/proton.me or *.ac.in/*.edu domains."]);
        exit;
    }
} else {
    // For non-.com domains, allow only institutional-style domains: *.ac.in, *.edu, *.edu.in
    if (!preg_match('/(\.ac\.in|\.edu(\.in)?)$/i', $domain)) {
        echo json_encode(["success" => false, "message" => "Use gmail/yahoo/outlook/hotmail/icloud/proton.me or *.ac.in/*.edu domains."]);
        exit;
    }
}

// Validate phone: Indian-style 10-digit mobile (starts with 6-9)
if (!preg_match('/^[6-9]\d{9}$/', $phone)) {
    echo json_encode(["success" => false, "message" => "Phone number must be 10 digits and start with 6, 7, 8, or 9"]);
    exit;
}

// Check if counseling ID already exists
$check_sql = "SELECT id FROM students WHERE counseling_id = '$counseling_id'";
$result = $conn->query($check_sql);
if ($result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Application from this Counseling ID is already submitted"]);
    exit;
}

// Check if email already exists
$check_email_sql = "SELECT id FROM students WHERE email = '$email'";
$email_result = $conn->query($check_email_sql);
if ($email_result->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already registered"]);
    exit;
}

// Handle file uploads
$upload_errors = [];
$allowed_photo_types = ['image/jpeg', 'image/jpg'];
$allowed_certificate_types = ['application/pdf'];

// Process photo upload
if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
    $photo_type = $_FILES['photo']['type'];
    $photo_size = $_FILES['photo']['size'];
    if ($photo_size > 5 * 1024 * 1024) {
        $upload_errors[] = "Photo size must not exceed 5MB";
    } elseif (!in_array($photo_type, $allowed_photo_types)) {
        $upload_errors[] = "Photo must be a JPEG image";
    } else {
        $photo_extension = pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION);
        $photo_filename = "photo_" . $counseling_id . "." . $photo_extension;
        $photo_path = "uploads/" . $photo_filename;
        
        if (!move_uploaded_file($_FILES['photo']['tmp_name'], $photo_path)) {
            $upload_errors[] = "Failed to upload photo";
        }
    }
} else {
    $upload_errors[] = "Photo is required";
}

// Process intermediate certificate upload
if (isset($_FILES['intermediate_certificate']) && $_FILES['intermediate_certificate']['error'] === UPLOAD_ERR_OK) {
    $intermediate_type = $_FILES['intermediate_certificate']['type'];
    $intermediate_size = $_FILES['intermediate_certificate']['size'];
    if ($intermediate_size > 5 * 1024 * 1024) {
        $upload_errors[] = "Intermediate certificate size must not exceed 5MB";
    } elseif (!in_array($intermediate_type, $allowed_certificate_types)) {
        $upload_errors[] = "Intermediate certificate must be a PDF file";
    } else {
        $intermediate_extension = pathinfo($_FILES['intermediate_certificate']['name'], PATHINFO_EXTENSION);
        $intermediate_filename = "intermediate_" . $counseling_id . "." . $intermediate_extension;
        $intermediate_path = "uploads/" . $intermediate_filename;
        
        if (!move_uploaded_file($_FILES['intermediate_certificate']['tmp_name'], $intermediate_path)) {
            $upload_errors[] = "Failed to upload intermediate certificate";
        }
    }
} else {
    $upload_errors[] = "Intermediate certificate is required";
}

// Process tenth certificate upload
if (isset($_FILES['tenth_certificate']) && $_FILES['tenth_certificate']['error'] === UPLOAD_ERR_OK) {
    $tenth_type = $_FILES['tenth_certificate']['type'];
    $tenth_size = $_FILES['tenth_certificate']['size'];
    if ($tenth_size > 5 * 1024 * 1024) {
        $upload_errors[] = "Tenth certificate size must not exceed 5MB";
    } elseif (!in_array($tenth_type, $allowed_certificate_types)) {
        $upload_errors[] = "Tenth certificate must be a PDF file";
    } else {
        $tenth_extension = pathinfo($_FILES['tenth_certificate']['name'], PATHINFO_EXTENSION);
        $tenth_filename = "tenth_" . $counseling_id . "." . $tenth_extension;
        $tenth_path = "uploads/" . $tenth_filename;
        
        if (!move_uploaded_file($_FILES['tenth_certificate']['tmp_name'], $tenth_path)) {
            $upload_errors[] = "Failed to upload tenth certificate";
        }
    }
} else {
    $upload_errors[] = "Tenth certificate is required";
}

// If there are upload errors, return them
if (!empty($upload_errors)) {
    echo json_encode(["success" => false, "message" => implode(", ", $upload_errors)]);
    exit;
}

// Insert data into database
$sql = "INSERT INTO students (counseling_id, first_name, last_name, email, phone, address, photo, intermediate_certificate, tenth_certificate) 
        VALUES ('$counseling_id', '$first_name', '$last_name', '$email', '$phone', '$address', '$photo_filename', '$intermediate_filename', '$tenth_filename')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["success" => true, "message" => "Application submitted successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
}

$conn->close();
?>