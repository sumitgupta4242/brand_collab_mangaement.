<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Query the admins table for the given username
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
    $stmt->execute([$username]);
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password using password_verify
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin'] = true;
        $_SESSION['admin_username'] = $admin['username'];
        header("Location: admin.php"); // Redirect to admin panel
        exit();
    } else {
        $error = "Invalid admin credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Login - UGC Marketplace</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
        background-color: #f0f8ff;
    }
    .modal-header {
        background-color: #dc3545;
        color: white;
    }
    .modal-footer {
        background-color: #f7f7f7;
    }
    .modal-body a {
        color: #dc3545;
        text-decoration: none;
        font-weight: bold;
    }
    .modal-body a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
  <!-- Admin Login Modal -->
  <div class="modal fade" id="adminLoginModal" tabindex="-1" aria-labelledby="adminLoginModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
         <div class="modal-header">
             <h5 class="modal-title" id="adminLoginModalLabel">Admin Login</h5>
             <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='index.php';"></button>
         </div>
         <div class="modal-body">
             <?php if(isset($error)): ?>
             <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
             <?php endif; ?>
             <form action="" method="POST">
                 <div class="mb-3">
                     <label for="username" class="form-label">Username</label>
                     <input type="text" name="username" id="username" class="form-control" required>
                 </div>
                 <div class="mb-3">
                     <label for="password" class="form-label">Password</label>
                     <input type="password" name="password" id="password" class="form-control" required>
                 </div>
                 <button type="submit" class="btn btn-danger w-100">Login</button>
             </form>
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
      // Automatically show the admin login modal when the page loads
      var adminLoginModal = new bootstrap.Modal(document.getElementById('adminLoginModal'), {
          backdrop: 'static'
      });
      adminLoginModal.show();
  </script>
</body>
</html>
