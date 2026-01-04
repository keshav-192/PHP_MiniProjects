<?php
session_start();
if (!empty($_SESSION['uid'])) {
  header('Location: home.php'); exit;
}

$flash_error = $_SESSION['flash'] ?? '';
$flash_success = $_SESSION['flash_success'] ?? '';
unset($_SESSION['flash'], $_SESSION['flash_success']);

$username_error = '';
$email_error = '';

if ($flash_error === 'Username already exists, try another.') {
  $username_error = $flash_error;
  $flash_error = '';
} elseif ($flash_error === 'Email already registered.') {
  $email_error = $flash_error;
  $flash_error = '';
}
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Create Account</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <div class="card">
    <div class="header">
      <span class="badge">New here?</span>
      <h1>Create your account</h1>
      <p>Sign up with your name, email and a strong password.</p>
    </div>

    <?php if ($flash_error): ?>
      <div class="error-text" style="margin-bottom:10px;">
        <?php echo htmlspecialchars($flash_error, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <?php if ($flash_success): ?>
      <div class="success-text" style="margin-bottom:10px;">
        <?php echo htmlspecialchars($flash_success, ENT_QUOTES, 'UTF-8'); ?>
      </div>
    <?php endif; ?>

    <form id="regForm" class="form" method="post" action="register_process.php" novalidate>
      <div class="row">
        <label class="label" for="name">Full name</label>
        <input class="input" id="name" name="name" type="text" required minlength="3" maxlength="100" pattern="^[A-Za-z][A-Za-z\s]{2,99}$" placeholder="enter name">
        <div class="error-text" id="msg-name"></div>
      </div>

      <div class="row">
        <label class="label" for="username">Username</label>
        <input class="input" id="username" name="username" type="text" required minlength="3" maxlength="32" pattern="^[a-zA-Z0-9_]{3,32}$" placeholder="xyz_123">
        <div class="error-text" id="msg-username"><?php if ($username_error) { echo htmlspecialchars($username_error, ENT_QUOTES, 'UTF-8'); } ?></div>
      </div>

      <div class="row">
        <label class="label" for="email">Email</label>
        <input class="input" id="email" name="email" type="email" required inputmode="email" maxlength="255" pattern="^[A-Za-z0-9._%+-]+@[A-Za-z0-9.-]+\.[A-Za-z]{2,}$" placeholder="xyz@gmail.com">
        <div class="error-text" id="msg-email"><?php if ($email_error) { echo htmlspecialchars($email_error, ENT_QUOTES, 'UTF-8'); } ?></div>
      </div>

      <div class="row">
        <label class="label" for="password">Password</label>
        <input class="input" id="password" name="password" type="password" required minlength="8" autocomplete="new-password" placeholder="enter password">
        <div class="error-text" id="msg-password"></div>
      </div>

      <div class="row">
        <label class="label" for="confirm">Confirm password</label>
        <input class="input" id="confirm" name="confirm" type="password" required autocomplete="new-password" placeholder="re-enter password">
        <div class="error-text" id="msg-confirm"></div>
      </div>

      <div class="actions">
        <button class="btn" type="submit">Create account</button>
        <div class="meta">Already have an account? <a href="login.php">Sign in</a></div>
      </div>
    </form>
  </div>

<script>
const f = document.getElementById('regForm');
const nameI = document.getElementById('name');
const userI = document.getElementById('username');
const emailI = document.getElementById('email');
const passI = document.getElementById('password');
const confI = document.getElementById('confirm');

function setError(input, msgId, message) {
  input.classList.add('error');
  input.classList.remove('success');
  const el = document.getElementById(msgId);
  if (!el) return;
  el.classList.add('error-text');
  el.classList.remove('success-text');
  el.textContent = message;
}
function setSuccess(input, msgId, message) {
  input.classList.remove('error');
  input.classList.add('success');
  const el = document.getElementById(msgId);
  if (!el) return;
  el.classList.remove('error-text');
  el.classList.add('success-text');
  el.textContent = message;
}

// Password complexity is validated in validatePassword; no visual strength bar.

function validateName() {
  if (nameI.value.trim() === '' || !nameI.checkValidity()) {
    setError(nameI, 'msg-name', 'Enter a valid name (letters and spaces only).');
    return false;
  }
  setSuccess(nameI, 'msg-name', 'Valid');
  return true;
}

function validateUsername() {
  if (userI.value.trim() === '' || !userI.checkValidity()) {
    setError(userI, 'msg-username', 'Username must be 3-32 characters (letters, numbers, _).');
    return false;
  }
  return true;
}

function validateEmail() {
  if (emailI.value.trim() === '' || !emailI.checkValidity()) {
    setError(emailI, 'msg-email', 'Enter a valid email address.');
    return false;
  }

  const domain = emailI.value.split('@')[1]?.toLowerCase() || '';
  const allowedProviders = [
    'gmail.com',
    'yahoo.com',
    'outlook.com',
    'hotmail.com',
    'live.com',
    'icloud.com',
    'proton.me'
  ];
  const institutionalPattern = /\.(ac|edu)(\.[a-z.]+)?$/i; // e.g. college.ac.in, univ.edu, univ.edu.in

  const isAllowed = allowedProviders.includes(domain) || institutionalPattern.test(domain);
  if (!isAllowed) {
    setError(emailI, 'msg-email', 'Use a college email or a popular provider (Gmail, Outlook, etc.).');
    return false;
  }

  // Basic checks passed; availability will be checked asynchronously
  return true;
}

function validatePassword() {
  const pw = passI.value;
  const complex = /[a-z]/.test(pw) && /[A-Z]/.test(pw) && /\d/.test(pw) && /[^A-Za-z0-9]/.test(pw) && pw.length >= 8;
  if (!complex) {
    setError(passI, 'msg-password', 'Use upper, lower, number and symbol with 8+ characters.');
    return false;
  }
  setSuccess(passI, 'msg-password', 'Strong password.');
  return true;
}

function validateConfirm() {
  const pw = passI.value;
  if (confI.value === '' || confI.value !== pw) {
    setError(confI, 'msg-confirm', 'Passwords do not match.');
    return false;
  }
  setSuccess(confI, 'msg-confirm', 'Passwords match.');
  return true;
}

async function checkUsernameAvailability() {
  if (!validateUsername()) return;
  const value = userI.value.trim();
  if (!value) return;
  try {
    const res = await fetch('check_username.php?username=' + encodeURIComponent(value));
    if (!res.ok) return;
    const data = await res.json();
    if (!data.ok) return;
    if (data.available) {
      setSuccess(userI, 'msg-username', 'Valid');
    } else {
      setError(userI, 'msg-username', 'Already used or taken.');
    }
  } catch (e) {
  }
}

async function checkEmailAvailability() {
  if (!validateEmail()) return;
  const value = emailI.value.trim();
  if (!value) return;
  try {
    const res = await fetch('check_email.php?email=' + encodeURIComponent(value));
    if (!res.ok) return;
    const data = await res.json();
    if (!data.ok) return;
    if (data.available) {
      setSuccess(emailI, 'msg-email', 'Valid');
    } else {
      setError(emailI, 'msg-email', 'Already used or taken.');
    }
  } catch (e) {
  }
}

nameI.addEventListener('blur', validateName);
userI.addEventListener('blur', validateUsername);
userI.addEventListener('blur', () => { checkUsernameAvailability(); });
emailI.addEventListener('blur', validateEmail);
emailI.addEventListener('blur', () => { checkEmailAvailability(); });
passI.addEventListener('blur', validatePassword);
confI.addEventListener('blur', validateConfirm);

f.addEventListener('submit', (e) => {
  let ok = true;

  if (!validateName()) ok = false;
  if (!validateUsername()) ok = false;
  if (!validateEmail()) ok = false;
  if (!validatePassword()) ok = false;
  if (!validateConfirm()) ok = false;

  if (!ok) e.preventDefault();
});
</script>
</body>
</html>
