<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$showtimes = getAllShowtimes();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Manage Showtimes</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                    <li><a href="showtimes.php" class="active">Showtimes</a></li>
                    <li><a href="../logout.php" class="btn-logout">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php displayFlashMessage(); ?>

        <div class="section-header">
            <h1 class="section-title">Manage Showtimes</h1>
            <a href="add_showtime.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Add Showtime
            </a>
        </div>

        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Movie</th>
                        <th>Hall</th>
                        <th>Date</th>
                        <th>Time</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($showtimes)): ?>
                        <?php foreach ($showtimes as $st): ?>
                            <tr>
                                <td><strong>#<?= $st['showtime_id'] ?></strong></td>
                                <td><?= htmlspecialchars($st['movie_name']) ?></td>
                                <td><?= htmlspecialchars($st['hall_name']) ?></td>
                                <td><?= date('d M Y', strtotime($st['show_date'])) ?></td>
                                <td><?= date('h:i A', strtotime($st['show_time'])) ?></td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                        <a href="edit_showtime.php?id=<?= $st['showtime_id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete_showtime.php?id=<?= $st['showtime_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this showtime? Any bookings tied to it will keep their order history but lose the showtime link.')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--gray);">
                                <i class="fas fa-calendar-times" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                                No showtimes scheduled yet
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>