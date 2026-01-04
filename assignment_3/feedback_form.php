<?php
include 'config.php';

// Check if student data exists
if (!isset($_SESSION['student_id'])) {
    redirect('index.php', 'Please complete previous steps first.');
}

$student_data = $_SESSION['student_data'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Feedback Questionnaire</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid #eaeaea; }
        .header h1 { color: #2c3e50; margin-bottom: 10px; }
        .student-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #3498db; }
        .info-row { display: flex; flex-wrap: wrap; margin-bottom: 5px; }
        .info-label { font-weight: 600; width: 200px; }
        .question-section { margin-bottom: 25px; }
        .section-title { background-color: #3498db; color: white; padding: 10px 15px; border-radius: 5px; margin-bottom: 15px; font-size: 18px; }
        .question { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .question-text { margin-bottom: 10px; font-weight: 500; }
        .rating-scale { display: flex; justify-content: space-between; margin-bottom: 10px; font-size: 14px; color: #666; }
        .rating-options { display: flex; justify-content: space-between; }
        .rating-option { display: flex; flex-direction: column; align-items: center; }
        .rating-option input { margin-bottom: 5px; }
        textarea { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; resize: vertical; min-height: 100px; }
        textarea:focus { border-color: #3498db; outline: none; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
        .btn { display: block; width: 100%; padding: 12px; background-color: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s; }
        .btn:hover { background-color: #2980b9; }
        .required { color: #e74c3c; }
        .error { color: #e74c3c; font-size: 14px; margin-top: 5px; }
        .message { background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb; }
        .nav-links { display: flex; justify-content: space-between; margin-top: 20px; }
        .nav-links a { color: #3498db; text-decoration: none; padding: 8px 15px; border: 1px solid #3498db; border-radius: 5px; transition: all 0.3s; }
        .nav-links a:hover { background-color: #3498db; color: white; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>QUESTIONNAIRE FOR STUDENT (<?php echo $student_data['branch']; ?>)</h1>
            <p>Please provide your valuable feedback</p>
        </div>
        
        <?php displayMessage(); ?>
        
        <div class="student-info">
            <div class="info-row">
                <div class="info-label">Register No:</div>
                <div><?php echo $student_data['register_no']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Branch:</div>
                <div><?php echo $student_data['branch']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Programme:</div>
                <div><?php echo $student_data['programme']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Regulation:</div>
                <div><?php echo $student_data['regulation']; ?></div>
            </div>
        </div>
        
        <form id="feedbackForm" method="POST" action="process_feedback.php">
            <div class="question-section">
                <div class="section-title">Curriculum Feedback (1 means Low, 5 means High)</div>
                
                <?php
                $questions = [
                    'q1' => 'Courses Contents are designed to enable Problem Solving Skills and Core competencies',
                    'q2' => 'Courses placed in the curriculum serves the needs of both advanced and slow learners',
                    'q3' => 'Contact Hour Distribution among the various Course Components (LTP) is Satisfiable',
                    'q4' => 'Composition of Basic Sciences, Engineering, Humanities and Management Courses is a right mix and satisfiable',
                    'q5' => 'Laboratory sessions are sufficient to improve the technical skills of students',
                    'q6' => 'Inclusion of Minor Project/ Mini Projects improved the technical competency and leadership skills among the students',
                    'q7' => 'Electives have enabled the passion to learn new technologies in emerging areas',
                    'q8' => 'Curriculum is providing opportunity towards Self learning to realize the expectations',
                    'q9' => 'Overall satisfaction with the curriculum structure and implementation'
                ];
                
                foreach ($questions as $key => $question) {
                    echo '
                    <div class="question">
                        <div class="question-text">' . $question . '</div>
                        <div class="rating-scale">
                            <span>1 (Low)</span>
                            <span>5 (High)</span>
                        </div>
                        <div class="rating-options">';
                    
                    for ($i = 1; $i <= 5; $i++) {
                        echo '
                            <div class="rating-option">
                                <input type="radio" id="' . $key . '_' . $i . '" name="' . $key . '" value="' . $i . '" required>
                                <label for="' . $key . '_' . $i . '">' . $i . '</label>
                            </div>';
                    }
                    
                    echo '
                        </div>
                    </div>';
                }
                ?>
                
                <div class="question">
                    <div class="question-text">Suggest any other points to improve the quality of the Curriculum</div>
                    <textarea name="suggestions" placeholder="Enter your suggestions here..."></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn">SUBMIT FEEDBACK</button>
        </form>
        
        <div class="nav-links">
            <a href="student_details.php">← Back to Student Details</a>
            <a href="report_form.php">View Reports →</a>
        </div>
    </div>
    
    <script>
        document.getElementById('feedbackForm').addEventListener('submit', function(e) {
            let isValid = true;
            document.querySelectorAll('.error').forEach(el => el.remove());
            
            const ratingQuestions = document.querySelectorAll('.rating-options');
            ratingQuestions.forEach((questionGroup, index) => {
                const selectedOption = questionGroup.querySelector('input:checked');
                if (!selectedOption) {
                    isValid = false;
                    const error = document.createElement('div');
                    error.className = 'error';
                    error.textContent = 'Please select a rating for this question';
                    questionGroup.parentNode.appendChild(error);
                }
            });
            
            if (!isValid) e.preventDefault();
        });
    </script>
</body>
</html>