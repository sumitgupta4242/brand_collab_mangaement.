<?php
session_start();
include('config.php');

// Ensure the user is logged in
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit();
}

$userEmail = trim($_SESSION['email']);

// Check if task_id is provided
if (!isset($_GET['task_id'])) {
    header("Location: welcome.php?msg=" . urlencode("No task selected."));
    exit();
}

$task_id = intval($_GET['task_id']);

// Verify that the task exists
$stmt = $pdo->prepare("SELECT * FROM contents WHERE id = ?");
$stmt->execute([$task_id]);
$task = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$task) {
    header("Location: welcome.php?msg=" . urlencode("Invalid task."));
    exit();
}

// Check if the user already applied for this task
$stmt = $pdo->prepare("SELECT id FROM applied_tasks WHERE user_email = ? AND task_id = ?");
$stmt->execute([$userEmail, $task_id]);
if ($stmt->fetch(PDO::FETCH_ASSOC)) {
    header("Location: welcome.php?msg=" . urlencode("You have already applied for this task."));
    exit();
}

// Insert the application record into applied_tasks table
$stmt = $pdo->prepare("INSERT INTO applied_tasks (user_email, task_id, applied_date) VALUES (?, ?, NOW())");
if ($stmt->execute([$userEmail, $task_id])) {
    header("Location: welcome.php?msg=" . urlencode("You have successfully applied for the task."));
    exit();
} else {
    header("Location: welcome.php?msg=" . urlencode("Failed to apply for the task."));
    exit();
}
?>
