<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        setFlashMessage('danger', 'Please fill in all fields.');
    } else {
        if (loginUser($username, $password)) {
            setFlashMessage('success', 'Welcome back, ' . $_SESSION['full_name'] . '!');
            redirect('index.php');
        } else {
            setFlashMessage('danger', 'Invalid username or password. Please try again.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <span class="icon">🎬</span>
                <span>CINEMA</span>BOOKING
                <span class="tagline">| Book Your Seat</span>
            </div>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="movies.php"><i class="fas fa-film"></i> Movies</a></li>
                    <li><a href="login.php" class="active"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <div style="text-align: center; margin-bottom: 10px;">
                <span style="font-size: 48px;">🎬</span>
            </div>
            <h2>Welcome Back!</h2>
            <p class="subtitle"><i class="fas fa-arrow-right" style="color: var(--primary);"></i> Login to your account to book tickets</p>
            <?php displayFlashMessage(); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username"><i class="fas fa-user"></i> Username</label>
                    <input type="text" id="username" name="username" placeholder="Enter your username" required>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </div>
            </form>
            
            <div class="form-footer">
                Don't have an account? <a href="register.php"><i class="fas fa-user-plus"></i> Register here</a>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="movies.php"><i class="fas fa-film"></i> Movies</a>
                <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
            </div>
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Made with <span class="heart">❤️</span></p>
        </div>
    </footer>
</body>
</html>