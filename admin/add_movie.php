<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$ratings = getAllRatings();
$genres = getAllGenres();

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
        $movieId = generateMovieId();
        
        // Insert movie
        $result = addMovie($movieId, $movieName, $ratingId, $price, $releaseDate, $duration, $description);
        
        if ($result) {
            // Insert genres
            if (!empty($selectedGenres)) {
                $db = getDB();
                foreach ($selectedGenres as $genreId) {
                    $db->query("INSERT INTO movie_genres (movie_id, genre_id) VALUES ('$movieId', $genreId)");
                }
            }
            setFlashMessage('success', 'Movie added successfully!');
            redirect('index.php');
        } else {
            setFlashMessage('danger', 'Failed to add movie.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Add Movie</title>
    <link rel="stylesheet" href="../css/style.css">
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
            <h2>Add New Movie</h2>
            <?php displayFlashMessage(); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="movie_name">Movie Name *</label>
                    <input type="text" id="movie_name" name="movie_name" required>
                </div>
                
                <div class="form-group">
                    <label for="rating_id">Rating *</label>
                    <select id="rating_id" name="rating_id" required>
                        <option value="">Select Rating</option>
                        <?php foreach ($ratings as $rating): ?>
                            <option value="<?= $rating['rating_id'] ?>"><?= $rating['rating_code'] ?> - <?= $rating['description'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="price">Price (RM) *</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required>
                </div>
                
                <div class="form-group">
                    <label for="release_date">Release Date</label>
                    <input type="date" id="release_date" name="release_date">
                </div>
                
                <div class="form-group">
                    <label for="duration">Duration (minutes)</label>
                    <input type="number" id="duration" name="duration" min="0">
                </div>
                
                <div class="form-group">
                    <label>Genres (Select multiple)</label>
                    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 10px; padding: 10px; border: 2px solid #ddd; border-radius: 5px; max-height: 200px; overflow-y: auto;">
                        <?php foreach ($genres as $genre): ?>
                            <label style="display: flex; align-items: center; gap: 5px; cursor: pointer;">
                                <input type="checkbox" name="genres[]" value="<?= $genre['genre_id'] ?>">
                                <?= $genre['genre_name'] ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-success">Add Movie</button>
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