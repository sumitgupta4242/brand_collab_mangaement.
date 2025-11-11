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
    $brand_name    = trim($_POST['brand_name']);
    $instagram     = trim($_POST['instagram']);
    $phone         = trim($_POST['phone']);
    $followers     = trim($_POST['followers']);
    $content_types = trim($_POST['content_types']);
    $address       = trim($_POST['address']);
    $details       = trim($_POST['details']);
    $status        = isset($_POST['status']) ? intval($_POST['status']) : 1; // 1=Active, 0=Blocked

    $stmt = $pdo->prepare("UPDATE brands SET brand_name = ?, instagram = ?, phone = ?, followers = ?, content_types = ?, address = ?, details = ?, status = ? WHERE id = ?");
    if ($stmt->execute([$brand_name, $instagram, $phone, $followers, $content_types, $address, $details, $status, $id])) {
        header("Location: admin_users.php");
        exit();
    } else {
        $error = "Update failed. Please try again.";
    }
}

// Fetch the brand details
$stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
$stmt->execute([$id]);
$brand = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$brand) {
    header("Location: admin_users.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Edit Brand - Admin Panel</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
   <h2>Edit Brand</h2>
   <a href="admin_users.php" class="btn btn-secondary mb-3">Back to Admin Users</a>
   <?php if(isset($error)): ?>
       <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
   <?php endif; ?>
   <form action="" method="POST">
       <div class="mb-3">
           <label for="brand_name" class="form-label">Brand Name</label>
           <input type="text" name="brand_name" id="brand_name" class="form-control" value="<?php echo htmlspecialchars($brand['brand_name']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="instagram" class="form-label">Instagram ID</label>
           <input type="text" name="instagram" id="instagram" class="form-control" value="<?php echo htmlspecialchars($brand['instagram']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="phone" class="form-label">Phone Number</label>
           <input type="text" name="phone" id="phone" class="form-control" value="<?php echo htmlspecialchars($brand['phone']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="followers" class="form-label">Followers</label>
           <input type="number" name="followers" id="followers" class="form-control" value="<?php echo htmlspecialchars($brand['followers']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="content_types" class="form-label">Content / Product Types</label>
           <input type="text" name="content_types" id="content_types" class="form-control" value="<?php echo htmlspecialchars($brand['content_types']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="address" class="form-label">Address</label>
           <input type="text" name="address" id="address" class="form-control" value="<?php echo htmlspecialchars($brand['address']); ?>" required>
       </div>
       <div class="mb-3">
           <label for="details" class="form-label">Other Details</label>
           <textarea name="details" id="details" class="form-control" rows="3"><?php echo htmlspecialchars($brand['details']); ?></textarea>
       </div>
       <div class="mb-3">
           <label for="status" class="form-label">Status</label>
           <select name="status" id="status" class="form-control">
               <option value="1" <?php if ($brand['status'] == 1) echo 'selected'; ?>>Active</option>
               <option value="0" <?php if ($brand['status'] == 0) echo 'selected'; ?>>Blocked</option>
           </select>
       </div>
       <button type="submit" class="btn btn-success">Update</button>
   </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
