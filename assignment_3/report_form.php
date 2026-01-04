<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback Reports</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; }
        .container { max-width: 1200px; margin: 30px auto; padding: 20px; }
        .header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid #eaeaea; }
        .header h1 { color: #2c3e50; margin-bottom: 10px; }
        .search-form { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); margin-bottom: 30px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 5px; font-weight: 600; color: #2c3e50; }
        input[type="text"], input[type="email"], select { width: 100%; padding: 10px 15px; border: 1px solid #ddd; border-radius: 5px; font-size: 16px; }
        input:focus, select:focus { border-color: #3498db; outline: none; box-shadow: 0 0 5px rgba(52, 152, 219, 0.3); }
        .btn { display: block; width: 100%; padding: 12px; background-color: #3498db; color: white; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; transition: background-color 0.3s; }
        .btn:hover { background-color: #2980b9; }
        .report-container { background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); margin-bottom: 20px; }
        .report-header { text-align: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 2px solid #eaeaea; }
        .report-title { font-size: 24px; margin-bottom: 10px; color: #2c3e50; }
        .stakeholder-info { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px; margin-bottom: 30px; }
        .info-item { padding: 15px; background: #f8f9fa; border-radius: 5px; border-left: 4px solid #3498db; }
        .info-label { font-weight: 600; color: #2c3e50; margin-bottom: 5px; }
        .feedback-item { margin-bottom: 20px; padding-bottom: 15px; border-bottom: 1px solid #eee; }
        .question-text { font-weight: 500; margin-bottom: 10px; }
        .rating { display: inline-block; padding: 5px 10px; background: #3498db; color: white; border-radius: 20px; font-weight: 600; }
        .suggestions { background: #f8f9fa; padding: 15px; border-radius: 5px; margin-top: 20px; }
        .nav-links { text-align: center; margin-top: 30px; }
        .nav-links a { color: #3498db; text-decoration: none; padding: 10px 20px; border: 1px solid #3498db; border-radius: 5px; margin: 0 10px; transition: all 0.3s; }
        .nav-links a:hover { background-color: #3498db; color: white; }
        .no-data { text-align: center; padding: 40px; color: #666; }
        .search-type { margin-bottom: 20px; }
        .search-type label { display: inline-block; margin-right: 15px; cursor: pointer; }
        .search-type input { margin-right: 5px; }
        .tab-container { margin-bottom: 20px; }
        .tabs { display: flex; border-bottom: 2px solid #3498db; margin-bottom: 20px; }
        .tab { padding: 10px 20px; cursor: pointer; border: 1px solid #ddd; border-bottom: none; border-radius: 5px 5px 0 0; margin-right: 5px; background: #f8f9fa; transition: all 0.3s; }
        .tab.active { background: #3498db; color: white; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .summary-stats { display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 20px; margin-bottom: 30px; }
        .stat-card { background: white; padding: 20px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
        .stat-number { font-size: 2em; font-weight: bold; color: #3498db; margin-bottom: 10px; }
        .stat-label { color: #666; font-size: 0.9em; }
        .all-feedback-list { max-height: 500px; overflow-y: auto; }
        .feedback-entry { background: white; padding: 15px; margin-bottom: 10px; border-radius: 5px; box-shadow: 0 0 5px rgba(0,0,0,0.1); cursor: pointer; transition: all 0.3s; }
        .feedback-entry:hover { background: #f8f9fa; }
        .feedback-header { display: flex; justify-content: between; align-items: center; margin-bottom: 10px; }
        .feedback-name { font-weight: bold; color: #2c3e50; }
        .feedback-type { background: #3498db; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.8em; }
        .feedback-date { color: #666; font-size: 0.9em; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Feedback Reports</h1>
            <p>View feedback from all stakeholders</p>
        </div>

        <div class="tab-container">
            <div class="tabs">
                <div class="tab active" onclick="showTab('search')">Search Feedback</div>
                <div class="tab" onclick="showTab('all')">View All Feedback</div>
                <div class="tab" onclick="showTab('summary')">Summary Reports</div>
            </div>
            
            <!-- Search Tab -->
            <div id="search" class="tab-content active">
                <div class="search-form">
                    <form id="reportForm" method="GET" action="">
                        <div class="form-group">
                            <label>Search By:</label>
                            <div class="search-type">
                                <label>
                                    <input type="radio" name="search_type" value="student" <?php echo (!isset($_GET['search_type']) || $_GET['search_type'] == 'student') ? 'checked' : ''; ?> onchange="toggleSearchFields()"> Student Register No
                                </label>
                                <label>
                                    <input type="radio" name="search_type" value="email" <?php echo (isset($_GET['search_type']) && $_GET['search_type'] == 'email') ? 'checked' : ''; ?> onchange="toggleSearchFields()"> Email Address
                                </label>
                                <label>
                                    <input type="radio" name="search_type" value="stakeholder" <?php echo (isset($_GET['search_type']) && $_GET['search_type'] == 'stakeholder') ? 'checked' : ''; ?> onchange="toggleSearchFields()"> Stakeholder Type
                                </label>
                                <label>
                                    <input type="radio" name="search_type" value="name" <?php echo (isset($_GET['search_type']) && $_GET['search_type'] == 'name') ? 'checked' : ''; ?> onchange="toggleSearchFields()"> Name
                                </label>
                            </div>
                        </div>

                        <div class="form-group" id="studentField">
                            <label for="register_no">Register No</label>
                            <input type="text" id="register_no" name="register_no" placeholder="Enter student register number" value="<?php echo isset($_GET['register_no']) ? htmlspecialchars($_GET['register_no']) : ''; ?>">
                        </div>

                        <div class="form-group" id="emailField" style="display: none;">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" placeholder="Enter email address" value="<?php echo isset($_GET['email']) ? htmlspecialchars($_GET['email']) : ''; ?>">
                        </div>

                        <div class="form-group" id="stakeholderField" style="display: none;">
                            <label for="stakeholder_type">Stakeholder Type</label>
                            <select id="stakeholder_type" name="stakeholder_type">
                                <option value="">Select Type</option>
                                <option value="Student" <?php echo (isset($_GET['stakeholder_type']) && $_GET['stakeholder_type'] == 'Student') ? 'selected' : ''; ?>>Student</option>
                                <option value="Faculty" <?php echo (isset($_GET['stakeholder_type']) && $_GET['stakeholder_type'] == 'Faculty') ? 'selected' : ''; ?>>Faculty</option>
                                <option value="Alumni" <?php echo (isset($_GET['stakeholder_type']) && $_GET['stakeholder_type'] == 'Alumni') ? 'selected' : ''; ?>>Alumni</option>
                            </select>
                        </div>

                        <div class="form-group" id="nameField" style="display: none;">
                            <label for="name">Name</label>
                            <input type="text" id="name" name="name" placeholder="Enter full name or part of name" value="<?php echo isset($_GET['name']) ? htmlspecialchars($_GET['name']) : ''; ?>">
                        </div>

                        <button type="submit" class="btn">SEARCH FEEDBACK</button>
                    </form>
                </div>

                <?php if (isset($_GET['search_type'])): ?>
                    <?php
                    $search_type = $_GET['search_type'];
                    
                    if ($search_type === 'student' && !empty($_GET['register_no'])) {
                        $register_no = trim($_GET['register_no']);
                        displayStudentFeedback($conn, $register_no);
                    } elseif ($search_type === 'email' && !empty($_GET['email'])) {
                        $email = trim($_GET['email']);
                        displayFeedbackByEmail($conn, $email);
                    } elseif ($search_type === 'stakeholder' && !empty($_GET['stakeholder_type'])) {
                        $stakeholder_type = $_GET['stakeholder_type'];
                        displayFeedbackByStakeholderType($conn, $stakeholder_type);
                    } elseif ($search_type === 'name' && !empty($_GET['name'])) {
                        $name = trim($_GET['name']);
                        displayFeedbackByName($conn, $name);
                    } else {
                        echo '<div class="no-data">Please enter search criteria</div>';
                    }
                    ?>
                <?php endif; ?>
            </div>

            <!-- All Feedback Tab -->
            <div id="all" class="tab-content">
                <div class="search-form">
                    <h3>All Feedback Submissions</h3>
                    <p>Click on any entry to view detailed feedback</p>
                    <?php displayAllFeedback($conn); ?>
                </div>
            </div>

            <!-- Summary Tab -->
            <div id="summary" class="tab-content">
                <div class="search-form">
                    <h3>Feedback Summary Reports</h3>
                    <?php displayFeedbackSummary($conn); ?>
                </div>
            </div>
        </div>
        
        <div class="nav-links">
            <a href="index.php">Submit New Feedback</a>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all tabs
            document.querySelectorAll('.tab').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName).classList.add('active');
            
            // Add active class to clicked tab
            event.target.classList.add('active');
        }

        function toggleSearchFields() {
            const searchType = document.querySelector('input[name="search_type"]:checked').value;
            
            // Hide all fields first
            document.getElementById('studentField').style.display = 'none';
            document.getElementById('emailField').style.display = 'none';
            document.getElementById('stakeholderField').style.display = 'none';
            document.getElementById('nameField').style.display = 'none';
            
            // Show relevant field
            if (searchType === 'student') {
                document.getElementById('studentField').style.display = 'block';
            } else if (searchType === 'email') {
                document.getElementById('emailField').style.display = 'block';
            } else if (searchType === 'stakeholder') {
                document.getElementById('stakeholderField').style.display = 'block';
            } else if (searchType === 'name') {
                document.getElementById('nameField').style.display = 'block';
            }
        }

        function viewFeedbackDetails(stakeholderId, feedbackId) {
            // Redirect to detailed view with parameters
            window.location.href = `feedback_detail.php?stakeholder_id=${stakeholderId}&feedback_id=${feedbackId}`;
        }

        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            toggleSearchFields();
        });
    </script>
</body>
</html>

<?php
// Function to display student feedback
function displayStudentFeedback($conn, $register_no) {
    $sql = "SELECT s.register_no, s.branch, s.programme, s.regulation, 
                   st.firstname, st.email, st.academic_year, st.stakeholder_type,
                   f.id as feedback_id, f.q1, f.q2, f.q3, f.q4, f.q5, f.q6, f.q7, f.q8, f.q9, f.suggestions,
                   f.submitted_at
            FROM students s
            JOIN stakeholders st ON s.stakeholder_id = st.id
            JOIN feedback_responses f ON (s.id = f.student_id OR st.id = f.stakeholder_id)
            WHERE s.register_no = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $register_no);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        displayFeedbackReport($result);
    } else {
        echo '<div class="no-data">No feedback found for register number: ' . htmlspecialchars($register_no) . '</div>';
    }
    $stmt->close();
}

// Function to display feedback by email
function displayFeedbackByEmail($conn, $email) {
    $sql = "SELECT st.id as stakeholder_id, st.firstname, st.email, st.stakeholder_type, st.academic_year,
                   s.register_no, s.branch, s.programme, s.regulation,
                   f.id as feedback_id, f.q1, f.q2, f.q3, f.q4, f.q5, f.q6, f.q7, f.q8, f.q9, f.suggestions,
                   f.submitted_at
            FROM stakeholders st
            LEFT JOIN students s ON st.id = s.stakeholder_id
            JOIN feedback_responses f ON st.id = f.stakeholder_id
            WHERE st.email = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        displayFeedbackReport($result);
    } else {
        echo '<div class="no-data">No feedback found for email: ' . htmlspecialchars($email) . '</div>';
    }
    $stmt->close();
}

// Function to display feedback by stakeholder type
function displayFeedbackByStakeholderType($conn, $stakeholder_type) {
    $sql = "SELECT st.id as stakeholder_id, st.firstname, st.email, st.stakeholder_type, st.academic_year,
                   s.register_no, s.branch, s.programme, s.regulation,
                   f.id as feedback_id, f.q1, f.q2, f.q3, f.q4, f.q5, f.q6, f.q7, f.q8, f.q9, f.suggestions,
                   f.submitted_at
            FROM stakeholders st
            LEFT JOIN students s ON st.id = s.stakeholder_id
            JOIN feedback_responses f ON st.id = f.stakeholder_id
            WHERE st.stakeholder_type = ?
            ORDER BY f.submitted_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $stakeholder_type);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo '<h3>All ' . $stakeholder_type . ' Feedback (' . $result->num_rows . ' entries)</h3>';
        while ($feedback = $result->fetch_assoc()) {
            displaySingleFeedbackReport($feedback);
        }
    } else {
        echo '<div class="no-data">No feedback found for stakeholder type: ' . htmlspecialchars($stakeholder_type) . '</div>';
    }
    $stmt->close();
}

// Function to display feedback by name
function displayFeedbackByName($conn, $name) {
    $sql = "SELECT st.id as stakeholder_id, st.firstname, st.email, st.stakeholder_type, st.academic_year,
                   s.register_no, s.branch, s.programme, s.regulation,
                   f.id as feedback_id, f.q1, f.q2, f.q3, f.q4, f.q5, f.q6, f.q7, f.q8, f.q9, f.suggestions,
                   f.submitted_at
            FROM stakeholders st
            LEFT JOIN students s ON st.id = s.stakeholder_id
            JOIN feedback_responses f ON st.id = f.stakeholder_id
            WHERE st.firstname LIKE ?
            ORDER BY f.submitted_at DESC";
    
    $search_name = "%" . $name . "%";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $search_name);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        echo '<h3>Feedback for names containing "' . htmlspecialchars($name) . '" (' . $result->num_rows . ' entries)</h3>';
        while ($feedback = $result->fetch_assoc()) {
            displaySingleFeedbackReport($feedback);
        }
    } else {
        echo '<div class="no-data">No feedback found for name: ' . htmlspecialchars($name) . '</div>';
    }
    $stmt->close();
}

// Function to display all feedback
function displayAllFeedback($conn) {
    $sql = "SELECT st.id as stakeholder_id, st.firstname, st.email, st.stakeholder_type, st.academic_year,
                   s.register_no, s.branch, s.programme, s.regulation,
                   f.id as feedback_id, f.q1, f.q2, f.q3, f.q4, f.q5, f.q6, f.q7, f.q8, f.q9,
                   f.submitted_at
            FROM stakeholders st
            LEFT JOIN students s ON st.id = s.stakeholder_id
            JOIN feedback_responses f ON st.id = f.stakeholder_id
            ORDER BY f.submitted_at DESC
            LIMIT 50";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        echo '<div class="all-feedback-list">';
        while ($feedback = $result->fetch_assoc()) {
            $avg_rating = calculateAverageRating($feedback);
            echo '<div class="feedback-entry" onclick="viewFeedbackDetails(' . $feedback['stakeholder_id'] . ', ' . $feedback['feedback_id'] . ')">';
            echo '<div class="feedback-header">';
            echo '<div>';
            echo '<span class="feedback-name">' . htmlspecialchars($feedback['firstname']) . '</span>';
            echo ' <span class="feedback-type">' . $feedback['stakeholder_type'] . '</span>';
            if (!empty($feedback['register_no'])) {
                echo ' <span style="color: #666;">(' . $feedback['register_no'] . ')</span>';
            }
            echo '</div>';
            echo '<div class="rating">Avg: ' . number_format($avg_rating, 1) . '/5</div>';
            echo '</div>';
            echo '<div class="feedback-date">Submitted: ' . date('M j, Y g:i A', strtotime($feedback['submitted_at'])) . '</div>';
            echo '</div>';
        }
        echo '</div>';
    } else {
        echo '<div class="no-data">No feedback submissions found.</div>';
    }
}

// Function to display feedback summary
function displayFeedbackSummary($conn) {
    // Overall statistics
    $sql = "SELECT stakeholder_type, 
                   COUNT(*) as total_responses,
                   AVG((q1+q2+q3+q4+q5+q6+q7+q8+q9)/9.0) as avg_rating
            FROM feedback_responses 
            GROUP BY stakeholder_type";
    
    $result = $conn->query($sql);
    
    echo '<div class="summary-stats">';
    
    // Total feedback count
    $total_sql = "SELECT COUNT(*) as total FROM feedback_responses";
    $total_result = $conn->query($total_sql);
    $total = $total_result->fetch_assoc()['total'];
    
    echo '<div class="stat-card">';
    echo '<div class="stat-number">' . $total . '</div>';
    echo '<div class="stat-label">Total Feedback Submissions</div>';
    echo '</div>';
    
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="stat-card">';
            echo '<div class="stat-number">' . $row['total_responses'] . '</div>';
            echo '<div class="stat-label">' . $row['stakeholder_type'] . ' Responses</div>';
            echo '<div class="stat-label">Avg Rating: ' . number_format($row['avg_rating'], 2) . '/5</div>';
            echo '</div>';
        }
    }
    
    // Recent activity
    $recent_sql = "SELECT COUNT(*) as recent FROM feedback_responses WHERE submitted_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    $recent_result = $conn->query($recent_sql);
    $recent = $recent_result->fetch_assoc()['recent'];
    
    echo '<div class="stat-card">';
    echo '<div class="stat-number">' . $recent . '</div>';
    echo '<div class="stat-label">Submissions This Week</div>';
    echo '</div>';
    
    echo '</div>';
    
    // Recent feedback list
    echo '<h4>Recent Feedback Submissions</h4>';
    $recent_feedback_sql = "SELECT st.firstname, st.stakeholder_type, f.submitted_at 
                           FROM feedback_responses f 
                           JOIN stakeholders st ON f.stakeholder_id = st.id 
                           ORDER BY f.submitted_at DESC 
                           LIMIT 10";
    $recent_result = $conn->query($recent_feedback_sql);
    
    if ($recent_result->num_rows > 0) {
        echo '<div class="all-feedback-list">';
        while ($row = $recent_result->fetch_assoc()) {
            echo '<div class="feedback-entry">';
            echo '<div class="feedback-name">' . htmlspecialchars($row['firstname']) . '</div>';
            echo '<div style="display: flex; justify-content: space-between; align-items: center;">';
            echo '<span class="feedback-type">' . $row['stakeholder_type'] . '</span>';
            echo '<span class="feedback-date">' . date('M j, Y g:i A', strtotime($row['submitted_at'])) . '</span>';
            echo '</div>';
            echo '</div>';
        }
        echo '</div>';
    }
}

// Function to display feedback report for multiple results
function displayFeedbackReport($result) {
    if ($result->num_rows > 0) {
        while ($feedback = $result->fetch_assoc()) {
            displaySingleFeedbackReport($feedback);
        }
    }
}

// Function to display single feedback report
function displaySingleFeedbackReport($feedback) {
    $questions = getQuestionsByStakeholderType($feedback['stakeholder_type']);
    ?>
    <div class="report-container">
        <div class="report-header">
            <div class="report-title"><?php echo $feedback['stakeholder_type']; ?> Feedback Report</div>
            <p>Academic Year: <?php echo $feedback['academic_year']; ?></p>
        </div>
        
        <div class="stakeholder-info">
            <div class="info-item">
                <div class="info-label">Name</div>
                <div><?php echo htmlspecialchars($feedback['firstname']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Email</div>
                <div><?php echo htmlspecialchars($feedback['email']); ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Stakeholder Type</div>
                <div><?php echo $feedback['stakeholder_type']; ?></div>
            </div>
            <?php if (!empty($feedback['register_no'])): ?>
            <div class="info-item">
                <div class="info-label">Register No</div>
                <div><?php echo $feedback['register_no']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Branch</div>
                <div><?php echo $feedback['branch']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Programme</div>
                <div><?php echo $feedback['programme']; ?></div>
            </div>
            <div class="info-item">
                <div class="info-label">Regulation</div>
                <div><?php echo $feedback['regulation']; ?></div>
            </div>
            <?php endif; ?>
            <div class="info-item">
                <div class="info-label">Submitted On</div>
                <div><?php echo date('d M Y, h:i A', strtotime($feedback['submitted_at'])); ?></div>
            </div>
        </div>
        
        <h3 style="margin-bottom: 20px; color: #2c3e50;">Feedback Responses</h3>
        
        <?php 
        $total_rating = 0;
        $question_count = 0;
        
        foreach ($questions as $key => $question) {
            if (isset($feedback[$key]) && $feedback[$key] > 0) {
                $rating = $feedback[$key];
                $total_rating += $rating;
                $question_count++;
                ?>
                <div class="feedback-item">
                    <div class="question-text"><?php echo $question; ?></div>
                    <div class="rating">Rating: <?php echo $rating; ?>/5</div>
                </div>
                <?php
            }
        }
        
        if ($question_count > 0) {
            $average_rating = $total_rating / $question_count;
            ?>
            <div class="info-item" style="margin-top: 20px;">
                <div class="info-label">Average Rating</div>
                <div style="font-size: 18px; font-weight: 600; color: #3498db;">
                    <?php echo number_format($average_rating, 2); ?> / 5
                </div>
            </div>
            <?php
        }
        ?>
        
        <?php if (!empty($feedback['suggestions'])): ?>
        <div class="suggestions">
            <div class="info-label">Additional Comments/Suggestions</div>
            <p><?php echo nl2br(htmlspecialchars($feedback['suggestions'])); ?></p>
        </div>
        <?php endif; ?>
    </div>
    <?php
}

// Helper function to get questions by stakeholder type
function getQuestionsByStakeholderType($type) {
    $questions = [
        'Student' => [
            'q1' => 'Courses Contents are designed to enable Problem Solving Skills and Core competencies',
            'q2' => 'Courses placed in the curriculum serves the needs of both advanced and slow learners',
            'q3' => 'Contact Hour Distribution among the various Course Components (LTP) is Satisfiable',
            'q4' => 'Composition of Basic Sciences, Engineering, Humanities and Management Courses is a right mix and satisfiable',
            'q5' => 'Laboratory sessions are sufficient to improve the technical skills of students',
            'q6' => 'Inclusion of Minor Project/ Mini Projects improved the technical competency and leadership skills among the students',
            'q7' => 'Electives have enabled the passion to learn new technologies in emerging areas',
            'q8' => 'Curriculum is providing opportunity towards Self learning to realize the expectations',
            'q9' => 'Overall satisfaction with the curriculum structure and implementation'
        ],
        'Faculty' => [
            'q1' => 'The curriculum adequately prepares students for industry requirements',
            'q2' => 'Course content is up-to-date with current industry trends',
            'q3' => 'Laboratory facilities and equipment are sufficient for practical learning',
            'q4' => 'Student engagement and participation in classes is satisfactory',
            'q5' => 'The academic calendar and schedule are well planned',
            'q6' => 'Support from administration for teaching activities is adequate',
            'q7' => 'Opportunities for faculty development and training are sufficient',
            'q8' => 'Research opportunities and infrastructure are satisfactory',
            'q9' => 'Overall satisfaction with the academic environment'
        ],
        'Alumni' => [
            'q1' => 'The education received adequately prepared me for my career',
            'q2' => 'Course content was relevant to real-world applications',
            'q3' => 'Practical laboratory sessions were beneficial for skill development',
            'q4' => 'Faculty support and guidance were adequate during my studies',
            'q5' => 'The institution provided good placement opportunities',
            'q6' => 'Extracurricular activities contributed to overall development',
            'q7' => 'The alumni network is active and supportive',
            'q8' => 'I would recommend this institution to prospective students',
            'q9' => 'Overall satisfaction with my educational experience'
        ]
    ];
    
    return $questions[$type] ?? $questions['Student'];
}

// Helper function to calculate average rating
function calculateAverageRating($feedback) {
    $total = 0;
    $count = 0;
    for ($i = 1; $i <= 9; $i++) {
        if (isset($feedback['q' . $i]) && $feedback['q' . $i] > 0) {
            $total += $feedback['q' . $i];
            $count++;
        }
    }
    return $count > 0 ? $total / $count : 0;
}

$conn->close();
?>