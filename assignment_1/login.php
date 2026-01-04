<?php
session_start();
if (!empty($_SESSION['uid'])) {
  header('Location: home.php'); exit;
}
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$flash_success = $_SESSION['flash_success'] ?? null; unset($_SESSION['flash_success']);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Sign in</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card">
    <div class="header">
      <span class="badge">Welcome back</span>
      <h1>Sign in to your account</h1>
      <p>Use your username and password.</p>
    </div>

    <?php if ($flash): ?>
      <div class="alert" role="alert"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>
    <?php if ($flash_success): ?>
      <div class="alert" role="alert" id="flash-success" style="border-color:#14532d;color:#86efac;">✅ <?php echo htmlspecialchars($flash_success); ?></div>
    <?php endif; ?>

    <form id="loginForm" class="form" method="post" action="login_process.php" novalidate>
      <div class="row">
        <label class="label" for="username">Username</label>
        <input class="input" id="username" name="username" type="text" required minlength="3" maxlength="32" pattern="^[A-Za-z0-9_]{3,32}$" placeholder="enter username">
        <div class="error-text" id="err-user"></div>
      </div>

      <div class="row">
        <label class="label" for="password">Password</label>
        <input class="input" id="password" name="password" type="password" required minlength="8" autocomplete="current-password" placeholder="enter password">
        <div class="error-text" id="err-pass"></div>
      </div>

      <div class="actions">
        <button class="btn" type="submit">Sign in</button>
        <div class="meta">No account? <a href="register.php">Create one</a></div>
      </div>
    </form>
  </div>

<script>
const lf = document.getElementById('loginForm');
const lu = document.getElementById('username');
const lp = document.getElementById('password');
lf.addEventListener('submit', (e) => {
  let ok = true;
  if (!lu.checkValidity()) { lu.classList.add('error'); document.getElementById('err-user').textContent = 'Enter a valid username.'; ok=false; } else { lu.classList.remove('error'); document.getElementById('err-user').textContent = ''; }
  if (!lp.checkValidity()) { lp.classList.add('error'); document.getElementById('err-pass').textContent = 'Enter your password.'; ok=false; } else { lp.classList.remove('error'); document.getElementById('err-pass').textContent = ''; }
  if (!ok) e.preventDefault();
});

// Auto-hide registration success message after 2 seconds
const flashSuccess = document.getElementById('flash-success');
if (flashSuccess) {
  setTimeout(() => {
    flashSuccess.style.display = 'none';
  }, 2000);
}
</script>
</body>
</html>
