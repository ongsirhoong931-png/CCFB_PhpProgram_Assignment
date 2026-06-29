<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    setFlashMessage('warning', 'Please login to view your bookings.');
    redirect('login.php');
}

$bookings = getUserBookings($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - My Bookings</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">🎬 <span>CINEMA</span>BOOKING</div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="movies.php">Movies</a></li>
                    <li><a href="my-bookings.php" class="active">My Bookings</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php displayFlashMessage(); ?>
        
        <h1>My Bookings</h1>
        
        <div class="table-container">
            <?php if (!empty($bookings)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Movie</th>
                            <th>Date</th>
                            <th>Quantity</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td>#<?= $booking['id'] ?></td>
                                <td><?= htmlspecialchars($booking['movie_name']) ?></td>
                                <td><?= date('d M Y, h:i A', strtotime($booking['order_date'])) ?></td>
                                <td><?= $booking['unit'] ?></td>
                                <td><?= formatPrice($booking['total']) ?></td>
                                <td>
                                    <span style="display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 12px; font-weight: bold; 
                                        <?php 
                                        $status = $booking['status'];
                                        if ($status == 'Confirmed') echo 'background: #d4edda; color: #155724;';
                                        elseif ($status == 'Pending') echo 'background: #fff3cd; color: #856404;';
                                        elseif ($status == 'Cancelled') echo 'background: #f8d7da; color: #721c24;';
                                        else echo 'background: #d1ecf1; color: #0c5460;';
                                        ?>">
                                        <?= $booking['status'] ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="text-align: center; padding: 40px 0; color: #666;">
                    You haven't made any bookings yet. 
                    <br><a href="movies.php" class="btn btn-primary" style="margin-top: 15px;">Browse Movies</a>
                </p>
            <?php endif; ?>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>