<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve and sanitize login data
  $email = trim($_POST['email']);
  $password = trim($_POST['password']);

  // Query the user from the database
  $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
  $stmt->execute([$email]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Verify the password and check email verification
  if ($user && password_verify($password, $user['password'])) {
    if ($user['is_verified'] == 1) {
      $_SESSION['name'] = $user['name'];
      $_SESSION['email'] = $user['email'];
      header("Location: welcome.php");
      exit();
    } else {
      $error = "Please verify your email first.";
    }
  } else {
    $error = "Invalid email or password.";
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login - UGC Marketplace</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
      background-color: #f0f8ff;
    }

    .modal-header {
      background-color: #28a745;
      color: white;
    }

    .modal-footer {
      background-color: #f7f7f7;
    }

    .modal-body a {
      color: #28a745;
      text-decoration: none;
      font-weight: bold;
    }

    .modal-body a:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <!-- Login Modal -->
  <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="loginModalLabel">Login</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
            onclick="window.location.href='index.php';"></button>
        </div>
        <div class="modal-body">
          <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
          <?php endif; ?>
          <!-- Existing login form code -->
          <form action="" method="POST">
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" name="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" name="password" id="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-success w-100">Login</button>
          </form>
          <hr>
          <!-- Google Login Button -->
          <a href="google-login.php" class="btn btn-danger w-100">Login with Google</a>
          <hr>
          <p class="text-center">Don't have an account? <a href="register.php">Register</a></p>

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" onclick="window.location.href='index.php';">Close</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Automatically show the login modal when the page loads
    var loginModal = new bootstrap.Modal(document.getElementById('loginModal'), {
      backdrop: 'static'
    });
    loginModal.show();
  </script>
</body>

</html>