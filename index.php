<?php
require_once 'includes/functions.php';

$movies = getAllMovies();
$recentMovies = array_slice($movies, 0, 8);
$stats = getMovieStatistics();
$bookingStats = getBookingsStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Home</title>
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
                    <li><a href="index.php" class="active"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="movies.php"><i class="fas fa-film"></i> Movies</a></li>
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
        
        <section class="hero">
            <h1>Welcome to <span>CINEMA</span>BOOKING</h1>
            <p>Book your movie tickets online instantly. Choose from the latest blockbusters and enjoy the show!</p>
            <a href="movies.php" class="btn-hero"><i class="fas fa-play"></i> Browse Movies</a>
        </section>

        <?php if ($stats): ?>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?= $stats['total_movies'] ?></div>
                <div class="label"><i class="fas fa-film"></i> Total Movies</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= formatPrice($stats['min_price']) ?></div>
                <div class="label"><i class="fas fa-tag"></i> Cheapest Ticket</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= formatPrice($stats['max_price']) ?></div>
                <div class="label"><i class="fas fa-crown"></i> Most Expensive</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= formatPrice($stats['avg_price']) ?></div>
                <div class="label"><i class="fas fa-calculator"></i> Average Price</div>
            </div>
            <?php if ($bookingStats): ?>
            <div class="stat-card">
                <div class="number"><?= $bookingStats['total_bookings'] ?? 0 ?></div>
                <div class="label"><i class="fas fa-ticket-alt"></i> Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= formatPrice($bookingStats['total_revenue'] ?? 0) ?></div>
                <div class="label"><i class="fas fa-money-bill-wave"></i> Total Revenue</div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <div class="section-header">
            <h2 class="section-title">Now Showing</h2>
            <a href="movies.php" class="view-all">View All <i class="fas fa-arrow-right"></i></a>
        </div>

        <div class="movie-grid">
            <?php if (!empty($recentMovies)): ?>
                <?php foreach ($recentMovies as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <span class="badge"><?= $movie['rating_code'] ?></span>
                            <span class="price-tag"><?= formatPrice($movie['price']) ?></span>
                            <i class="fas fa-film" style="font-size: 60px; opacity: 0.15;"></i>
                        </div>
                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['movie_name']) ?></h3>
                            <div class="genres"><i class="fas fa-tags" style="font-size: 11px;"></i> <?= htmlspecialchars($movie['genres'] ?? 'No genres') ?></div>
                            <div class="meta">
                                <span style="font-size: 13px; color: var(--gray);">
                                    <i class="fas fa-layer-group"></i> <?= $movie['genre_count'] ?? 0 ?> genres
                                </span>
                                <span class="price"><?= formatPrice($movie['price']) ?></span>
                            </div>
                            <a href="movie-details.php?id=<?= $movie['movie_id'] ?>" class="btn-book">
                                <i class="fas fa-ticket-alt"></i> Book Now
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; padding: 60px; color: var(--gray);">
                    <i class="fas fa-inbox" style="font-size: 40px; display: block; margin-bottom: 15px;"></i>
                    No movies available at the moment.
                </p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="movies.php"><i class="fas fa-film"></i> Movies</a>
                <?php if (isLoggedIn()): ?>
                    <a href="my-bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                    <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
                <?php else: ?>
                    <a href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                    <a href="register.php"><i class="fas fa-user-plus"></i> Register</a>
                <?php endif; ?>
            </div>
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Made with <span class="heart">❤️</span></p>
        </div>
    </footer>
    
    <script>
        // Navbar scroll effect
        window.addEventListener('scroll', function() {
            const header = document.querySelector('header');
            if (window.scrollY > 50) {
                header.classList.add('scrolled');
            } else {
                header.classList.remove('scrolled');
            }
        });
    </script>
</body>
</html>