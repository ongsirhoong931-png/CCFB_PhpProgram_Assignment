<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    setFlashMessage('warning', 'Please login to book tickets.');
    redirect('login.php');
}

$showtimeId = $_GET['showtime_id'] ?? '';
$showtime = getShowtimeById($showtimeId);

if (!$showtime) {
    setFlashMessage('danger', 'Showtime not found.');
    redirect('movies.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $seats = $_POST['seats'] ?? '';
    
    if (empty($seats)) {
        setFlashMessage('danger', 'Please select at least one seat.');
    } else {
        $seatList = explode(',', $seats);
        $total = $showtime['price'] * count($seatList);
        
        $result = createBooking($_SESSION['user_id'], $showtimeId, $seatList, $total);
        if ($result['success']) {
            setFlashMessage('success', 'Booking confirmed! Order ID: #' . $result['order_id']);
            redirect('my-bookings.php');
        } else {
            setFlashMessage('danger', 'Booking failed: ' . $result['message']);
        }
    }
}

// Re-fetch in case anything changed and build the seat grid
$totalRows = (int)$showtime['total_rows'];
$seatsPerRow = (int)$showtime['seats_per_row'];
$bookedSeats = getBookedSeats($showtimeId);
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
                    <h2><?= htmlspecialchars($showtime['movie_name']) ?></h2>
                    <p><strong><i class="fas fa-calendar-day"></i> Date:</strong> <?= date('l, d M Y', strtotime($showtime['show_date'])) ?></p>
                    <p><strong><i class="fas fa-clock"></i> Time:</strong> <?= date('h:i A', strtotime($showtime['show_time'])) ?></p>
                    <p><strong><i class="fas fa-door-open"></i> Hall:</strong> <?= htmlspecialchars($showtime['hall_name']) ?></p>
                    <p><strong><i class="fas fa-tag"></i> Price per ticket:</strong> <?= formatPrice($showtime['price']) ?></p>
                </div>
            </div>

            <form method="POST" action="">
                <h3><i class="fas fa-chair"></i> Select Your Seats</h3>
                <p style="color: #666; margin-bottom: 15px; font-size: 14px;">
                    <i class="fas fa-info-circle"></i> Click on available seats to select them. Seats already booked for this showtime are disabled.
                </p>
                
                <div class="seat-grid" style="grid-template-columns: repeat(<?= $seatsPerRow ?>, 1fr);">
                    <?php for ($r = 0; $r < $totalRows; $r++): ?>
                        <?php $rowLetter = chr(65 + $r); ?>
                        <?php for ($c = 1; $c <= $seatsPerRow; $c++): ?>
                            <?php
                            $seatId = $rowLetter . $c;
                            $isBooked = in_array($seatId, $bookedSeats);
                            ?>
                            <div class="seat <?= $isBooked ? 'booked' : 'available' ?>" data-seat="<?= $seatId ?>" <?= $isBooked ? '' : 'onclick="toggleSeat(this)"' ?>>
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
                        <span id="totalDisplay" style="color: var(--primary); font-weight: 800;">RM 0.00</span>
                    </p>
                </div>
                
                <button type="submit" class="btn btn-success" style="width: 100%; padding: 16px; font-size: 18px;">
                    <i class="fas fa-check-circle"></i> Confirm Booking
                </button>
            </form>
        </div>
    </main>

    <script>
        const pricePerTicket = <?= $showtime['price'] ?>;
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
            
            const total = selectedSeats.size * pricePerTicket;
            document.getElementById('totalDisplay').textContent = 'RM ' + total.toFixed(2);
        }
        
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