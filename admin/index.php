<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$movies = getAllMovies();
$bookings = getAllBookings();
$stats = getMovieStatistics();
$bookingStats = getBookingsStatistics();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Admin Dashboard</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">
                <span class="icon">🎬</span>
                <span>CINEMA</span>BOOKING
                <span class="tagline">| Admin Panel</span>
            </div>
            <nav>
                <ul>
                    <li><a href="../index.php"><i class="fas fa-home"></i> Home</a></li>
                    <li><a href="../movies.php"><i class="fas fa-film"></i> Movies</a></li>
                    <li><a href="index.php" class="active"><i class="fas fa-crown"></i> Admin</a></li>
                    <li><a href="../logout.php" class="btn-nav btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php displayFlashMessage(); ?>
        
        <div class="section-header">
            <h1 class="section-title">Admin Dashboard</h1>
            <a href="add_movie.php" class="btn btn-success">
                <i class="fas fa-plus"></i> Add New Movie
            </a>
        </div>
        
        <!-- Statistics -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="number"><?= $stats['total_movies'] ?? 0 ?></div>
                <div class="label"><i class="fas fa-film"></i> Total Movies</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $bookingStats['total_bookings'] ?? 0 ?></div>
                <div class="label"><i class="fas fa-ticket-alt"></i> Total Bookings</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $bookingStats['unique_customers'] ?? 0 ?></div>
                <div class="label"><i class="fas fa-users"></i> Unique Customers</div>
            </div>
            <div class="stat-card">
                <div class="number"><?= $bookingStats ? formatPrice($bookingStats['total_revenue']) : 'RM 0' ?></div>
                <div class="label"><i class="fas fa-money-bill-wave"></i> Total Revenue</div>
            </div>
        </div>
        
        <!-- Movies Table -->
        <h2 style="margin-top: 50px; margin-bottom: 20px; font-size: 24px; font-weight: 700;">
            <i class="fas fa-film" style="color: var(--primary);"></i> Movies
        </h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Movie Name</th>
                        <th>Rating</th>
                        <th>Price</th>
                        <th>Genres</th>
                        <th style="text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($movies)): ?>
                        <?php foreach ($movies as $movie): ?>
                            <tr>
                                <td><strong><?= $movie['movie_id'] ?></strong></td>
                                <td><?= htmlspecialchars($movie['movie_name']) ?></td>
                                <td><span class="status-badge confirmed"><?= $movie['rating_code'] ?></span></td>
                                <td><strong><?= formatPrice($movie['price']) ?></strong></td>
                                <td><?= htmlspecialchars($movie['genres'] ?? '-') ?></td>
                                <td style="text-align: center;">
                                    <div style="display: flex; gap: 8px; justify-content: center; flex-wrap: wrap;">
                                        <a href="edit_movie.php?id=<?= $movie['movie_id'] ?>" class="btn btn-warning btn-sm">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="delete_movie.php?id=<?= $movie['movie_id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this movie?')">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align: center; padding: 40px; color: var(--gray);">
                                <i class="fas fa-inbox" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                                No movies found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Bookings Table -->
        <h2 style="margin-top: 50px; margin-bottom: 20px; font-size: 24px; font-weight: 700;">
            <i class="fas fa-clipboard-list" style="color: var(--primary);"></i> All Bookings
        </h2>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer</th>
                        <th>Movie</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th style="text-align: center;">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($bookings)): ?>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><strong>#<?= $booking['id'] ?></strong></td>
                                <td><?= htmlspecialchars($booking['full_name'] ?? $booking['username']) ?></td>
                                <td><?= htmlspecialchars($booking['movie_name']) ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($booking['order_date'])) ?></td>
                                <td><strong><?= formatPrice($booking['total']) ?></strong></td>
                                <td>
                                    <span class="status-badge <?= strtolower($booking['status']) ?>">
                                        <?= $booking['status'] ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($booking['status'] != 'Cancelled'): ?>
                                        <a href="update_order.php?id=<?= $booking['id'] ?>&status=Cancelled" class="btn btn-danger btn-sm" onclick="return confirm('Cancel this booking?')">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    <?php else: ?>
                                        <span style="color: var(--gray); font-size: 12px;">
                                            <i class="fas fa-check-circle" style="color: #22c55e;"></i> Cancelled
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 40px; color: var(--gray);">
                                <i class="fas fa-inbox" style="font-size: 30px; display: block; margin-bottom: 10px;"></i>
                                No bookings found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="../index.php"><i class="fas fa-home"></i> Home</a>
                <a href="../movies.php"><i class="fas fa-film"></i> Movies</a>
                <a href="index.php"><i class="fas fa-crown"></i> Admin</a>
            </div>
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Made with <span class="heart">❤️</span></p>
        </div>
    </footer>
</body>
</html>