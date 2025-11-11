<?php
// db_config.php

$host = "localhost:3307";
$dbname = "ugc_marketplace";
$username = "root"; // Update if needed
$password = "";     // Update if needed

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set error mode to exception to catch errors properly
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Optional: echo "Database connection successful!";
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
