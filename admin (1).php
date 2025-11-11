<?php
session_start();
include('config.php');

// Ensure admin is logged in; if not, redirect to admin login.
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// ---------------------------
// Global Settings - Toggle Apply Feature (for all tasks)
// ---------------------------
$settings_message = "";
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_toggle'])) {
    $apply_enabled = ($_POST['apply_toggle'] === 'on') ? 1 : 0;
    $stmtSetting = $pdo->prepare("UPDATE settings SET apply_enabled = ? WHERE id = 1");
    $stmtSetting->execute([$apply_enabled]);
    $settings_message = "Global apply setting updated successfully.";
}

// Fetch the current global setting
$stmtSetting = $pdo->prepare("SELECT apply_enabled FROM settings WHERE id = 1");
$stmtSetting->execute();
$settings = $stmtSetting->fetch(PDO::FETCH_ASSOC);
$currentApplySetting = $settings ? $settings['apply_enabled'] : 0;

// ---------------------------
// Content Management Section
// ---------------------------
$action = isset($_GET['action']) ? $_GET['action'] : '';

// Handle deletion of content
if ($action == 'delete' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Delete any applied_tasks that reference this content to satisfy foreign key constraints.
    $stmtDeleteApplied = $pdo->prepare("DELETE FROM applied_tasks WHERE task_id = ?");
    $stmtDeleteApplied->execute([$id]);
    
    // Now, delete the content.
    $stmt = $pdo->prepare("DELETE FROM contents WHERE id = ?");
    $stmt->execute([$id]);
    header("Location: admin.php");
    exit();
}

// Handle toggling of individual task's apply setting
if ($action == 'toggle_apply' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    // First, retrieve the current apply status for this task.
    $stmt = $pdo->prepare("SELECT apply_enabled FROM contents WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($current) {
        $newStatus = ($current['apply_enabled'] == 1) ? 0 : 1;
        $stmtUpdate = $pdo->prepare("UPDATE contents SET apply_enabled = ? WHERE id = ?");
        $stmtUpdate->execute([$newStatus, $id]);
    }
    header("Location: admin.php");
    exit();
}

// Process content add or edit form submissions (if not global settings form).
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['apply_toggle'])) {
    // Process file upload for image
    $image = '';
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $fileTmpPath = $_FILES['image_file']['tmp_name'];
        $fileName = $_FILES['image_file']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));
        // Optionally, add allowed extension checks here.
        $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
        $dest_path = $uploadDir . $newFileName;
        if (move_uploaded_file($fileTmpPath, $dest_path)) {
            $image = $dest_path;
        }
    }
    // In edit mode, if no new image is uploaded, use the current image value.
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        if (empty($image) && isset($_POST['current_image'])) {
            $image = $_POST['current_image'];
        }
    }
    
    // Process expire_at field
    $expire_at = trim($_POST['expire_at']);
    if (empty($expire_at)) {
        $expire_at = NULL;
    } else {
        // Convert the datetime-local format (e.g. 2025-03-10T14:30) to MySQL datetime format.
        $expire_at = str_replace('T', ' ', $expire_at) . ":00"; // Append seconds if needed.
    }
    
    // Process link field
    $link = trim($_POST['link']);
    if (empty($link)) {
        $link = NULL;
    }
    
    // Check if editing content
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id = intval($_POST['id']);
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $stmt = $pdo->prepare("UPDATE contents SET title = ?, description = ?, image = ?, expire_at = ?, link = ? WHERE id = ?");
        $stmt->execute([$title, $description, $image, $expire_at, $link, $id]);
        header("Location: admin.php");
        exit();
    } else {
        // Adding new content
        $title = trim($_POST['title']);
        $description = trim($_POST['description']);
        $stmt = $pdo->prepare("INSERT INTO contents (title, description, image, expire_at, link) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$title, $description, $image, $expire_at, $link]);
        header("Location: admin.php");
        exit();
    }
}

// If action is edit, fetch the content for editing.
$editContent = null;
if ($action == 'edit' && isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $pdo->prepare("SELECT * FROM contents WHERE id = ?");
    $stmt->execute([$id]);
    $editContent = $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fetch all contents for display.
$stmt = $pdo->query("SELECT * FROM contents ORDER BY created_at DESC");
$contents = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin Panel - Manage Content</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f8f9fa; }
    .container { margin-top: 30px; }
    .table th, .table td { vertical-align: middle; }
  </style>
</head>
<body>
  <!-- Navbar with user's name -->
  <nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container-fluid">
      <a class="navbar-brand" href="admin.php">Admin Panel</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav me-auto">
          <li class="nav-item">
            <a class="nav-link" href="admin.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="logout_admin.php">Logout</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="admin_users.php">USERS</a>
          </li>
        </ul>
        <span class="navbar-text">
          Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Admin'); ?>
        </span>
      </div>
    </div>
  </nav>

  <div class="container">
    <!-- Global Settings Section -->
    <h4>Global Settings</h4>
    <?php if (!empty($settings_message)): ?>
      <div class="alert alert-success"><?php echo htmlspecialchars($settings_message); ?></div>
    <?php endif; ?>
    <form method="POST" class="mb-4">
        <div class="mb-3">
            <label for="apply_toggle" class="form-label">Allow Apply Feature (Global)</label>
            <select name="apply_toggle" id="apply_toggle" class="form-select" onchange="this.form.submit()">
                <option value="off" <?php if($currentApplySetting == 0) echo 'selected'; ?>>Off</option>
                <option value="on" <?php if($currentApplySetting == 1) echo 'selected'; ?>>On</option>
            </select>
        </div>
    </form>

    <!-- Content List Section -->
    <h4>Content List</h4>
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>ID</th>
                <th>Title</th>
                <th>Description</th>
                <th>Image</th>
                <th>Expire At</th>
                <th>Link</th>
                <th>Apply Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($contents as $content): ?>
            <tr>
                <td><?php echo htmlspecialchars($content['id']); ?></td>
                <td><?php echo htmlspecialchars($content['title']); ?></td>
                <td><?php echo htmlspecialchars($content['description']); ?></td>
                <td>
                    <?php if ($content['image']): ?>
                        <img src="<?php echo htmlspecialchars($content['image']); ?>" alt="Image" style="max-width:100px;">
                    <?php else: ?>
                        No Image
                    <?php endif; ?>
                </td>
                <td><?php echo htmlspecialchars($content['expire_at']); ?></td>
                <td>
                    <?php if ($content['link']): ?>
                        <a href="<?php echo htmlspecialchars($content['link']); ?>" target="_blank">Visit Link</a>
                    <?php else: ?>
                        No Link
                    <?php endif; ?>
                </td>
                <td>
                    <?php echo ($content['apply_enabled'] == 1) ? 'On' : 'Off'; ?>
                    <br>
                    <a href="admin.php?action=toggle_apply&id=<?php echo $content['id']; ?>" class="btn btn-sm btn-info">
                        <?php echo ($content['apply_enabled'] == 1) ? 'Disable Apply' : 'Enable Apply'; ?>
                    </a>
                </td>
                <td>
                    <a href="admin.php?action=edit&id=<?php echo $content['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                    <a href="admin.php?action=delete&id=<?php echo $content['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this content?');">Delete</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    
    <?php if ($editContent): 
      // Convert expire_at to datetime-local format if available.
      $expireAtValue = '';
      if (!empty($editContent['expire_at'])) {
          $dt = new DateTime($editContent['expire_at']);
          $expireAtValue = $dt->format('Y-m-d\TH:i');
      }
    ?>
    <!-- Edit Content Form -->
    <h4>Edit Content</h4>
    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="action" value="edit">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($editContent['id']); ?>">
        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($editContent['image']); ?>">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" value="<?php echo htmlspecialchars($editContent['title']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" required><?php echo htmlspecialchars($editContent['description']); ?></textarea>
        </div>
        <div class="mb-3">
            <label for="image_file" class="form-label">Upload Image</label>
            <input type="file" name="image_file" id="image_file" class="form-control">
            <?php if (!empty($editContent['image'])): ?>
                <small class="form-text text-muted">Current image: <?php echo htmlspecialchars($editContent['image']); ?></small>
            <?php endif; ?>
        </div>
        <div class="mb-3">
            <label for="expire_at" class="form-label">Expire At</label>
            <input type="datetime-local" name="expire_at" id="expire_at" class="form-control" value="<?php echo htmlspecialchars($expireAtValue); ?>">
            <small class="form-text text-muted">Leave empty if you do not want the content to expire.</small>
        </div>
        <div class="mb-3">
            <label for="link" class="form-label">Link for User</label>
            <input type="text" name="link" id="link" class="form-control" value="<?php echo htmlspecialchars($editContent['link'] ?? ''); ?>" placeholder="Enter a URL">
        </div>
        <button type="submit" class="btn btn-success">Update Content</button>
        <a href="admin.php" class="btn btn-secondary">Cancel</a>
    </form>
    <?php else: ?>
    <!-- Add New Content Form -->
    <h4>Add New Content</h4>
    <form action="admin.php" method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" required></textarea>
        </div>
        <div class="mb-3">
            <label for="image_file" class="form-label">Upload Image</label>
            <input type="file" name="image_file" id="image_file" class="form-control">
        </div>
        <div class="mb-3">
            <label for="expire_at" class="form-label">Expire At</label>
            <input type="datetime-local" name="expire_at" id="expire_at" class="form-control" placeholder="Optional">
            <small class="form-text text-muted">Leave empty if you do not want the content to expire.</small>
        </div>
        <div class="mb-3">
            <label for="link" class="form-label">Link for User</label>
            <input type="text" name="link" id="link" class="form-control" placeholder="Enter a URL">
        </div>
        <button type="submit" class="btn btn-primary">Add Content</button>
    </form>
    <?php endif; ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
