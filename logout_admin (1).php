<?php
session_start();
unset($_SESSION['admin']);
unset($_SESSION['admin_username']);
session_destroy(); // Clear admin session data
header("Location: admin_login.php"); // Redirect to the admin login page
exit();
?>
