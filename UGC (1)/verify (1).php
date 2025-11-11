<?php
include('config.php');

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    // Check if the token exists in the database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE verification_token = ?");
    $stmt->execute([$token]);
    $user = $stmt->fetch();

    if ($user) {
        // Update user status to verified
        $stmt = $pdo->prepare("UPDATE users SET is_verified = 1, verification_token = NULL WHERE id = ?");
        $stmt->execute([$user['id']]);

        echo "Email verified successfully! You can now <a href='login.php'>login</a>.";
    } else {
        echo "Invalid or expired verification link.";
    }
} else {
    echo "No verification token provided.";
}
?>
