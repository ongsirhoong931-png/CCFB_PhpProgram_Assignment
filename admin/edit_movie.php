<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$movieId = $_GET['id'] ?? '';
$movie = getMovieById($movieId);

if (!$movie) {
    setFlashMessage('danger', 'Movie not found.');
    redirect('index.php');
}

$ratings = getAllRatings();
$allGenres = getAllGenres();
$movieGenres = getMovieGenres($movieId);
$movieGenreIds = array_column($movieGenres, 'genre_id');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieName = $_POST['movie_name'] ?? '';
    $ratingId = $_POST['rating_id'] ?? '';
    $price = $_POST['price'] ?? '';
    $releaseDate = $_POST['release_date'] ?? null;
    $duration = $_POST['duration'] ?? null;
    $description = $_POST['description'] ?? '';
    $selectedGenres = $_POST['genres'] ?? [];
    
    // Validate
    if (empty($movieName) || empty($ratingId) || empty($price)) {
        setFlashMessage('danger', 'Please fill in all required fields.');
    } else {
        // Update movie
        $result = updateMovie($movieId, $movieName, $ratingId, $price, $releaseDate, $duration, $description);
        
        if ($result) {
            // Update genres
            $db = getDB();
            $db->query("DELETE FROM movie_genres WHERE movie_id = '$movieId'");
            
            if (!empty($selectedGenres)) {
                foreach ($selectedGenres as $genreId) {
                    $db->query("INSERT INTO movie_genres (movie_id, genre_id) VALUES ('$movieId', $genreId)");
                }
            }
            
            setFlashMessage('success', 'Movie updated successfully!');
            redirect('index.php');
        } else {
            setFlashMessage('danger', 'Failed to update movie.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Edit Movie</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">🎬 <span>CINEMA</span>BOOKING</div>
            <nav>
                <ul>
                    <li><a href="../index.php">Home</a></li>
                    <li><a href="../movies.php">Movies</a></li>
                    <li><a href="index.php">Admin</a></li>
                    <li><a href="../logout.php" class="btn-logout">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container" style="max-width: 600px;">
            <h2>Edit Movie: <?= htmlspecialchars($movie['movie_name']) ?></h2>
            <?php displayFlashMessage(); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="movie_name">Movie Name *</label>
                    <input type="text" id="movie_name" name="movie_name" value="<?= htmlspecialchars($movie['movie_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="rating_id">Rating *</label>
                    <select id="rating_id" name="rating_id" required>
                        <option value="">Select Rating</option>
                        <?php foreach ($ratings as $rating): ?>
                            <option value="<?= $rating['rating_id'] ?>" <?= $movie['rating_id'] == $rating['rating_id'] ? 'selected' : '' ?>>
                                <?= $rating['rating_code'] ?> - <?= $rating['description'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (RM) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" value="<?= $movie['price'] ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date" value="<?= $movie['release_date'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration" min="0" value="<?= $movie['duration_minutes'] ?? '' ?>">
                </div>
                
                <div class="form-group">
                    <label>Genres (Select multiple)</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; padding: 10px; border: 2px solid #ddd; border-radius: 5px; max-height: 200px; overflow-y: auto;">
                        <?php foreach ($allGenres as $genre): ?>
                            <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                                <input type="checkbox" name="genres[]" value="<?= $genre['genre_id'] ?>" 
                                    <?= in_array($genre['genre_id'], $movieGenreIds) ? 'checked' : '' ?>>
                                <?= $genre['genre_name'] ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"><?= htmlspecialchars($movie['description'] ?? '') ?></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Movie</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>