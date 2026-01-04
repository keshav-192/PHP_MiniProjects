<?php
include 'config.php';

// Get all users with state and district names, ordered by ID ascending
$sql = "SELECT u.user_id, u.first_name, u.last_name, u.email, u.created_at, 
               s.state_name, d.district_name 
        FROM users u 
        JOIN states s ON u.state_id = s.state_id 
        JOIN districts d ON u.district_id = d.district_id 
        ORDER BY u.user_id ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registered Users</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #eef2ff 0%, #e2e8f0 40%, #ffffff 100%);
            min-height: 100vh;
            padding: 32px 16px;
        }
        
        .container {
            width: 100%;
            max-width: 960px;
            margin: 0 auto;
        }
        
        .header {
            text-align: center;
            margin-bottom: 24px;
            color: #1f2937;
        }
        
        .header h1 {
            font-size: 24px;
            margin-bottom: 4px;
            letter-spacing: 0.02em;
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
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #dbe3f0;
        }
        
        .card-header h2 {
            font-size: 20px;
        }
        
        .btn {
            background: #4c7cff;
            color: #ffffff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.3s, box-shadow 0.3s;
        }
        
        .btn:hover {
            background: #3b66d6;
            box-shadow: 0 4px 10px rgba(0,0,0,0.12);
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f8f9fa;
            font-weight: bold;
        }
        
        tr:hover {
            background-color: #f5f5f5;
        }
        
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #666;
        }
        
        @media (max-width: 768px) {
            .card-header {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
            
            table {
                font-size: 14px;
            }
            
            th, td {
                padding: 8px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Registered Users</h1>
            <p>All registered users in the system</p>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h2>User List</h2>
                <a href="registration.php" class="btn">Back to Registration</a>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>State</th>
                            <th>District</th>
                            <th>Registered On</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $user['user_id']; ?></td>
                                <td><?php echo $user['first_name']; ?></td>
                                <td><?php echo $user['last_name']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['state_name']; ?></td>
                                <td><?php echo $user['district_name']; ?></td>
                                <td><?php echo date('M j, Y g:i A', strtotime($user['created_at'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="empty-state">
                    <p>No users have registered yet.</p>
                    <a href="registration.php" class="btn">Register First User</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>

<?php $conn->close(); ?>