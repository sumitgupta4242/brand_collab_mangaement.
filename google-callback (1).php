<?php
include("config.php");
require_once 'vendor/autoload.php';
session_start();

$client = new Google_Client();
$client->setClientId('1033645016400-q050v75dol5m9ekuk52r2toit00cu5jv.apps.googleusercontent.com');
$client->setClientSecret('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');
$client->setRedirectUri('http://localhost/UGC/google-callback.php'); // Must match the URI set in google-login.php

if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    
    if (!isset($token['error'])) {
        $client->setAccessToken($token['access_token']);

        // Retrieve user profile information from Google
        $oauth = new Google_Service_Oauth2($client);
        $google_info = $oauth->userinfo->get();

        $google_email = $google_info->email;
        $google_name = $google_info->name;

        // Check if user exists in the database
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$google_email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // User exists, set session and redirect to welcome page
            $_SESSION['name']  = $user['name'];
            $_SESSION['email'] = $user['email'];

            header('Location: welcome.php');
            exit();
        } else {
            // User does not exist, show an error or redirect to register page
            $_SESSION['error'] = 'Your email is not registered. Please sign up first.';
            header('Location: register.php'); // Redirect to registration page if needed
            exit();
        }
    } else {
        // Handle errors, e.g., token error
        $_SESSION['error'] = 'Failed to get access token.';
        header('Location: login.php');
        exit();
    }
} else {
    // If no code parameter exists, redirect back to login.
    header('Location: login.php');
    exit();
}
?>
