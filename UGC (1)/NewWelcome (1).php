<?php
session_start();
include('config.php');

$isLoggedIn = false;
$userName = "";
$userEmail = "";
$isVerified = false;

if (isset($_SESSION['name'])) {
    $isLoggedIn = true;
    $userName = $_SESSION['name'];
}
if (isset($_SESSION['email'])) {
    $userEmail = trim($_SESSION['email']);
}

if ($isLoggedIn && !empty($userEmail)) {
    $stmtVerify = $pdo->prepare("SELECT is_verified FROM content_creators WHERE LOWER(TRIM(email)) = LOWER(?)");
    $stmtVerify->execute([$userEmail]);
    $creatorData = $stmtVerify->fetch(PDO::FETCH_ASSOC);
    if ($creatorData) {
        $isVerified = ((int)$creatorData['is_verified'] === 1);
    }
}

$stmt = $pdo->prepare("SELECT * FROM contents ORDER BY created_at DESC");
$stmt->execute();
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmtSetting = $pdo->prepare("SELECT apply_enabled FROM settings WHERE id = 1");
$stmtSetting->execute();
$setting = $stmtSetting->fetch(PDO::FETCH_ASSOC);
$globalApply = $setting ? (int)$setting['apply_enabled'] : 0;
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
        background-color: #f8f9fa;
    }
    .navbar {
        margin-bottom: 20px;
    }
    .content-section {
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
  </style>
</head>
<body>
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
                <span class="nav-link">Welcome, <?php echo htmlspecialchars($userName); ?></span>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="logout.php">Logout</a>
              </li>
              <li class="nav-item">
                <a class="nav-link" href="register_creator.php">Verify Your Profile</a>
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

  <div class="container content-section">
    <?php if (!$isLoggedIn): ?>
      <div class="alert alert-info text-center">
        Welcome to UGC Marketplace. Please <a href="register.php" class="alert-link">Register</a> or 
        <a href="login.php" class="alert-link">Login</a> to view our content.
      </div>
    <?php else: ?>
      <?php if ($isVerified): ?>
          <div class="alert alert-success text-center">
              You are verified now.
          </div>
      <?php else: ?>
          <div class="alert alert-warning text-center">
              Your profile is not verified. Please contact the admin for verification.
          </div>
      <?php endif; ?>
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
                  <div class="mt-3">
                    <?php
                      $taskApply = isset($content['apply_enabled']) ? (int)$content['apply_enabled'] : 1;
                      if ($globalApply === 1 && $taskApply === 1 && $isVerified):
                    ?>
                      <a href="successfull.php?task_id=<?php echo htmlspecialchars($content['id']); ?>" class="btn btn-success">Apply</a>
                    <?php else: ?>
                      <button class="btn btn-secondary" disabled>Apply</button>
                      <?php if (!$isVerified): ?>
                        <div class="mt-1 text-danger small">Please verify your profile</div>
                      <?php endif; ?>
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

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
