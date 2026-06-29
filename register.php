<?php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $full_name = $_POST['full_name'] ?? '';
    $gender = $_POST['gender'] ?? '';
    
    $errors = [];
    
    if (empty($username)) $errors[] = 'Username is required';
    if (strlen($username) < 3) $errors[] = 'Username must be at least 3 characters';
    if (empty($password)) $errors[] = 'Password is required';
    if (strlen($password) < 6) $errors[] = 'Password must be at least 6 characters';
    if ($password !== $confirm_password) $errors[] = 'Passwords do not match';
    if (empty($email)) $errors[] = 'Email is required';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Invalid email format';
    if (empty($phone)) $errors[] = 'Phone number is required';
    if (empty($full_name)) $errors[] = 'Full name is required';
    if (empty($gender)) $errors[] = 'Gender is required';
    
    if (empty($errors)) {
        $result = registerUser($username, $password, $email, $phone, $full_name, $gender);
        
        if ($result['success']) {
            setFlashMessage('success', 'Registration successful! Please login.');
            redirect('login.php');
        } else {
            setFlashMessage('danger', $result['message']);
        }
    } else {
        setFlashMessage('danger', implode('<br>', $errors));
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Register</title>
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
                    <li><a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                    <li><a href="register.php" class="active"><i class="fas fa-user-plus"></i> Register</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <div style="text-align: center; margin-bottom: 10px;">
                <span style="font-size: 48px;">🎬</span>
            </div>
            <h2>Create Account</h2>
            <p class="subtitle"><i class="fas fa-arrow-right" style="color: var(--primary);"></i> Join us and start booking your movies today!</p>
            <?php displayFlashMessage(); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="full_name"><i class="fas fa-user"></i> Full Name</label>
                    <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required value="<?= htmlspecialchars($_POST['full_name'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="username"><i class="fas fa-id-badge"></i> Username</label>
                    <input type="text" id="username" name="username" placeholder="Choose a username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="email"><i class="fas fa-envelope"></i> Email</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone"><i class="fas fa-phone"></i> Phone Number</label>
                    <input type="text" id="phone" name="phone" placeholder="Enter your phone number" required value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
                </div>
                
                <div class="form-group">
                    <label for="gender"><i class="fas fa-venus-mars"></i> Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="">Select Gender</option>
                        <option value="M" <?= ($_POST['gender'] ?? '') == 'M' ? 'selected' : '' ?>>Male</option>
                        <option value="F" <?= ($_POST['gender'] ?? '') == 'F' ? 'selected' : '' ?>>Female</option>
                        <option value="O" <?= ($_POST['gender'] ?? '') == 'O' ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="password"><i class="fas fa-lock"></i> Password</label>
                    <input type="password" id="password" name="password" placeholder="Min 6 characters" required>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password"><i class="fas fa-check-circle"></i> Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter your password" required>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create Account
                    </button>
                </div>
            </form>
            
            <div class="form-footer">
                Already have an account? <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login here</a>
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