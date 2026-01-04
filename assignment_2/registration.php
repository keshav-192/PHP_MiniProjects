<?php
include 'config.php';
session_start();

$errors = [];
$success = '';

// Helper: validate email domain (allow common providers and institutional domains)
function is_valid_email_domain($email) {
    if (!preg_match('/^[A-Za-z0-9._%+-]+@([A-Za-z0-9.-]+\.[A-Za-z]{2,})$/', $email, $matches)) {
        return false;
    }

    $domain = strtolower($matches[1]);

    // Common public providers
    $allowedProviders = [
        'gmail.com',
        'yahoo.com',
        'yahoo.in',
        'outlook.com',
        'hotmail.com',
        'live.com',
        'icloud.com',
        'protonmail.com',
        'proton.me'
    ];

    if (in_array($domain, $allowedProviders, true)) {
        return true;
    }

    // Institutional domains: *.edu, *.edu.xx, *.ac.xx
    if (preg_match('/\.edu(\.[a-z]{2})?$/', $domain)) {
        return true;
    }
    if (preg_match('/\.ac\.[a-z]{2}$/', $domain)) {
        return true;
    }

    return false;
}

// Get states for dropdown
$states = [];
$sql = "SELECT * FROM states ORDER BY state_name";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $states[] = $row;
    }
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $state_id = $_POST['state_id'];
    $district_id = $_POST['district_id'];
    
    // Validation
    if (empty($first_name)) {
        $errors[] = "First name is required.";
    } elseif (!preg_match("/^[A-Za-z ]+$/", $first_name)) {
        $errors[] = "First name should contain only letters and spaces.";
    }

    if (empty($last_name)) {
        $errors[] = "Last name is required.";
    } elseif (!preg_match("/^[A-Za-z ]+$/", $last_name)) {
        $errors[] = "Last name should contain only letters and spaces.";
    }

    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!preg_match("/^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/", $email)) {
        $errors[] = "Please enter a valid email address.";
    } elseif (!is_valid_email_domain($email)) {
        $errors[] = "Please use a valid email provider or institutional domain.";
    }

    if (empty($password)) {
        $errors[] = "Password is required.";
    } elseif (!preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/", $password)) {
        $errors[] = "Password must be at least 8 characters and include upper, lower, number, and special character.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (empty($state_id)) $errors[] = "State is required.";
    if (empty($district_id)) $errors[] = "District is required.";
    
    // Check if email already exists
    if (empty($errors)) {
        $sql = "SELECT user_id FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $errors[] = "Email already exists.";
        }
        $stmt->close();
    }
    
    // If no errors, insert user
    if (empty($errors)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (first_name, last_name, email, password, state_id, district_id) 
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssii", $first_name, $last_name, $email, $hashed_password, $state_id, $district_id);
        
        if ($stmt->execute()) {
            $success = "Registration successful!";
            // Clear form
            $_POST = [];
        } else {
            $errors[] = "Error saving user: " . $conn->error;
        }
        $stmt->close();
    }
}

// AJAX: Check if email is already registered
if (isset($_GET['check_email'])) {
    $email = isset($_GET['email']) ? trim($_GET['email']) : '';
    $exists = false;
    if ($email !== '') {
        $sql = "SELECT user_id FROM users WHERE email = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->num_rows > 0;
        $stmt->close();
    }
    echo json_encode(['exists' => $exists]);
    exit;
}

// Get districts based on selected state (for AJAX)
if (isset($_GET['state_id'])) {
    $state_id = $_GET['state_id'];
    $sql = "SELECT * FROM districts WHERE state_id = ? ORDER BY district_name";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $state_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $districts = [];
    while($row = $result->fetch_assoc()) {
        $districts[] = $row;
    }
    echo json_encode($districts);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Registration</title>
    <style>
        * {
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #e2e8f0 40%, #ffffff 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 32px 16px;
        }
        
        .container {
            width: 100%;
            max-width: 540px;
            margin: 0 auto;
        }
        
        .card {
            background: #ffffff;
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.16);
            border: 1px solid #e0e7ff;
            overflow: hidden;
            position: relative;
        }

        .card::before {
            content: "";
            position: absolute;
            top: 0;
            bottom: 0;
            left: 0;
            width: 5px;
            background: linear-gradient(180deg, #4c7cff, #9f7aea);
            border-radius: 16px 0 0 16px;
            pointer-events: none;
        }

        .card-header {
            background: linear-gradient(135deg, #f1f5ff, #e0ecff);
            color: #1f2937;
            padding: 18px 24px 12px;
            border-bottom: 1px solid #dbe3f0;
            text-align: center;
        }
        
        .card-header h1 {
            font-size: 24px;
            margin-bottom: 4px;
            letter-spacing: 0.02em;
        }

        .card-header p {
            font-size: 13px;
            color: #6b7280;
        }
        
        .card-body {
            padding: 22px 24px 20px;
            background: #ffffff;
        }
        
        .form-group {
            margin-bottom: 16px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }
        
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #d0d0d5;
            border-radius: 5px;
            font-size: 14px;
            background-color: #fafafa;
        }
        
        input:focus, select:focus {
            outline: none;
            border-color: #4c7cff;
            box-shadow: 0 0 0 2px rgba(76,124,255,0.15);
            background-color: #ffffff;
        }
        
        .btn {
            background: #4c7cff;
            color: #ffffff;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            width: 100%;
            transition: background 0.3s, box-shadow 0.3s;
        }
        
        .btn:hover {
            background: #3b66d6;
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        }
        
        .alert {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-error {
            background: #ffecec;
            color: #c0392b;
            border: 1px solid #f5b7b1;
        }
        
        .alert-success {
            background: #e9f7ef;
            color: #1e8449;
            border: 1px solid #a9dfbf;
            text-align: center;
        }
        
        .form-row {
            display: flex;
            gap: 15px;
        }
        
        .form-row .form-group {
            flex: 1;
        }
        
        .password-wrapper {
            position: relative;
        }
        
        .password-wrapper input {
            width: 100%;
            padding-right: 38px;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            cursor: pointer;
            color: #6b7280;
            font-size: 16px;
            padding: 0;
        }
        
        .link-container {
            text-align: center;
            margin-top: 18px;
        }
        
        .link-container a {
            color: #4c7cff;
            text-decoration: none;
            font-size: 14px;
        }
        
        .link-container a:hover {
            text-decoration: none;
        }

        .field-message {
            font-size: 12px;
            margin-top: 4px;
            display: block;
        }

        .field-error {
            color: #d63031;
        }

        .field-valid {
            color: #27ae60;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>User Registration</h1>
                <p>Create your account</p>
            </div>
            <div class="card-body">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-error">
                        <?php foreach ($errors as $error): ?>
                            <p><?php echo $error; ?></p>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success">
                        <p><?php echo $success; ?></p>
                    </div>
                <?php endif; ?>
                
                <form method="POST" id="registrationForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" 
                                   value="<?php echo isset($_POST['first_name']) ? $_POST['first_name'] : ''; ?>" 
                                   required>
                            <small id="first_name_msg" class="field-message"></small>
                        </div>
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" 
                                   value="<?php echo isset($_POST['last_name']) ? $_POST['last_name'] : ''; ?>" 
                                   required>
                            <small id="last_name_msg" class="field-message"></small>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo isset($_POST['email']) ? $_POST['email'] : ''; ?>" 
                               required>
                        <small id="email_msg" class="field-message"></small>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="state_id">State</label>
                            <select id="state_id" name="state_id" required>
                                <option value="">Select State</option>
                                <?php foreach ($states as $state): ?>
                                    <option value="<?php echo $state['state_id']; ?>" 
                                        <?php echo (isset($_POST['state_id']) && $_POST['state_id'] == $state['state_id']) ? 'selected' : ''; ?>>
                                        <?php echo $state['state_name']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <small id="state_id_msg" class="field-message"></small>
                        </div>
                        <div class="form-group">
                            <label for="district_id">District</label>
                            <select id="district_id" name="district_id" required>
                                <option value="">Select District</option>
                                <?php 
                                if (isset($_POST['state_id']) && !empty($_POST['state_id'])) {
                                    $selected_state_id = $_POST['state_id'];
                                    $sql = "SELECT * FROM districts WHERE state_id = $selected_state_id ORDER BY district_name";
                                    $result = $conn->query($sql);
                                    if ($result->num_rows > 0) {
                                        while($district = $result->fetch_assoc()) {
                                            $selected = (isset($_POST['district_id']) && $_POST['district_id'] == $district['district_id']) ? 'selected' : '';
                                            echo '<option value="' . $district['district_id'] . '" ' . $selected . '>' . $district['district_name'] . '</option>';
                                        }
                                    }
                                }
                                ?>
                            </select>
                            <small id="district_id_msg" class="field-message"></small>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="password">Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="password" name="password" required>
                                <button type="button" class="password-toggle" data-target="password">&#128065;</button>
                            </div>
                            <small id="password_msg" class="field-message"></small>
                        </div>
                        <div class="form-group">
                            <label for="confirm_password">Confirm Password</label>
                            <div class="password-wrapper">
                                <input type="password" id="confirm_password" name="confirm_password" required>
                                <button type="button" class="password-toggle" data-target="confirm_password">&#128065;</button>
                            </div>
                            <small id="confirm_password_msg" class="field-message"></small>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Register</button>
                </form>
                
                <div class="link-container">
                    <a href="display.php">View Registered Users</a>
                </div>
            </div>
        </div>
    </div>

    <script>
        const namePattern = /^[A-Za-z ]+$/;
        const emailPattern = /^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$/;
        const passwordPattern = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[^A-Za-z0-9]).{8,}$/;

        function setMessage(el, message, isValid) {
            el.textContent = message;
            el.classList.remove('field-error', 'field-valid');
            if (!message) {
                return;
            }
            el.classList.add(isValid ? 'field-valid' : 'field-error');
        }

        function validateName(inputId, msgId, label) {
            const input = document.getElementById(inputId);
            const msgEl = document.getElementById(msgId);
            const value = input.value.trim();
            if (!value) {
                setMessage(msgEl, label + ' is required.', false);
                return false;
            }
            if (!namePattern.test(value)) {
                setMessage(msgEl, label + ' should contain only letters and spaces.', false);
                return false;
            }
            setMessage(msgEl, 'Valid', true);
            return true;
        }

        function validateEmail() {
            const input = document.getElementById('email');
            const msgEl = document.getElementById('email_msg');
            const value = input.value.trim();
            if (!value) {
                setMessage(msgEl, 'Email is required.', false);
                return false;
            }
            if (!emailPattern.test(value)) {
                setMessage(msgEl, 'Please enter a valid email address.', false);
                return false;
            }

            const domain = value.split('@')[1]?.toLowerCase() || '';
            const allowedProviders = [
                'gmail.com',
                'yahoo.com',
                'yahoo.in',
                'outlook.com',
                'hotmail.com',
                'live.com',
                'icloud.com',
                'protonmail.com',
                'proton.me'
            ];

            const institutionalEdu = /\.edu(\.[a-z]{2})?$/;
            const institutionalAc = /\.ac\.[a-z]{2}$/;

            const isAllowed =
                allowedProviders.includes(domain) ||
                institutionalEdu.test(domain) ||
                institutionalAc.test(domain);

            if (!isAllowed) {
                setMessage(msgEl, 'Please use a valid email provider or institutional domain.', false);
                return false;
            }

            // Check with server if this email is already registered
            setMessage(msgEl, 'Checking email...', true);
            fetch(`registration.php?check_email=1&email=${encodeURIComponent(value)}`)
                .then(response => response.json())
                .then(data => {
                    if (data && data.exists) {
                        setMessage(msgEl, 'Email already registered.', false);
                    } else {
                        setMessage(msgEl, 'Valid', true);
                    }
                })
                .catch(() => {
                    setMessage(msgEl, 'Valid', true);
                });

            return true;
        }

        function validatePassword() {
            const input = document.getElementById('password');
            const msgEl = document.getElementById('password_msg');
            const value = input.value;
            if (!value) {
                setMessage(msgEl, 'Password is required.', false);
                return false;
            }
            if (!passwordPattern.test(value)) {
                setMessage(msgEl, 'Password must be at least 8 characters and include upper, lower, number, and special character.', false);
                return false;
            }
            setMessage(msgEl, 'Valid', true);
            return true;
        }

        function validateConfirmPassword() {
            const password = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            const msgEl = document.getElementById('confirm_password_msg');
            if (!confirm) {
                setMessage(msgEl, 'Please confirm your password.', false);
                return false;
            }
            if (password !== confirm) {
                setMessage(msgEl, 'Passwords do not match.', false);
                return false;
            }
            setMessage(msgEl, 'Valid', true);
            return true;
        }

        function validateSelect(id, msgId, label) {
            const select = document.getElementById(id);
            const msgEl = document.getElementById(msgId);
            if (!select.value) {
                setMessage(msgEl, label + ' is required.', false);
                return false;
            }
            setMessage(msgEl, 'Valid', true);
            return true;
        }

        document.getElementById('first_name').addEventListener('blur', function () {
            validateName('first_name', 'first_name_msg', 'First name');
        });

        document.getElementById('last_name').addEventListener('blur', function () {
            validateName('last_name', 'last_name_msg', 'Last name');
        });

        document.getElementById('email').addEventListener('blur', validateEmail);
        document.getElementById('password').addEventListener('blur', validatePassword);
        document.getElementById('confirm_password').addEventListener('blur', validateConfirmPassword);

        document.getElementById('state_id').addEventListener('blur', function () {
            validateSelect('state_id', 'state_id_msg', 'State');
        });

        document.getElementById('district_id').addEventListener('blur', function () {
            validateSelect('district_id', 'district_id_msg', 'District');
        });

        document.getElementById('state_id').addEventListener('change', function() {
            const stateId = this.value;
            const districtSelect = document.getElementById('district_id');
            
            if (stateId) {
                // Fetch districts for selected state
                fetch(`registration.php?state_id=${stateId}`)
                    .then(response => response.json())
                    .then(districts => {
                        districtSelect.innerHTML = '<option value="">Select District</option>';
                        districts.forEach(district => {
                            const option = document.createElement('option');
                            option.value = district.district_id;
                            option.textContent = district.district_name;
                            districtSelect.appendChild(option);
                        });
                        validateSelect('district_id', 'district_id_msg', 'District');
                    })
                    .catch(error => {
                        console.error('Error fetching districts:', error);
                    });
            } else {
                districtSelect.innerHTML = '<option value="">Select District</option>';
            }
        });

        document.getElementById('registrationForm').addEventListener('submit', function (e) {
            const validFirst = validateName('first_name', 'first_name_msg', 'First name');
            const validLast = validateName('last_name', 'last_name_msg', 'Last name');
            const validEmail = validateEmail();
            const validPass = validatePassword();
            const validConfirm = validateConfirmPassword();
            const validState = validateSelect('state_id', 'state_id_msg', 'State');
            const validDistrict = validateSelect('district_id', 'district_id_msg', 'District');

            if (!(validFirst && validLast && validEmail && validPass && validConfirm && validState && validDistrict)) {
                e.preventDefault();
            }
        });

        document.querySelectorAll('.password-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const targetId = btn.getAttribute('data-target');
                const input = document.getElementById(targetId);
                if (!input) return;
                const isPassword = input.type === 'password';
                input.type = isPassword ? 'text' : 'password';
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>