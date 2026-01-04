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

// Get counseling ID from query parameter
if (!isset($_GET['counseling_id']) || empty($_GET['counseling_id'])) {
    echo json_encode(["success" => false, "message" => "Counseling ID is required"]);
    exit;
}

$counseling_id = $conn->real_escape_string(trim($_GET['counseling_id']));

// Search for student
$sql = "SELECT * FROM students WHERE counseling_id = '$counseling_id'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $student = $result->fetch_assoc();
    echo json_encode(["success" => true, "student" => $student]);
} else {
    echo json_encode(["success" => false, "message" => "No student found with that Counseling ID"]);
}

$conn->close();
?>