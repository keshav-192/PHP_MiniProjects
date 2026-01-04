<?php
include 'config.php';

// Check if stakeholder data exists
if (!isset($_SESSION['stakeholder_id'])) {
    redirect('index.php', 'Please fill out personal details first.');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; }
        .container { max-width: 800px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid #eaeaea; }
        .header h1 { color: #2c3e50; margin-bottom: 10px; }
        .form-section { margin-bottom: 25px; }
        .section-title { background-color: #3498db; color: white; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; font-size: 18px; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #2c3e50; }
        input[type="text"], select { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; transition: border 0.3s; }
        input:focus, select:focus { border-color: #3498db; outline: none; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
        .btn { display: block; width: 100%; padding: 12px; background-color: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s; }
        .btn:hover { background-color: #2980b9; }
        .required { color: #e74c3c; }
        .error { color: #e74c3c; font-size: 14px; margin-top: 5px; }
        .valid { color: #27ae60; font-size: 14px; margin-top: 5px; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .nav-links { display: flex; justify-content: space-between; margin-top: 20px; }
        .nav-links a { color: #3498db; text-decoration: none; padding: 8px 15px; border: 1px solid #3498db; border-radius: 5px; transition: all 0.3s; }
        .nav-links a:hover { background-color: #3498db; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>STUDENT DETAILS</h1>
            <p>Please provide your academic information</p>
        </div>
        
        <?php displayMessage(); ?>
        
        <form id="studentForm" method="POST" action="process_student.php">
            <div class="form-section">
                <div class="section-title">PROGRAMME DETAILS</div>
                
                <div class="form-group">
                    <label for="register_no">Register No <span class="required">*</span></label>
                    <input type="text" id="register_no" name="register_no" placeholder="Enter register number" required>
                </div>
                
                <div class="form-group">
                    <label for="programme_type">Programme Type <span class="required">*</span></label>
                    <select id="programme_type" name="programme_type" required>
                        <option value="">Select</option>
                        <option value="UG">UG</option>
                        <option value="PG">PG</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="programme">Programme <span class="required">*</span></label>
                    <select id="programme" name="programme" required disabled>
                        <option value="">Select</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="branch">Branch <span class="required">*</span></label>
                    <select id="branch" name="branch" required disabled>
                        <option value="">Select</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="regulation">Regulation <span class="required">*</span></label>
                    <select id="regulation" name="regulation" required>
                        <option value="">Select</option>
                        <option value="R19">R19</option>
                        <option value="R22">R22</option>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="btn">SUBMIT & CONTINUE TO FEEDBACK</button>
        </form>
        
        <div class="nav-links">
            <a href="index.php">← Back to Personal Details</a>
            <a href="report_form.php">View Reports →</a>
        </div>
    </div>
    
    <script>
        // Programme and branch data
        const programmes = {
            'UG': ['Btech', 'BBA', 'BCA', 'BPharm'],
            'PG': ['Mtech', 'MBA', 'MCA']
        };
        
        const branches = {
            'Btech': ['IT', 'CSE', 'ECE', 'EEE', 'MECH', 'CIVIL'],
            'BBA': ['General Management', 'Finance', 'Marketing'],
            'BCA': ['Computer Applications'],
            'BPharm': ['Pharmacy'],
            'Mtech': ['Computer Science', 'Electronics', 'Mechanical'],
            'MBA': ['Finance', 'Marketing', 'HR', 'Operations'],
            'MCA': ['Computer Applications']
        };
        
        // Programme type change handler
        document.getElementById('programme_type').addEventListener('change', function() {
            const programmeSelect = document.getElementById('programme');
            const branchSelect = document.getElementById('branch');
            
            programmeSelect.innerHTML = '<option value="">Select</option>';
            branchSelect.innerHTML = '<option value="">Select</option>';
            
            if (this.value) {
                programmeSelect.disabled = false;
                programmes[this.value].forEach(programme => {
                    const option = document.createElement('option');
                    option.value = programme;
                    option.textContent = programme;
                    programmeSelect.appendChild(option);
                });
            } else {
                programmeSelect.disabled = true;
                branchSelect.disabled = true;
            }
        });
        
        // Programme change handler
        document.getElementById('programme').addEventListener('change', function() {
            const branchSelect = document.getElementById('branch');
            branchSelect.innerHTML = '<option value="">Select</option>';
            
            if (this.value) {
                branchSelect.disabled = false;
                branches[this.value].forEach(branch => {
                    const option = document.createElement('option');
                    option.value = branch;
                    option.textContent = branch;
                    branchSelect.appendChild(option);
                });
            } else {
                branchSelect.disabled = true;
            }
        });
        
        // Helper to clear register number messages
        const registerField = document.getElementById('register_no');
        function clearRegisterMessages() {
            const parent = registerField.parentNode;
            parent.querySelectorAll('.error, .valid').forEach(el => el.remove());
        }

        function validateRegisterFormat(showMessage = true) {
            clearRegisterMessages();
            const value = registerField.value.trim();
            if (!value) return true; // required check handled on submit

            // Exactly 10 characters, letters or digits only (no spaces or special characters)
            const regRegex = /^[A-Za-z0-9]{10}$/;
            if (!regRegex.test(value)) {
                if (showMessage) {
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'Register number must contain exactly 10 characters with only letters and digits (no spaces or special characters)';
                    registerField.parentNode.appendChild(error);
                }
                return false;
            }
            return true;
        }

        // Live duplicate check for register number on blur
        registerField.addEventListener('blur', function () {
            // First check format
            if (!validateRegisterFormat(true)) {
                return;
            }

            const value = registerField.value.trim();
            if (!value) return; // required check handled on submit

            const formData = new FormData();
            formData.append('register_no', value);

            fetch('check_register.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                clearRegisterMessages();
                if (!data.success) {
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'Please enter a valid register number';
                    registerField.parentNode.appendChild(error);
                    return;
                }

                if (data.exists) {
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'register number already registered';
                    registerField.parentNode.appendChild(error);
                } else {
                    const valid = document.createElement('div');
                    valid.className = 'valid';
                    valid.textContent = 'Valid';
                    registerField.parentNode.appendChild(valid);
                }
            })
            .catch(() => {
                clearRegisterMessages();
                const error = document.createElement('div');
                error.className = 'error';
                error.textContent = 'Please enter a valid register number';
                registerField.parentNode.appendChild(error);
            });
        });

        // Form validation
        document.getElementById('studentForm').addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.error').forEach(el => el.remove());
            
            const requiredFields = document.querySelectorAll('[required]');
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'This field is required';
                    field.parentNode.appendChild(error);
                }
            });

            // Register number format validation (10 alphabets, no digits/spaces/special chars)
            if (!validateRegisterFormat(true)) {
                isValid = false;
            }

            // Do not allow submit if duplicate register number is indicated
            const existingError = registerField.parentNode.querySelector('.error');
            if (existingError && existingError.textContent.includes('register number already registered')) {
                isValid = false;
            }
            
            if (!isValid) e.preventDefault();
        });
    </script>
</body>
</html>