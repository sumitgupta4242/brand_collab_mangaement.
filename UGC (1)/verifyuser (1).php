<?php
session_start();
include('config.php');

if (isset($_GET['action'], $_GET['type'], $_GET['id'])) {
    $action = $_GET['action'];
    $type   = $_GET['type'];
    $id     = intval($_GET['id']);
    $message = '';
    
    // Determine the table based on type
    if ($type == 'creator') {
        $table = 'content_creators';
    } elseif ($type == 'brand') {
        $table = 'brands';
    } else {
        $table = '';
    }
    
    if (!empty($table)) {
        if ($action == 'delete') {
            $stmt = $pdo->prepare("DELETE FROM $table WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = ucfirst($type) . " deleted successfully.";
            } else {
                $message = "Error deleting " . $type;
            }
        } elseif ($action == 'block') {
            // Set status to 0 (blocked)
            $stmt = $pdo->prepare("UPDATE $table SET status = 0 WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = ucfirst($type) . " blocked successfully.";
            } else {
                $message = "Error blocking " . $type;
            }
        } elseif ($action == 'unblock') {
            // Set status to 1 (active)
            $stmt = $pdo->prepare("UPDATE $table SET status = 1 WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = ucfirst($type) . " unblocked successfully.";
            } else {
                $message = "Error unblocking " . $type;
            }
        } elseif ($action == 'verify' && $type == 'creator') {
            // Update verification status for content creator
            $stmt = $pdo->prepare("UPDATE content_creators SET is_verified = 1 WHERE id = ?");
            if ($stmt->execute([$id])) {
                $message = "Content creator verified successfully.";
                
                // If the verified content creator is currently logged in,
                // update the session variable to reflect the new verification status.
                $stmtEmail = $pdo->prepare("SELECT email FROM content_creators WHERE id = ?");
                $stmtEmail->execute([$id]);
                $creatorEmail = trim($stmtEmail->fetchColumn());
                if ($creatorEmail && isset($_SESSION['email'])) {
                    // Compare emails in a case-insensitive way
                    if (strtolower(trim($_SESSION['email'])) === strtolower($creatorEmail)) {
                        $_SESSION['is_verified'] = true;
                    }
                }
            } else {
                $message = "Verification failed. Please try again.";
            }
        }
        header("Location: admin_users.php?msg=" . urlencode($message));
        exit();
    }
}
?>
