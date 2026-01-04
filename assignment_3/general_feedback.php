<?php
include 'config.php';

// Check if stakeholder data exists
if (!isset($_SESSION['stakeholder_id'])) {
    redirect('index.php', 'Please fill out personal details first.');
}

$stakeholder_data = $_SESSION['stakeholder_data'];
$stakeholder_type = $stakeholder_data['stakeholder_type'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $stakeholder_type; ?> Feedback Form</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; }
        .container { max-width: 900px; margin: 30px auto; padding: 20px; background: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid #eaeaea; }
        .header h1 { color: #2c3e50; margin-bottom: 10px; }
        .stakeholder-info { background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin-bottom: 20px; border-left: 4px solid #3498db; }
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
            <h1><?php echo $stakeholder_type; ?> FEEDBACK QUESTIONNAIRE</h1>
            <p>Please provide your valuable feedback</p>
        </div>
        
        <?php displayMessage(); ?>
        
        <div class="stakeholder-info">
            <div class="info-row">
                <div class="info-label">Name:</div>
                <div><?php echo $stakeholder_data['firstname']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Email:</div>
                <div><?php echo $stakeholder_data['email']; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Stakeholder Type:</div>
                <div><?php echo $stakeholder_type; ?></div>
            </div>
            <div class="info-row">
                <div class="info-label">Academic Year:</div>
                <div><?php echo $stakeholder_data['academic_year']; ?></div>
            </div>
        </div>
        
        <form id="feedbackForm" method="POST" action="process_general_feedback.php">
            <input type="hidden" name="stakeholder_type" value="<?php echo $stakeholder_type; ?>">
            
            <div class="question-section">
                <div class="section-title">General Feedback (1 means Low, 5 means High)</div>
                
                <?php
                // Different questions based on stakeholder type
                if ($stakeholder_type === 'Faculty') {
                    $questions = [
                        'q1' => 'The curriculum adequately prepares students for industry requirements',
                        'q2' => 'Course content is up-to-date with current industry trends',
                        'q3' => 'Laboratory facilities and equipment are sufficient for practical learning',
                        'q4' => 'Student engagement and participation in classes is satisfactory',
                        'q5' => 'The academic calendar and schedule are well planned',
                        'q6' => 'Support from administration for teaching activities is adequate',
                        'q7' => 'Opportunities for faculty development and training are sufficient',
                        'q8' => 'Research opportunities and infrastructure are satisfactory',
                        'q9' => 'Overall satisfaction with the academic environment'
                    ];
                } else { // Alumni
                    $questions = [
                        'q1' => 'The education received adequately prepared me for my career',
                        'q2' => 'Course content was relevant to real-world applications',
                        'q3' => 'Practical laboratory sessions were beneficial for skill development',
                        'q4' => 'Faculty support and guidance were adequate during my studies',
                        'q5' => 'The institution provided good placement opportunities',
                        'q6' => 'Extracurricular activities contributed to overall development',
                        'q7' => 'The alumni network is active and supportive',
                        'q8' => 'I would recommend this institution to prospective students',
                        'q9' => 'Overall satisfaction with my educational experience'
                    ];
                }
                
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
                    <div class="question-text">Additional Comments or Suggestions</div>
                    <textarea name="suggestions" placeholder="Enter your comments or suggestions here..."></textarea>
                </div>
            </div>
            
            <button type="submit" class="btn">SUBMIT FEEDBACK</button>
        </form>
        
        <div class="nav-links">
            <a href="index.php">← Back to Personal Details</a>
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