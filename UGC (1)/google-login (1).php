<?php
// google-login.php
require_once 'vendor/autoload.php'; // Make sure you have installed google/apiclient via Composer
session_start();
$client = new Google_Client();
$client->setClientId('1033645016400-q050v75dol5m9ekuk52r2toit00cu5jv.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/UGC/google-callback.php'); // Replace with your actual callback URL
$client->addScope('email');
$client->addScope('profile');

// Redirect to Google's OAuth 2.0 server
$login_url = $client->createAuthUrl();
header('Location: ' . filter_var($login_url, FILTER_SANITIZE_URL));
exit();
?>
