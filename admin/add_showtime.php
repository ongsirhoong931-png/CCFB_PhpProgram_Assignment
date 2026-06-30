<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$movies = getAllMovies();
$halls = getAllHalls();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $movieId = $_POST['movie_id'] ?? '';
    $hallId = $_POST['hall_id'] ?? '';
    $showDate = $_POST['show_date'] ?? '';
    $showTime = $_POST['show_time'] ?? '';

    if (empty($movieId) || empty($hallId) || empty($showDate) || empty($showTime)) {
        setFlashMessage('danger', 'Please fill in all fields.');
    } else {
        if (addShowtime($movieId, $hallId, $showDate, $showTime)) {
            setFlashMessage('success', 'Showtime added successfully!');
            redirect('showtimes.php');
        } else {
            setFlashMessage('danger', 'Failed to add showtime.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Add Showtime</title>
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
                    <li><a href="showtimes.php">Showtimes</a></li>
                    <li><a href="../logout.php" class="btn-logout">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container" style="max-width: 600px;">
            <h2>Add New Showtime</h2>
            <?php displayFlashMessage(); ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="movie_id">Movie *</label>
                    <select id="movie_id" name="movie_id" required>
                        <option value="">Select Movie</option>
                        <?php foreach ($movies as $movie): ?>
                            <option value="<?= $movie['movie_id'] ?>"><?= htmlspecialchars($movie['movie_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hall_id">Hall *</label>
                    <select id="hall_id" name="hall_id" required>
                        <option value="">Select Hall</option>
                        <?php foreach ($halls as $hall): ?>
                            <option value="<?= $hall['hall_id'] ?>">
                                <?= htmlspecialchars($hall['hall_name']) ?> (<?= $hall['total_rows'] * $hall['seats_per_row'] ?> seats)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="show_date">Show Date *</label>
                    <input type="date" id="show_date" name="show_date" required min="<?= date('Y-m-d') ?>">
                </div>

                <div class="form-group">
                    <label for="show_time">Show Time *</label>
                    <input type="time" id="show_time" name="show_time" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-success">Add Showtime</button>
                    <a href="showtimes.php" class="btn btn-secondary">Cancel</a>
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