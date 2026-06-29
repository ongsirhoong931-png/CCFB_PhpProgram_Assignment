<?php
require_once 'includes/functions.php';

$search = $_GET['search'] ?? '';
$genre = $_GET['genre'] ?? '';
$min_price = $_GET['min_price'] ?? '';
$max_price = $_GET['max_price'] ?? '';

if (!empty($search)) {
    $movies = searchMovies($search);
} elseif (!empty($genre)) {
    $movies = getMoviesByGenre($genre);
} elseif (!empty($min_price) && !empty($max_price)) {
    $movies = getMoviesByPriceRange($min_price, $max_price);
} else {
    $movies = getAllMovies();
}

$genres = getAllGenres();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Movies</title>
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
        
        <div class="section-header">
            <h1 class="section-title">All Movies</h1>
            <span style="color: var(--gray);"><?= count($movies) ?> movies found</span>
        </div>
        
        <div class="filter-bar">
            <form method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; width: 100%; align-items: center;">
                <input type="text" name="search" placeholder="🔍 Search movies..." value="<?= htmlspecialchars($search) ?>" style="flex: 2; min-width: 180px;">
                
                <select name="genre" style="flex: 1; min-width: 130px;">
                    <option value="">All Genres</option>
                    <?php foreach ($genres as $g): ?>
                        <option value="<?= $g['genre_name'] ?>" <?= $genre == $g['genre_name'] ? 'selected' : '' ?>>
                            <?= $g['genre_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <input type="number" name="min_price" placeholder="Min RM" value="<?= htmlspecialchars($min_price) ?>" style="flex: 0.5; min-width: 80px;">
                <input type="number" name="max_price" placeholder="Max RM" value="<?= htmlspecialchars($max_price) ?>" style="flex: 0.5; min-width: 80px;">
                
                <button type="submit" class="btn btn-primary">Apply</button>
                <?php if (!empty($genre) || !empty($search) || !empty($min_price)): ?>
                    <a href="movies.php" class="btn btn-secondary">Clear</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="movie-grid">
            <?php if (!empty($movies)): ?>
                <?php foreach ($movies as $movie): ?>
                    <div class="movie-card">
                        <div class="movie-poster">
                            <span class="badge"><?= $movie['rating_code'] ?></span>
                            <span class="price-tag"><?= formatPrice($movie['price']) ?></span>
                            🎬
                        </div>
                        <div class="movie-info">
                            <h3><?= htmlspecialchars($movie['movie_name']) ?></h3>
                            <div class="genres"><?= htmlspecialchars($movie['genres'] ?? 'No genres') ?></div>
                            <div class="meta">
                                <span style="font-size: 13px; color: var(--gray);">
                                    <?= $movie['genre_count'] ?? 0 ?> genres
                                </span>
                                <span class="price"><?= formatPrice($movie['price']) ?></span>
                            </div>
                            <a href="movie-details.php?id=<?= $movie['movie_id'] ?>" class="btn-book">🎫 Book Now</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="grid-column: 1 / -1; text-align: center; padding: 60px 20px; color: var(--gray);">
                    🎬 No movies found. Try adjusting your filters.
                </p>
            <?php endif; ?>
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