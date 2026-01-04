<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stakeholder Feedback Form</title>
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
        input[type="text"], input[type="email"], input[type="tel"], select { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; transition: border 0.3s; }
        input:focus, select:focus { border-color: #3498db; outline: none; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
        .radio-group { display: flex; gap: 15px; margin-top: 5px; }
        .radio-option { display: flex; align-items: center; gap: 5px; }
        .btn { display: block; width: 100%; padding: 12px; background-color: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s; }
        .btn:hover { background-color: #2980b9; }
        .required { color: #e74c3c; }
        .error { color: #e74c3c; font-size: 14px; margin-top: 5px; }
        .valid { color: #27ae60; font-size: 14px; margin-top: 5px; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>VIGNAN'S FOUNDATION FOR SCIENCE, TECHNOLOGY & RESEARCH</h1>
            <p>Stakeholder Feedback Form</p>
        </div>
        
        <?php displayMessage(); ?>
        
        <form id="stakeholderForm" method="POST" action="process_stakeholder.php">
            <div class="form-section">
                <div class="section-title">PERSONAL DETAILS</div>
                
                <div class="form-group">
                    <label for="firstname">Name of the Stakeholder <span class="required">*</span></label>
                    <input type="text" id="firstname" name="firstname" placeholder="Please enter your firstname" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone <span class="required">*</span></label>
                    <input type="tel" id="phone" name="phone" placeholder="Please enter your phone" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email" placeholder="Please enter your email" required>
                </div>
                
                <div class="form-group">
                    <label for="stakeholder_type">Type of the Stakeholder <span class="required">*</span></label>
                    <select id="stakeholder_type" name="stakeholder_type" required>
                        <option value="">Select the type</option>
                        <option value="Student">Student</option>
                        <option value="Faculty">Faculty</option>
                        <option value="Alumni">Alumni</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="academic_year">Feedback for Academic Year <span class="required">*</span></label>
                    <select id="academic_year" name="academic_year" required>
                        <option value="">Select the Academic Year</option>
                        <option value="2022-2023">2022-2023</option>
                        <option value="2023-2024">2023-2024</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Gender <span class="required">*</span></label>
                    <div class="radio-group">
                        <div class="radio-option">
                            <input type="radio" id="male" name="gender" value="Male" required>
                            <label for="male">Male</label>
                        </div>
                        <div class="radio-option">
                            <input type="radio" id="female" name="gender" value="Female">
                            <label for="female">Female</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <button type="submit" class="btn">SUBMIT</button>
        </form>
        
        <div style="text-align: center; margin-top: 20px;">
            <a href="report_form.php" style="color: #3498db; text-decoration: none;">View Existing Reports →</a>
        </div>
    </div>
    
    <script>
        const nameField = document.getElementById('firstname');
        const phoneField = document.getElementById('phone');
        const emailField = document.getElementById('email');

        function clearNameMessages() {
            const parent = nameField.parentNode;
            parent.querySelectorAll('.error, .valid').forEach(el => el.remove());
        }

        function clearPhoneMessages() {
            const parent = phoneField.parentNode;
            parent.querySelectorAll('.error, .valid').forEach(el => el.remove());
        }

        function clearEmailMessages() {
            const parent = emailField.parentNode;
            parent.querySelectorAll('.error, .valid').forEach(el => el.remove());
        }

        function toCamelCaseName(value) {
            return value
                .toLowerCase()
                .split(/\s+/)
                .filter(Boolean)
                .map(word => word.charAt(0).toUpperCase() + word.slice(1))
                .join(' ');
        }

        function validateName(showValidMessage = true) {
            clearNameMessages();
            const rawValue = nameField.value.trim();
            if (!rawValue) return true; // required check handled separately

            // Auto-format to Camel Case
            const formatted = toCamelCaseName(rawValue);
            nameField.value = formatted;

            const nameRegex = /^[A-Z][a-z]*(?: [A-Z][a-z]*)*$/;
            if (!nameRegex.test(formatted)) {
                const error = document.createElement('div');
                error.className = 'error';
                error.textContent = 'Name must start with a capital letter in each word and contain only letters and spaces';
                nameField.parentNode.appendChild(error);
                return false;
            }

            if (showValidMessage) {
                const valid = document.createElement('div');
                valid.className = 'valid';
                valid.textContent = 'Valid';
                nameField.parentNode.appendChild(valid);
            }
            return true;
        }

        function validatePhone(showValidMessage = true) {
            clearPhoneMessages();
            const rawValue = phoneField.value.trim();
            if (!rawValue) return true; // required check handled separately

            // First digit 6-9, total 10 digits
            const phoneRegex = /^[6-9]\d{9}$/;
            if (!phoneRegex.test(rawValue)) {
                const error = document.createElement('div');
                error.className = 'error';
                error.textContent = 'Phone must be 10 digits starting with 6, 7, 8, or 9 and contain no spaces or special characters';
                phoneField.parentNode.appendChild(error);
                return false;
            }

            if (showValidMessage) {
                const valid = document.createElement('div');
                valid.className = 'valid';
                valid.textContent = 'Valid';
                phoneField.parentNode.appendChild(valid);
            }
            return true;
        }

        function validateEmail(showValidMessage = true) {
            clearEmailMessages();
            const rawValue = emailField.value.trim();
            if (!rawValue) return true; // required check handled separately

            // Basic email format check (allows any valid domain like gmail, yahoo, hotmail, institutional, etc.)
            const basicEmailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!basicEmailRegex.test(rawValue)) {
                const error = document.createElement('div');
                error.className = 'error';
                error.textContent = 'Please enter a valid email address';
                emailField.parentNode.appendChild(error);
                return false;
            }

            // NOTE: duplicate checking is done via AJAX on blur and on the server on submit
            if (showValidMessage) {
                const valid = document.createElement('div');
                valid.className = 'valid';
                valid.textContent = 'Valid';
                emailField.parentNode.appendChild(valid);
            }
            return true;
        }

        // Validate and auto-format when user leaves the name field
        nameField.addEventListener('blur', function () {
            validateName(true);
        });

        // Validate phone when user leaves the phone field
        phoneField.addEventListener('blur', function () {
            validatePhone(true);
        });

        // Validate email when user leaves the email field + check for duplicates via AJAX
        emailField.addEventListener('blur', function () {
            // First validate format
            if (!validateEmail(false)) {
                return;
            }

            // Then check if email already exists via AJAX
            clearEmailMessages();
            const rawValue = emailField.value.trim();

            const formData = new FormData();
            formData.append('email', rawValue);

            fetch('check_email.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                clearEmailMessages();
                if (!data.success) {
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'Please enter a valid email address';
                    emailField.parentNode.appendChild(error);
                    return;
                }

                if (data.exists) {
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'email already registered';
                    emailField.parentNode.appendChild(error);
                } else {
                    const valid = document.createElement('div');
                    valid.className = 'valid';
                    valid.textContent = 'Valid';
                    emailField.parentNode.appendChild(valid);
                }
            })
            .catch(() => {
                // On error, just fall back to basic format validation message
                clearEmailMessages();
                const error = document.createElement('div');
                error.className = 'error';
                error.textContent = 'Please enter a valid email address';
                emailField.parentNode.appendChild(error);
            });
        });

        document.getElementById('stakeholderForm').addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.error').forEach(el => el.remove());
            document.querySelectorAll('.valid').forEach(el => el.remove());
            
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

            // Name validation: auto-format + rules
            if (!validateName(true)) {
                isValid = false;
            }

            // Phone validation: exactly 10 digits, starting 6-9
            if (!validatePhone(true)) {
                isValid = false;
            }

            // Email validation: format only (duplicate is checked again on server)
            if (!validateEmail(true)) {
                isValid = false;
            }

            if (!isValid) e.preventDefault();
        });
    </script>
</body>
</html>