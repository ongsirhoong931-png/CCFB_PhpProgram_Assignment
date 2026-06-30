<?php
require_once 'includes/functions.php';

$movieId = $_GET['movie_id'] ?? '';
$movie = getMovieById($movieId);

if (!$movie) {
    setFlashMessage('danger', 'Movie not found.');
    redirect('movies.php');
}

$showtimes = getShowtimesByMovie($movieId);

// Group showtimes by date for a cleaner display
$showtimesByDate = [];
foreach ($showtimes as $st) {
    $showtimesByDate[$st['show_date']][] = $st;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Select Showtime</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">🎬 <span>CINEMA</span>BOOKING</div>
            <nav>
                <ul>
                    <li><a href="index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="movies.php" class="active"><i class="fas fa-film"></i> Movies</a></li>
                    <?php if (isLoggedIn()): ?>
                        <li><a href="my-bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a></li>
                        <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                        <?php if (isAdmin()): ?>
                            <li><a href="admin/"><i class="fas fa-crown"></i> Admin</a></li>
                        <?php endif; ?>
                        <li><a href="logout.php" class="btn-nav btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    <?php else: ?>
                        <li><a href="login.php" class="btn-nav btn-login"><i class="fas fa-sign-in-alt"></i> Login</a></li>
                        <li><a href="register.php" class="btn-nav btn-register"><i class="fas fa-user-plus"></i> Register</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php displayFlashMessage(); ?>

        <div class="movie-summary" style="margin: 25px 0;">
            <div class="thumb"><i class="fas fa-film"></i></div>
            <div>
                <h2><?= htmlspecialchars($movie['movie_name']) ?></h2>
                <p><strong><i class="fas fa-star" style="color: #f59e0b;"></i> Rating:</strong> <?= $movie['rating_code'] ?></p>
                <p><strong><i class="fas fa-tag"></i> Price per ticket:</strong> <?= formatPrice($movie['price']) ?></p>
            </div>
        </div>

        <h3 style="margin: 20px 0 15px;"><i class="fas fa-clock"></i> Select a Showtime</h3>

        <?php if (empty($showtimesByDate)): ?>
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> No showtimes have been scheduled for this movie yet. Please check back later.
            </div>
        <?php else: ?>
            <?php foreach ($showtimesByDate as $date => $times): ?>
                <div class="card" style="margin-bottom: 20px;">
                    <div class="card-header">
                        <h3><i class="fas fa-calendar-day"></i> <?= date('l, d M Y', strtotime($date)) ?></h3>
                    </div>
                    <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                        <?php foreach ($times as $st): ?>
                            <?php if (!isLoggedIn()): ?>
                                <a href="login.php" class="btn btn-secondary" style="min-width: 140px; text-align: center; flex-direction: column; height: auto; padding: 14px;">
                                    <strong><?= date('h:i A', strtotime($st['show_time'])) ?></strong>
                                    <span style="font-size: 12px; color: var(--gray); display: block;"><?= htmlspecialchars($st['hall_name']) ?></span>
                                </a>
                            <?php else: ?>
                                <a href="booking.php?showtime_id=<?= $st['showtime_id'] ?>" class="btn btn-primary" style="min-width: 140px; text-align: center; flex-direction: column; height: auto; padding: 14px;">
                                    <strong><?= date('h:i A', strtotime($st['show_time'])) ?></strong>
                                    <span style="font-size: 12px; opacity: 0.85; display: block;"><?= htmlspecialchars($st['hall_name']) ?></span>
                                </a>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (!isLoggedIn()): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> Please <a href="login.php">login</a> to book a showtime.
                </div>
            <?php endif; ?>
        <?php endif; ?>
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
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>