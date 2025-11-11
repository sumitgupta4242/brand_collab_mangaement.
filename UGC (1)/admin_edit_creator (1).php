<?php
session_start();
include('config.php');

// Ensure admin is logged in
if (!isset($_SESSION['admin']) || $_SESSION['admin'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// Check if ID is provided
if (!isset($_GET['id'])) {
    header("Location: admin_users.php");
    exit();
}

$id = intval($_GET['id']);

// Process form submission for update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name          = trim($_POST['name']);
    $instagram     = trim($_POST['instagram']);
    $phone         = trim($_POST['phone']);
    $followers     = trim($_POST['followers']);
    $content_types = trim($_POST['content_types']);
    $address       = trim($_POST['address']);
    $details       = trim($_POST['details']);
    $status        = isset($_POST['status']) ? intval($_POST['status']) : 1; // 1=Active, 0=Blocked

    $stmt = $pdo->prepare("UPDATE content_creators SET name = ?, instagram = ?, phone = ?, followers = ?, content_types = ?, address = ?, details = ?, status = ? WHERE id = ?");
    if ($stmt->execute([$name, $instagram, $phone, $followers, $content_types, $address, $details, $status, $id])) {
        header("Location: admin_users.php");
        exit();
    } else {
        $error = "Update failed. Please try again.";
    }
}

// Fetch the content creator details
$stmt = $pdo->prepare("SELECT * FROM content_creators WHERE id = ?");
$stmt->execute([$id]);
$creator = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$creator) {
    header("Location: admin_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Edit Content Creator - Admin Panel</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
   <h2>Edit Content Creator</h2>
   <a href="admin_users.php" class="btn btn-secondary mb-3">Back to Admin Users</a>
   <?php if(isset($error)): ?>
       <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
   <?php endif; ?>
   <form action="" method="POST">
       <div class="mb-3">
           <label for="name" class="form-label">Name</label>
           <input type="text" name="name" id="name" class="form-control" value="<?php echo htmlspecialchars($creator['name']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="instagram" class="form-label">Instagram ID</label>
           <input type="text" name="instagram" id="instagram" class="form-control" value="<?php echo htmlspecialchars($creator['instagram']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="phone" class="form-label">Phone Number</label>
           <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($creator['phone']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="followers" class="form-label">Followers</label>
           <input type="number" name="followers" id="followers" class="form-control" value="<?php echo htmlspecialchars($creator['followers']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="content_types" class="form-label">Content Types</label>
           <input type="text" name="content_types" id="content_types" class="form-control" value="<?php echo htmlspecialchars($creator['content_types']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="address" class="form-label">Address</label>
           <input type="text" name="address" id="address" class="form-control" value="<?php echo htmlspecialchars($creator['address']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="details" class="form-label">Other Details</label>
           <textarea name="details" id="details" class="form-control" rows="3"><?php echo htmlspecialchars($creator['details']); ?></textarea>
       </div>
       <div class="mb-3">
           <label for="status" class="form-label">Status</label>
           <select name="status" id="status" class="form-control">
               <option value="1" <?php if ($creator['status'] == 1) echo 'selected'; ?>>Active</option>
               <option value="0" <?php if ($creator['status'] == 0) echo 'selected'; ?>>Blocked</option>
           </select>
       </div>
       <button type="submit" class="btn btn-success">Update</button>
   </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
