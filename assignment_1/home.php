<?php
session_start();
if (empty($_SESSION['uid'])) {
  header('Location: login.php'); exit;
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Home</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card">
    <div class="header">
      <span class="badge">Dashboard</span>
      <h1>Hello, <?php echo htmlspecialchars($_SESSION['uname']); ?></h1>
      <p>You are logged in successfully.</p>
    </div>
    <div class="actions">
      <a class="btn" href="logout.php">Logout</a>
    </div>
  </div>
</body>
</html>
