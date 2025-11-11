<?php
session_start();
include('config.php');

// Load PHPMailer classes and namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require('php mailer/Exception.php');
require('php mailer/PHPMailer.php');
require('php mailer/SMTP.php');

// Function to send verification email
function sendVerificationEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sumitrichan@gmail.com'; // Your Gmail
        $mail->Password   = 'syll jdru rtam jdap';      // Your Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient settings
        $mail->setFrom('sumitrichan@gmail.com', 'UGC MARKET');
        $mail->addAddress($email); // Recipient's email

        // Email content
        $mail->isHTML(true);
        $mail->Subject = 'Verify Your Email - UGC Marketplace';
        $verificationLink = "http://localhost/ugc/verify.php?token=$token"; // Change to your domain in production
        $mail->Body    = "<p>Click the link below to verify your email:</p>
                          <p><a href='$verificationLink'>$verificationLink</a></p>
                          <p>If you did not request this, please ignore this email.</p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $mail->ErrorInfo);
        return false;
    }
}

// Initialize messages
$success_message = "";
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $name     = trim($_POST['name']);
    $mobile   = trim($_POST['mobile']);
    $email    = trim($_POST['email']);
    $password = trim($_POST['password']);
    $verification_token = bin2hex(random_bytes(32)); // Generate a unique token

    // Check if the email is already registered
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->rowCount() > 0) {
        $error = "Email already registered. Please use a different email.";
    } else {
        // Hash the password before saving it
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert new user with the verification token (and is_verified = 0)
        $stmt = $pdo->prepare("INSERT INTO users (name, mobile, email, password, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, 0)");
        if ($stmt->execute([$name, $mobile, $email, $hashedPassword, $verification_token])) {
            // Send verification email
            if (sendVerificationEmail($email, $verification_token)) {
                $success_message = "Registration successful! Verification email sent successfully.";
            } else {
                $success_message = "Registration successful! But failed to send verification email.";
            }
        } else {
            $error = "Registration failed. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Register - UGC Marketplace</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
        background-color: #f0f8ff;
    }
    .modal-header {
        background-color: #007bff;
        color: white;
    }
    .modal-footer {
        background-color: #f7f7f7;
    }
    .modal-body a {
        color: #007bff;
        text-decoration: none;
        font-weight: bold;
    }
    .modal-body a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
  <?php if (!empty($success_message)): ?>
    <!-- Display success message if registration is successful -->
    <div class="container mt-5">
      <div class="alert alert-success text-center">
        <?php echo htmlspecialchars($success_message); ?>
      </div>
      <div class="text-center">
        <a href="login.php" class="btn btn-primary">Go to Login</a>
      </div>
    </div>
  <?php else: ?>
    <!-- Registration Modal -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="registerModalLabel">Register</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='index.php';"></button>
          </div>
          <div class="modal-body">
            <?php if (!empty($error)): ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <form action="" method="POST">
              <div class="mb-3">
                <label for="name" class="form-label">Name</label>
                <input type="text" name="name" id="name" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="mobile" class="form-label">Mobile No</label>
                <input type="text" name="mobile" id="mobile" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" required>
              </div>
              <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control" required>
              </div>
              <button type="submit" class="btn btn-primary w-100">Register</button>
            </form>
            <hr>
            <p class="text-center">Already have an account? <a href="login.php">Login</a></p>
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
      // Automatically show the registration modal when the page loads
      var registerModal = new bootstrap.Modal(document.getElementById('registerModal'), { backdrop: 'static' });
      registerModal.show();
    </script>
  <?php endif; ?>
</body>
</html>
