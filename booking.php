<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    setFlashMessage('warning', 'Please login to book tickets.');
    redirect('login.php');
}

$movieId = $_GET['movie_id'] ?? '';
$movie = getMovieById($movieId);

if (!$movie) {
    setFlashMessage('danger', 'Movie not found.');
    redirect('movies.php');
}

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seats = $_POST['seats'] ?? '';
    $quantity = $_POST['quantity'] ?? 1;
    $total = $movie['price'] * (int)$quantity;
    
    if (empty($seats)) {
        setFlashMessage('danger', 'Please select at least one seat.');
    } else {
        $result = createBooking($_SESSION['user_id'], $movie['movie_id'], $seats, $total);
        if ($result['success']) {
            setFlashMessage('success', 'Booking confirmed! Order ID: #' . $result['order_id']);
            redirect('my-bookings.php');
        } else {
            setFlashMessage('danger', 'Booking failed: ' . $result['message']);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Book Tickets</title>
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
                    <li><a href="movies.php"><i class="fas fa-film"></i> Movies</a></li>
                    <li><a href="my-bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a></li>
                    <li><a href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/"><i class="fas fa-crown"></i> Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <?php displayFlashMessage(); ?>
        
        <div style="text-align: center; margin: 20px 0;">
            <h1 style="font-size: 32px; font-weight: 800;"><i class="fas fa-ticket-alt" style="color: var(--primary);"></i> Book Tickets</h1>
        </div>
        
        <div class="booking-container">
            <div class="movie-summary">
                <div class="thumb"><i class="fas fa-film"></i></div>
                <div>
                    <h2><?= htmlspecialchars($movie['movie_name']) ?></h2>
                    <p><strong><i class="fas fa-star" style="color: #f59e0b;"></i> Rating:</strong> <?= $movie['rating_code'] ?></p>
                    <p><strong><i class="fas fa-tag"></i> Price per ticket:</strong> <?= formatPrice($movie['price']) ?></p>
                    <p><strong><i class="fas fa-tags"></i> Genres:</strong> <?= htmlspecialchars($movie['genres'] ?? '') ?></p>
                </div>
            </div>

            <form method="POST" action="">
                <h3><i class="fas fa-chair"></i> Select Your Seats</h3>
                <p style="color: #666; margin-bottom: 15px; font-size: 14px;">
                    <i class="fas fa-info-circle"></i> Click on available seats to select them. Selected seats will be highlighted.
                </p>
                
                <div style="margin-bottom: 20px;">
                    <label for="quantity"><strong><i class="fas fa-ticket-alt"></i> Number of Tickets:</strong></label>
                    <select id="quantity" name="quantity" style="padding: 10px 16px; margin-left: 12px; border: 2px solid var(--gray-light); border-radius: var(--radius-sm); font-size: 15px; background: var(--white);">
                        <?php for ($i = 1; $i <= 10; $i++): ?>
                            <option value="<?= $i ?>"><?= $i ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="seat-grid">
                    <?php for ($row = 'A'; $row <= 'J'; $row++): ?>
                        <?php for ($col = 1; $col <= 10; $col++): ?>
                            <?php
                            $seatId = $row . $col;
                            $isBooked = false; // In real system, check database
                            ?>
                            <div class="seat <?= $isBooked ? 'booked' : 'available' ?>" data-seat="<?= $seatId ?>" onclick="toggleSeat(this)">
                                <?= $seatId ?>
                            </div>
                        <?php endfor; ?>
                    <?php endfor; ?>
                </div>
                
                <div style="display: flex; gap: 25px; justify-content: center; margin: 20px 0; flex-wrap: wrap;">
                    <div><span style="display: inline-block; width: 20px; height: 20px; background: #e5e7eb; border-radius: 6px; vertical-align: middle;"></span> Available</div>
                    <div><span style="display: inline-block; width: 20px; height: 20px; background: var(--primary-gradient); border-radius: 6px; vertical-align: middle;"></span> Selected</div>
                    <div><span style="display: inline-block; width: 20px; height: 20px; background: #ef4444; border-radius: 6px; vertical-align: middle;"></span> Booked</div>
                </div>
                
                <input type="hidden" name="seats" id="selectedSeats" value="">
                
                <div style="text-align: center; margin: 25px 0; padding: 20px; background: var(--light); border-radius: var(--radius-sm); border: 2px dashed var(--gray-light);">
                    <p style="font-size: 16px;">
                        <strong><i class="fas fa-chair"></i> Selected Seats:</strong> 
                        <span id="seatDisplay" style="color: var(--primary); font-weight: 700;">None</span>
                    </p>
                    <p style="font-size: 20px; margin-top: 8px;">
                        <strong><i class="fas fa-money-bill-wave"></i> Total Price:</strong> 
                        <span id="totalDisplay" style="color: var(--primary); font-weight: 800;"><?= formatPrice($movie['price']) ?></span>
                    </p>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%; padding: 16px; font-size: 18px;">
                    <i class="fas fa-check-circle"></i> Confirm Booking
                </button>
            </form>
        </div>
    </main>

    <script>
        const pricePerTicket = <?= $movie['price'] ?>;
        let selectedSeats = new Set();
        
        function toggleSeat(element) {
            if (element.classList.contains('booked')) return;
            
            const seatId = element.dataset.seat;
            
            if (selectedSeats.has(seatId)) {
                selectedSeats.delete(seatId);
                element.classList.remove('selected');
            } else {
                selectedSeats.add(seatId);
                element.classList.add('selected');
            }
            
            updateDisplay();
        }
        
        function updateDisplay() {
            const seats = Array.from(selectedSeats);
            document.getElementById('selectedSeats').value = seats.join(',');
            document.getElementById('seatDisplay').textContent = seats.join(', ') || 'None';
            
            const quantity = parseInt(document.getElementById('quantity').value);
            const total = selectedSeats.size * pricePerTicket;
            document.getElementById('totalDisplay').textContent = 'RM ' + total.toFixed(2);
        }
        
        document.getElementById('quantity').addEventListener('change', updateDisplay);
        
        // Initialize
        updateDisplay();
    </script>

    <footer>
        <div class="container">
            <div class="footer-links">
                <a href="index.php"><i class="fas fa-home"></i> Home</a>
                <a href="movies.php"><i class="fas fa-film"></i> Movies</a>
                <a href="my-bookings.php"><i class="fas fa-ticket-alt"></i> My Bookings</a>
                <a href="profile.php"><i class="fas fa-user"></i> Profile</a>
            </div>
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved. Made with <span class="heart">❤️</span></p>
        </div>
    </footer>
</body>
</html>