<?php
include 'config.php';
session_start();

// Check if student data is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Store student data in database
    $stmt = $conn->prepare("INSERT INTO students (register_no, branch, programme_type, programme, regulation, stakeholder_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $_POST['register_no'], $_POST['branch'], $_POST['programme_type'], $_POST['programme'], $_POST['regulation'], $_SESSION['stakeholder_id']);
    
    if ($stmt->execute()) {
        $_SESSION['student_id'] = $stmt->insert_id;
        $_SESSION['student_data'] = [
            'register_no' => $_POST['register_no'],
            'branch' => $_POST['branch'],
            'programme_type' => $_POST['programme_type'],
            'programme' => $_POST['programme'],
            'regulation' => $_POST['regulation']
        ];
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}

// Get student data from session
$student_data = $_SESSION['student_data'] ?? [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback Questionnaire</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            background-color: #f5f7fa;
            color: #333;
            line-height: 1.6;
        }
        
        .container {
            max-width: 900px;
            margin: 30px auto;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            padding-bottom: 15px;
            border-bottom: 2px solid #eaeaea;
        }
        
        .header h1 {
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .student-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }
        
        .info-row {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 5px;
        }
        
        .info-label {
            font-weight: 600;
            width: 200px;
        }
        
        .question-section {
            margin-bottom: 25px;
        }
        
        .section-title {
            background-color: #3498db;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 18px;
        }
        
        .question {
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }
        
        .question-text {
            margin-bottom: 10px;
            font-weight: 500;
        }
        
        .rating-scale {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            color: #666;
        }
        
        .rating-options {
            display: flex;
            justify-content: space-between;
        }
        
        .rating-option {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        
        .rating-option input {
            margin-bottom: 5px;
        }
        
        textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            resize: vertical;
            min-height: 100px;
        }
        
        textarea:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.3);
        }
        
        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #3498db;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #2980b9;
        }
        
        .required {
            color: #e74c3c;
        }
        
        .error {
            color: #e74c3c;
            font-size: 14px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>QUESTIONNAIRE FOR STUDENT (<?php echo $student_data['branch'] ?? 'IT'; ?>)</h1>
        </div>
        
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">Register No:</div>
                <div><?php echo $student_data['register_no'] ?? ''; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Branch:</div>
                <div><?php echo $student_data['branch'] ?? ''; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Programme:</div>
                <div><?php echo $student_data['programme'] ?? ''; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Regulation:</div>
                <div><?php echo $student_data['regulation'] ?? ''; ?></div>
            </div>
        </div>
        
        <form id="feedbackForm" method="POST" action="save_feedback.php">
            <div class="question-section">
                <div class="section-title">Courses Contents of Curriculum are in tune with the Program Outcomes</div>
                
                <div class="question">
                    <div class="question-text">Courses Contents are designed to enable Problem Solving Skills and Core competencies</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q1_<?php echo $i; ?>" name="q1" value="<?php echo $i; ?>" required>
                            <label for="q1_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Courses placed in the curriculum serves the needs of both advanced and slow learners</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q2_<?php echo $i; ?>" name="q2" value="<?php echo $i; ?>" required>
                            <label for="q2_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Contact Hour Distribution among the various Course Components (LTP) is Satisfiable</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q3_<?php echo $i; ?>" name="q3" value="<?php echo $i; ?>" required>
                            <label for="q3_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Composition of Basic Sciences, Engineering, Humanities and Management Courses is a right mix and satisfiable</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q4_<?php echo $i; ?>" name="q4" value="<?php echo $i; ?>" required>
                            <label for="q4_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Laboratory sessions are sufficient to improve the technical skills of students</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q5_<?php echo $i; ?>" name="q5" value="<?php echo $i; ?>" required>
                            <label for="q5_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Inclusion of Minor Project/ Mini Projects improved the technical competency and leadership skills among the students</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q6_<?php echo $i; ?>" name="q6" value="<?php echo $i; ?>" required>
                            <label for="q6_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Electives have enabled the passion to learn new technologies in emerging areas</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q7_<?php echo $i; ?>" name="q7" value="<?php echo $i; ?>" required>
                            <label for="q7_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Curriculum is providing opportunity towards Self learning to realize the expectations</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q8_<?php echo $i; ?>" name="q8" value="<?php echo $i; ?>" required>
                            <label for="q8_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Overall satisfaction with the curriculum</div>
                    <div class="rating-scale">
                        <span>1 means Low</span>
                        <span>5 means High</span>
                    </div>
                    <div class="rating-options">
                        <?php for($i = 1; $i <= 5; $i++): ?>
                        <div class="rating-option">
                            <input type="radio" id="q9_<?php echo $i; ?>" name="q9" value="<?php echo $i; ?>" required>
                            <label for="q9_<?php echo $i; ?>"><?php echo $i; ?></label>
                        </div>
                        <?php endfor; ?>
                    </div>
                </div>
                
                <div class="question">
                    <div class="question-text">Suggest any other points to improve the quality of the Curriculum</div>
                    <textarea name="suggestions" placeholder="Enter your suggestions here..."></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn">SUBMIT</button>
        </form>
    </div>
    
    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            let isValid = true;
            
            // Clear previous errors
            document.querySelectorAll('.error').forEach(el => el.remove());
            
            // Validate all rating questions
            const ratingQuestions = document.querySelectorAll('.rating-options');
            ratingQuestions.forEach((questionGroup, index) => {
                const questionNumber = index + 1;
                const selectedOption = questionGroup.querySelector('input:checked');
                
                if (!selectedOption) {
                    isValid = false;
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'Please select a rating for this question';
                    questionGroup.parentNode.appendChild(error);
                }
            });
            
            if (!isValid) {
                e.preventDefault();
            }
        });
    </script>
</body>
</html>