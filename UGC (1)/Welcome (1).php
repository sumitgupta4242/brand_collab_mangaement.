<?php
session_start();
include('config.php');

$isLoggedIn = false;
$userName = "";
$userEmail = "";

if (isset($_SESSION['name'])) {
    $isLoggedIn = true;
    $userName = $_SESSION['name'];
}
if (isset($_SESSION['email'])) {
    $userEmail = trim($_SESSION['email']);
}

// Fetch contents from database (admin-uploaded content)
$stmt = $pdo->prepare("SELECT * FROM contents ORDER BY created_at DESC");
$stmt->execute();
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch the global apply setting from the settings table
$stmtSetting = $pdo->prepare("SELECT apply_enabled FROM settings WHERE id = 1");
$stmtSetting->execute();
$setting = $stmtSetting->fetch(PDO::FETCH_ASSOC);
$globalApply = $setting ? (int)$setting['apply_enabled'] : 0;

// If user is logged in, fetch applied tasks details from the applied_tasks table.
$appliedTasks = [];
if ($isLoggedIn) {
    $stmtApplied = $pdo->prepare("SELECT at.*, c.title FROM applied_tasks at JOIN contents c ON at.task_id = c.id WHERE at.user_email = ?");
    $stmtApplied->execute([$userEmail]);
    $appliedTasks = $stmtApplied->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Welcome - UGC Marketplace</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body {
        background-color: #f0f8ff;
    }
    .navbar {
        margin-bottom: 20px;
    }
    .content-section, .profile-section {
        margin-top: 30px;
    }
    .card {
        margin-bottom: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0,0,0,0.1);
    }
    .card-img-top {
        border-top-left-radius: 8px;
        border-top-right-radius: 8px;
    }
    a {
        color: #28a745;
        text-decoration: none;
    }
    a:hover {
        text-decoration: underline;
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
      <a class="navbar-brand" href="#">UGC Marketplace</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
              aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
            <?php if ($isLoggedIn): ?>
              <li class="nav-item">
                <span class="nav-link">
                  Welcome, <?php echo htmlspecialchars($userName); ?>
                </span>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="register_brand.php">Brand Register</a>
              </li>
            <?php else: ?>
              <li class="nav-item">
                <a class="nav-link" href="register.php">Register</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
              </li>
            <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

  <!-- User Profile Section (Visible Only When Logged In) -->
  <?php if ($isLoggedIn): ?>
  <div class="container profile-section">
    <div class="card">
      <div class="card-body">
        <h4 class="card-title">User Profile</h4>
        <p><strong>Registered Email:</strong> <?php echo htmlspecialchars($userEmail); ?></p>
        <h5>Applied Tasks</h5>
        <?php if (!empty($appliedTasks)): ?>
          <ul class="list-group">
            <?php foreach ($appliedTasks as $task): ?>
              <li class="list-group-item d-flex justify-content-between align-items-center">
                <?php echo htmlspecialchars($task['title']); ?>
                <span class="badge bg-primary rounded-pill"><?php echo htmlspecialchars($task['applied_date']); ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php else: ?>
          <p>You have not applied for any tasks yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <!-- Main Content -->
  <div class="container content-section">
    <?php if (!$isLoggedIn): ?>
      <div class="alert alert-info text-center">
        Welcome to UGC Marketplace. Please <a href="register.php" class="alert-link">Register</a> or 
        <a href="login.php" class="alert-link">Login</a> to view our content.
      </div>
    <?php else: ?>
      <h2 class="mb-4">Latest Content</h2>
      <div class="row">
        <?php if ($contents && count($contents) > 0): ?>
          <?php foreach ($contents as $content): ?>
            <div class="col-md-4">
              <div class="card">
                <?php if (!empty($content['image'])): ?>
                  <img src="<?php echo htmlspecialchars($content['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($content['title']); ?>">
                <?php endif; ?>
                <div class="card-body">
                  <h5 class="card-title"><?php echo htmlspecialchars($content['title']); ?></h5>
                  <p class="card-text"><?php echo htmlspecialchars($content['description']); ?></p>
                  <?php if (!empty($content['link'])): ?>
                    <a href="<?php echo htmlspecialchars($content['link']); ?>" class="btn btn-primary" target="_blank">Visit Link</a>
                  <?php endif; ?>
                  <!-- Apply Button for this task -->
                  <div class="mt-3">
                    <?php
                      $taskApply = isset($content['apply_enabled']) ? (int)$content['apply_enabled'] : 1;
                      if ($globalApply === 1 && $taskApply === 1):
                    ?>
                      <a href="apply.php?task_id=<?php echo htmlspecialchars($content['id']); ?>" class="btn btn-success">Apply</a>

                    <?php else: ?>
                      <button class="btn btn-secondary" disabled>Apply</button>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-warning">
              No content available at the moment. Please check back later.
            </div>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  </div>

  <!-- Bootstrap JS Bundle with Popper -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
