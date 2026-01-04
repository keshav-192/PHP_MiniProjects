<?php include 'config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: #f5f7fa; color: #333; line-height: 1.6; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .container { max-width: 600px; text-align: center; padding: 40px; background: white; border-radius: 10px; box-shadow: 0 0 20px rgba(0, 0, 0, 0.1); }
        .success-icon { font-size: 80px; color: #27ae60; margin-bottom: 20px; }
        h1 { color: #2c3e50; margin-bottom: 20px; }
        p { margin-bottom: 30px; font-size: 18px; color: #666; }
        .btn { display: inline-block; padding: 12px 30px; background-color: #3498db; color: white; text-decoration: none; border-radius: 5px; font-size: 16px; transition: background-color 0.3s; margin: 0 10px; }
        .btn:hover { background-color: #2980b9; }
        .btn-secondary { background-color: #95a5a6; }
        .btn-secondary:hover { background-color: #7f8c8d; }
    </style>
</head>
<body>
    <div class="container">
        <div class="success-icon">✓</div>
        <h1>Thank You!</h1>
        <p>Your feedback has been successfully submitted. We appreciate your time and valuable input.</p>
        <div>
            <a href="index.php" class="btn">Submit Another Feedback</a>
            <a href="report_form.php" class="btn btn-secondary">View Reports</a>
        </div>
    </div>
</body>
</html>