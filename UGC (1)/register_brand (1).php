<?php
session_start();
include('config.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $brandName     = trim($_POST['brand_name']);
    $instagram     = trim($_POST['instagram']);
    $phone         = trim($_POST['phone']);
    $followers     = trim($_POST['followers']);
    $content_types = trim($_POST['content_types']);
    $address       = trim($_POST['address']);
    $details       = trim($_POST['details']);

    // Insert new brand into the database
    $stmt = $pdo->prepare("INSERT INTO brands (brand_name, instagram, phone, followers, content_types, address, details) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$brandName, $instagram, $phone, $followers, $content_types, $address, $details])) {
        $_SESSION['name'] = $brandName;
        header("Location: welcome.php");
        exit();
    } else {
        $error = "Registration failed. Please try again.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1">
   <title>Register as Brand - UGC Marketplace</title>
   <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
   <style>
       body { background-color: #f0f8ff; }
       .modal-header { background-color: #17a2b8; color: white; }
       .modal-footer { background-color: #f7f7f7; }
   </style>
</head>
<body>
   <!-- Registration Modal for Brands -->
   <div class="modal fade" id="registerBrandModal" tabindex="-1" aria-labelledby="registerBrandModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
         <div class="modal-content">
            <div class="modal-header">
               <h5 class="modal-title" id="registerBrandModalLabel">Register as Brand</h5>
               <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='welcome.php';"></button>
            </div>
            <div class="modal-body">
               <?php if(isset($error)): ?>
               <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
               <?php endif; ?>
               <form action="" method="POST">
                  <div class="mb-3">
                     <label for="brand_name" class="form-label">Brand Name</label>
                     <input type="text" name="brand_name" id="brand_name" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label for="instagram" class="form-label">Instagram ID</label>
                     <input type="text" name="instagram" id="instagram" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label for="phone" class="form-label">Phone Number</label>
                     <input type="text" name="phone" id="phone" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label for="followers" class="form-label">Followers</label>
                     <input type="number" name="followers" id="followers" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label for="content_types" class="form-label">Content / Product Types</label>
                     <input type="text" name="content_types" id="content_types" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label for="address" class="form-label">Address</label>
                     <input type="text" name="address" id="address" class="form-control" required>
                  </div>
                  <div class="mb-3">
                     <label for="details" class="form-label">Other Details</label>
                     <textarea name="details" id="details" class="form-control" rows="3"></textarea>
                  </div>
                  <button type="submit" class="btn btn-info w-100">Register</button>
               </form>
            </div>
            <div class="modal-footer">
               <button type="button" class="btn btn-secondary" onclick="window.location.href='welcome.php';">Close</button>
            </div>
         </div>
      </div>
   </div>
   <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
   <script>
      var registerBrandModal = new bootstrap.Modal(document.getElementById('registerBrandModal'), { backdrop: 'static' });
      registerBrandModal.show();
   </script>
</body>
</html>
