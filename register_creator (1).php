<?php
session_start();
include('config.php');

$availableContentTypes = ['Fashion', 'Beauty', 'Food', 'Travel', 'Tech', 'Fitness', 'Lifestyle', 'Gaming', 'Education'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve and sanitize form data
    $name           = trim($_POST['name']);
    $instagram      = trim($_POST['instagram']);
    $email          = trim($_POST['email']); // New email input
    $phone          = trim($_POST['phone']);
    $followers      = trim($_POST['followers']);
    $content_types  = trim($_POST['content_types']); // Stored as a comma-separated string
    $address        = trim($_POST['address']);
    $details        = trim($_POST['details']);

    // Insert new content creator with is_verified default 0 (not verified)
    $stmt = $pdo->prepare("INSERT INTO content_creators (name, instagram, email, phone, followers, content_types, address, details, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 0)");
    if ($stmt->execute([$name, $instagram, $email, $phone, $followers, $content_types, $address, $details])) {
        $_SESSION['name'] = $name;
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
  <title>Verify Your Profile - UGC Marketplace</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <style>
    body { background-color: #f0f8ff; }
    .modal-header { background-color: #007bff; color: white; }
    .modal-footer { background-color: #f7f7f7; }
    .content-type-item {
        cursor: pointer;
        padding: 5px 10px;
        border: 1px solid #ddd;
        margin: 2px;
        display: inline-block;
        border-radius: 5px;
    }
    .content-type-item.selected {
        background-color: #007bff;
        color: white;
    }
  </style>
</head>
<body>
    <div class="modal fade" id="registerCreatorModal" tabindex="-1" aria-labelledby="registerCreatorModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerCreatorModalLabel">Verify Your Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="window.location.href='welcome.php';"></button>
                </div>
                <div class="modal-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
                    <?php endif; ?>
                    <form action="" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" name="name" id="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="instagram" class="form-label">Instagram ID</label>
                            <input type="text" name="instagram" id="instagram" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" name="email" id="email" class="form-control" required>
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
                            <label class="form-label">Content Types</label>
                            <div id="contentTypesContainer">
                                <?php foreach ($availableContentTypes as $type): ?>
                                    <span class="content-type-item" data-type="<?php echo htmlspecialchars($type); ?>"><?php echo htmlspecialchars($type); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <input type="hidden" name="content_types" id="selectedContentTypes">
                        </div>
                        <div class="mb-3">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" name="address" id="address" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="details" class="form-label">Other Details</label>
                            <textarea name="details" id="details" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit for Verification</button>
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
        var registerCreatorModal = new bootstrap.Modal(document.getElementById('registerCreatorModal'), { backdrop: 'static' });
        registerCreatorModal.show();

        document.addEventListener('DOMContentLoaded', function() {
            const contentTypeItems = document.querySelectorAll('.content-type-item');
            const selectedContentTypesInput = document.getElementById('selectedContentTypes');
            let selectedContentTypes = [];

            contentTypeItems.forEach(item => {
                item.addEventListener('click', function() {
                    this.classList.toggle('selected');
                    const type = this.getAttribute('data-type');
                    
                    if (this.classList.contains('selected')) {
                        selectedContentTypes.push(type);
                    } else {
                        selectedContentTypes = selectedContentTypes.filter(t => t !== type);
                    }
                    
                    selectedContentTypesInput.value = selectedContentTypes.join(', '); // Store as a comma-separated string
                });
            });
        });
    </script>
</body>
</html>
