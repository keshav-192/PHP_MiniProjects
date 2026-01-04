<?php
// Run this file once to set up the database

$host = 'localhost';
$username = 'root';
$password = '';

try {
    // Create connection
    $conn = new mysqli($host, $username, $password);
    
    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    // Create database
    $sql = "CREATE DATABASE IF NOT EXISTS registration_db";
    if ($conn->query($sql) === TRUE) {
        echo "Database created successfully<br>";
    } else {
        echo "Error creating database: " . $conn->error . "<br>";
    }
    
    // Select database
    $conn->select_db("registration_db");
    
    // Create states table
    $sql = "CREATE TABLE IF NOT EXISTS states (
        state_id INT AUTO_INCREMENT PRIMARY KEY,
        state_name VARCHAR(100) NOT NULL UNIQUE
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "States table created successfully<br>";
    } else {
        echo "Error creating states table: " . $conn->error . "<br>";
    }
    
    // Create districts table
    $sql = "CREATE TABLE IF NOT EXISTS districts (
        district_id INT AUTO_INCREMENT PRIMARY KEY,
        district_name VARCHAR(100) NOT NULL,
        state_id INT NOT NULL,
        FOREIGN KEY (state_id) REFERENCES states(state_id) ON DELETE CASCADE
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Districts table created successfully<br>";
    } else {
        echo "Error creating districts table: " . $conn->error . "<br>";
    }
    
    // Create users table
    $sql = "CREATE TABLE IF NOT EXISTS users (
        user_id INT AUTO_INCREMENT PRIMARY KEY,
        first_name VARCHAR(50) NOT NULL,
        last_name VARCHAR(50) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        state_id INT NOT NULL,
        district_id INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (state_id) REFERENCES states(state_id),
        FOREIGN KEY (district_id) REFERENCES districts(district_id)
    )";
    
    if ($conn->query($sql) === TRUE) {
        echo "Users table created successfully<br>";
    } else {
        echo "Error creating users table: " . $conn->error . "<br>";
    }
    
    // Insert sample states
    $states = [
        'Maharashtra', 'Delhi', 'Karnataka', 'Tamil Nadu', 'Uttar Pradesh',
        'Gujarat', 'Rajasthan', 'West Bengal', 'Punjab', 'Kerala'
    ];
    
    foreach ($states as $state) {
        $sql = "INSERT IGNORE INTO states (state_name) VALUES ('$state')";
        $conn->query($sql);
    }
    echo "Sample states inserted<br>";
    
    // Insert sample districts
    $districts = [
        'Maharashtra' => ['Mumbai', 'Pune', 'Nagpur', 'Nashik', 'Thane'],
        'Delhi' => ['New Delhi', 'Central Delhi', 'North Delhi', 'South Delhi', 'West Delhi'],
        'Karnataka' => ['Bangalore', 'Mysore', 'Hubli', 'Mangalore', 'Belgaum'],
        'Tamil Nadu' => ['Chennai', 'Coimbatore', 'Madurai', 'Salem', 'Tiruchirappalli'],
        'Uttar Pradesh' => ['Lucknow', 'Kanpur', 'Varanasi', 'Agra', 'Allahabad'],
        'Gujarat' => ['Ahmedabad', 'Surat', 'Vadodara', 'Rajkot', 'Bhavnagar'],
        'Rajasthan' => ['Jaipur', 'Jodhpur', 'Udaipur', 'Kota', 'Ajmer'],
        'West Bengal' => ['Kolkata', 'Howrah', 'Durgapur', 'Siliguri', 'Asansol'],
        'Punjab' => ['Chandigarh', 'Ludhiana', 'Amritsar', 'Jalandhar', 'Patiala'],
        'Kerala' => ['Thiruvananthapuram', 'Kochi', 'Kozhikode', 'Thrissur', 'Kollam']
    ];
    
    foreach ($districts as $state => $districtList) {
        // Get state_id
        $sql = "SELECT state_id FROM states WHERE state_name = '$state'";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $state_id = $row['state_id'];
            
            foreach ($districtList as $district) {
                $sql = "INSERT IGNORE INTO districts (district_name, state_id) VALUES ('$district', $state_id)";
                $conn->query($sql);
            }
        }
    }
    echo "Sample districts inserted<br>";
    
    echo "<h3 style='color: green;'>Database setup completed successfully!</h3>";
    echo "<p><a href='registration.php' style='color: blue;'>Go to Registration Form</a></p>";
    
} catch(Exception $e) {
    die("ERROR: " . $e->getMessage());
}

$conn->close();
?>