<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$showtimeId = $_GET['id'] ?? '';
$showtime = getShowtimeById($showtimeId);

if (!$showtime) {
    setFlashMessage('danger', 'Showtime not found.');
    redirect('showtimes.php');
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
        if (updateShowtime($showtimeId, $movieId, $hallId, $showDate, $showTime)) {
            setFlashMessage('success', 'Showtime updated successfully!');
            redirect('showtimes.php');
        } else {
            setFlashMessage('danger', 'Failed to update showtime.');
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Edit Showtime</title>
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
            <h2>Edit Showtime #<?= $showtime['showtime_id'] ?></h2>
            <?php displayFlashMessage(); ?>

            <form method="POST" action="">
                <div class="form-group">
                    <label for="movie_id">Movie *</label>
                    <select id="movie_id" name="movie_id" required>
                        <option value="">Select Movie</option>
                        <?php foreach ($movies as $movie): ?>
                            <option value="<?= $movie['movie_id'] ?>" <?= $movie['movie_id'] == $showtime['movie_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($movie['movie_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="hall_id">Hall *</label>
                    <select id="hall_id" name="hall_id" required>
                        <option value="">Select Hall</option>
                        <?php foreach ($halls as $hall): ?>
                            <option value="<?= $hall['hall_id'] ?>" <?= $hall['hall_id'] == $showtime['hall_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($hall['hall_name']) ?> (<?= $hall['total_rows'] * $hall['seats_per_row'] ?> seats)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="show_date">Show Date *</label>
                    <input type="date" id="show_date" name="show_date" required value="<?= $showtime['show_date'] ?>">
                </div>

                <div class="form-group">
                    <label for="show_time">Show Time *</label>
                    <input type="time" id="show_time" name="show_time" required value="<?= substr($showtime['show_time'], 0, 5) ?>">
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Showtime</button>
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