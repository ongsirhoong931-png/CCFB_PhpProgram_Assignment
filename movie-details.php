<?php
require_once 'includes/functions.php';

$movieId = $_GET['id'] ?? '';
$movie = getMovieById($movieId);

if (!$movie) {
    setFlashMessage('danger', 'Movie not found.');
    redirect('movies.php');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - <?= htmlspecialchars($movie['movie_name']) ?></title>
    <link rel="stylesheet" href="assets/css/style.css">
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
                    <li><a href="index.php">Home</a></li>
                    <li><a href="movies.php" class="active">Movies</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="my-bookings.php">My Bookings</a></li>
                        <li><a href="profile.php">Profile</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin/">Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="btn-nav btn-logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-nav btn-login">Login</a></li>
                        <li><a href="register.php" class="btn-nav btn-register">Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php displayFlashMessage(); ?>
        
        <div style="display: grid; grid-template-columns: 320px 1fr; gap: 40px; background: var(--white); border-radius: var(--radius); padding: 40px; box-shadow: var(--shadow); margin: 30px 0;">
            <div style="height: 450px; background: linear-gradient(135deg, var(--secondary), var(--secondary-light)); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center; font-size: 80px; color: rgba(255,255,255,0.2); position: relative; overflow: hidden;">
                🎬
            </div>
            <div>
                <h2 style="font-size: 36px; font-weight: 800; margin-bottom: 10px;"><?= htmlspecialchars($movie['movie_name']) ?></h2>
                <div>
                    <span style="display: inline-block; background: var(--secondary); color: var(--white); padding: 4px 16px; border-radius: 20px; font-weight: 700; font-size: 13px;"><?= $movie['rating_code'] ?></span>
                    <span style="margin-left: 10px; color: var(--gray);">ID: <?= $movie['movie_id'] ?></span>
                </div>
                <div style="font-size: 32px; font-weight: 900; color: var(--primary); margin: 15px 0;"><?= formatPrice($movie['price']) ?></div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin: 15px 0; padding: 15px; background: var(--light); border-radius: var(--radius-sm);">
                    <div>
                        <strong style="color: var(--gray);">Genres</strong>
                        <p><?= htmlspecialchars($movie['genres'] ?? 'N/A') ?></p>
                    </div>
                    <div>
                        <strong style="color: var(--gray);">Rating Description</strong>
                        <p><?= htmlspecialchars($movie['rating_description'] ?? '') ?></p>
                    </div>
                </div>
                
                <div style="margin: 20px 0;">
                    <h4 style="margin-bottom: 8px;">About this movie</h4>
                    <p style="color: var(--gray); line-height: 1.8;"><?= htmlspecialchars($movie['description'] ?? 'No description available.') ?></p>
                </div>
                
                <?php if (isLoggedIn()): ?>
                    <a href="booking.php?movie_id=<?= $movie['movie_id'] ?>" class="btn btn-primary" style="padding: 14px 40px; font-size: 16px;">🎫 Book Now</a>
                <?php else: ?>
                    <div style="margin-top: 20px; padding: 15px; background: var(--light); border-radius: var(--radius-sm); text-align: center;">
                        <p style="color: var(--gray);">Please <a href="login.php" style="font-weight: 600;">login</a> to book tickets.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="index.php">Home</a>
                <a href="movies.php">Movies</a>
                <?php if (isLoggedIn()): ?>
                    <a href="my-bookings.php">My Bookings</a>
                    <a href="profile.php">Profile</a>
                <?php else: ?>
                    <a href="login.php">Login</a>
                    <a href="register.php">Register</a>
                <?php endif; ?>
            </div>
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Made with ❤️</p>
        </div>
    </footer>
</body>
</html>