<?php
session_start(); // Start session if needed
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UGC Marketplace</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .navbar {
            background-color: #343a40;
        }

        .navbar-brand,
        .nav-link {
            color: white !important;
        }

        .hero-section {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(to right, #6a11cb, #2575fc);
            color: white;
        }

        .btn-custom {
            background-color: #ff7f50;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
        }
    </style>
</head>

<body>

    <!-- Navigation Bar -->
    <!-- index.php excerpt -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="#">UGC Marketplace</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Projects</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Contact Us</a></li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
                    <li class="nav-item"><a class="nav-link" href="register.php">Sign-UP</a></li>
                    <!-- Admin Login Button -->
                    <li class="nav-item"><a class="nav-link" href="admin_login.php">Admin Login</a></li>
                </ul>
            </div>
        </div>
    </nav>


    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Welcome to the UGC Marketplace</h1>
        <p>Buy and Sell Digital Content Easily</p>
        <a href="register.php" class="btn btn-custom">Explore Now</a>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2>What is UGC Marketplace?</h2>
        <p>
            The UGC (User-Generated Content) Marketplace is a platform where creators can upload, sell, and purchase
            digital content such as graphics, videos, music, and templates.
            It connects buyers with talented content creators worldwide, making it easier to find high-quality assets
            for various projects.
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>