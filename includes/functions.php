<?php
// ============================================
// HELPER FUNCTIONS - WITH SHOWTIME MODULE
// ============================================

require_once 'db_connection.php';

session_start();

// ---------- USER FUNCTIONS ----------

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    $db = getDB();
    $userId = (int)$_SESSION['user_id'];
    $result = $db->query("SELECT * FROM user WHERE user_id = $userId");
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function loginUser($username, $password) {
    $db = getDB();
    $username = $db->real_escape_string($username);
    
    $result = $db->query("SELECT * FROM user WHERE username = '$username' AND is_blocked = 0");
    
    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc();
        
        // Check password (using SHA2 as in your dbA.sql)
        $hashedPassword = hash('sha256', $password);
        if ($user['password'] === $hashedPassword) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['full_name'] = $user['full_name'];
            return true;
        }
    }
    return false;
}

function logoutUser() {
    session_unset();
    session_destroy();
}

function registerUser($username, $password, $email, $phone, $full_name, $gender) {
    $db = getDB();
    
    // Escape inputs
    $username = $db->real_escape_string($username);
    $email = $db->real_escape_string($email);
    $phone = $db->real_escape_string($phone);
    $full_name = $db->real_escape_string($full_name);
    $gender = $db->real_escape_string($gender);
    
    // Check if username exists
    $check = $db->query("SELECT user_id FROM user WHERE username = '$username'");
    if ($check && $check->num_rows > 0) {
        return ['success' => false, 'message' => 'Username already exists'];
    }
    
    // Check if email exists
    $check = $db->query("SELECT user_id FROM user WHERE email = '$email'");
    if ($check && $check->num_rows > 0) {
        return ['success' => false, 'message' => 'Email already registered'];
    }
    
    // Hash password using SHA2 (matching your dbA.sql)
    $hashedPassword = hash('sha256', $password);
    
    $sql = "INSERT INTO user (username, password, email, phone, full_name, gender, role) 
            VALUES ('$username', '$hashedPassword', '$email', '$phone', '$full_name', '$gender', 'member')";
    
    if ($db->query($sql)) {
        return ['success' => true, 'message' => 'Registration successful'];
    }
    
    return ['success' => false, 'message' => 'Registration failed: ' . $db->error];
}

// ---------- MOVIE FUNCTIONS ----------

function getAllMovies() {
    $db = getDB();
    $result = $db->query("SELECT * FROM movie_details");
    $movies = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
    return $movies;
}

function getMovieById($movieId) {
    $db = getDB();
    $movieId = $db->real_escape_string($movieId);
    
    $result = $db->query("SELECT * FROM movie_details WHERE movie_id = '$movieId'");
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function getMoviesByGenre($genre) {
    $db = getDB();
    $genre = $db->real_escape_string($genre);
    
    $result = $db->query("SELECT * FROM movie_details WHERE genres LIKE '%$genre%'");
    $movies = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
    return $movies;
}

function searchMovies($keyword) {
    $db = getDB();
    $keyword = $db->real_escape_string($keyword);
    
    $result = $db->query("SELECT * FROM movie_details WHERE movie_name LIKE '%$keyword%' OR genres LIKE '%$keyword%'");
    $movies = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
    return $movies;
}

function getMoviesByPriceRange($min, $max) {
    $db = getDB();
    $min = (float)$min;
    $max = (float)$max;
    
    $result = $db->query("SELECT * FROM movie_details WHERE price BETWEEN $min AND $max");
    $movies = [];
    
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $movies[] = $row;
        }
    }
    return $movies;
}

function addMovie($movieId, $movieName, $ratingId, $price, $releaseDate = null, $duration = null, $description = null) {
    $db = getDB();
    
    $movieId = $db->real_escape_string($movieId);
    $movieName = $db->real_escape_string($movieName);
    $ratingId = (int)$ratingId;
    $price = (float)$price;
    $releaseDate = $releaseDate ? "'" . $db->real_escape_string($releaseDate) . "'" : "NULL";
    $duration = $duration ? (int)$duration : "NULL";
    $description = $description ? "'" . $db->real_escape_string($description) . "'" : "NULL";
    
    $sql = "INSERT INTO movies (movie_id, movie_name, rating_id, price, release_date, duration_minutes, description) 
            VALUES ('$movieId', '$movieName', $ratingId, $price, $releaseDate, $duration, $description)";
    
    return $db->query($sql);
}

function updateMovie($movieId, $movieName, $ratingId, $price, $releaseDate = null, $duration = null, $description = null) {
    $db = getDB();
    
    $movieId = $db->real_escape_string($movieId);
    $movieName = $db->real_escape_string($movieName);
    $ratingId = (int)$ratingId;
    $price = (float)$price;
    $releaseDate = $releaseDate ? "'" . $db->real_escape_string($releaseDate) . "'" : "NULL";
    $duration = $duration ? (int)$duration : "NULL";
    $description = $description ? "'" . $db->real_escape_string($description) . "'" : "NULL";
    
    $sql = "UPDATE movies SET 
            movie_name = '$movieName',
            rating_id = $ratingId,
            price = $price,
            release_date = $releaseDate,
            duration_minutes = $duration,
            description = $description
            WHERE movie_id = '$movieId'";
    
    return $db->query($sql);
}

function deleteMovie($movieId) {
    $db = getDB();
    $movieId = $db->real_escape_string($movieId);
    
    $sql = "DELETE FROM movies WHERE movie_id = '$movieId'";
    return $db->query($sql);
}

// ---------- HALL FUNCTIONS ----------

function getAllHalls() {
    $db = getDB();
    $result = $db->query("SELECT * FROM halls ORDER BY hall_name");
    $halls = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $halls[] = $row;
        }
    }
    return $halls;
}

function getHallById($hallId) {
    $db = getDB();
    $hallId = (int)$hallId;
    $result = $db->query("SELECT * FROM halls WHERE hall_id = $hallId");
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// ---------- SHOWTIME FUNCTIONS ----------

function getShowtimesByMovie($movieId) {
    $db = getDB();
    $movieId = $db->real_escape_string($movieId);
    
    $sql = "SELECT s.*, h.hall_name, h.total_rows, h.seats_per_row
            FROM showtimes s
            JOIN halls h ON s.hall_id = h.hall_id
            WHERE s.movie_id = '$movieId'
              AND (s.show_date > CURDATE() OR (s.show_date = CURDATE() AND s.show_time >= CURTIME()))
            ORDER BY s.show_date, s.show_time";
    
    $result = $db->query($sql);
    $showtimes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $showtimes[] = $row;
        }
    }
    return $showtimes;
}

function getShowtimeById($showtimeId) {
    $db = getDB();
    $showtimeId = (int)$showtimeId;
    
    $sql = "SELECT s.*, h.hall_name, h.total_rows, h.seats_per_row, m.movie_name, m.price
            FROM showtimes s
            JOIN halls h ON s.hall_id = h.hall_id
            JOIN movies m ON s.movie_id = m.movie_id
            WHERE s.showtime_id = $showtimeId";
    
    $result = $db->query($sql);
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function getAllShowtimes() {
    $db = getDB();
    
    $sql = "SELECT s.*, h.hall_name, m.movie_name
            FROM showtimes s
            JOIN halls h ON s.hall_id = h.hall_id
            JOIN movies m ON s.movie_id = m.movie_id
            ORDER BY s.show_date DESC, s.show_time DESC";
    
    $result = $db->query($sql);
    $showtimes = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $showtimes[] = $row;
        }
    }
    return $showtimes;
}

function addShowtime($movieId, $hallId, $showDate, $showTime) {
    $db = getDB();
    $movieId = $db->real_escape_string($movieId);
    $hallId = (int)$hallId;
    $showDate = $db->real_escape_string($showDate);
    $showTime = $db->real_escape_string($showTime);
    
    $sql = "INSERT INTO showtimes (movie_id, hall_id, show_date, show_time) 
            VALUES ('$movieId', $hallId, '$showDate', '$showTime')";
    
    return $db->query($sql);
}

function updateShowtime($showtimeId, $movieId, $hallId, $showDate, $showTime) {
    $db = getDB();
    $showtimeId = (int)$showtimeId;
    $movieId = $db->real_escape_string($movieId);
    $hallId = (int)$hallId;
    $showDate = $db->real_escape_string($showDate);
    $showTime = $db->real_escape_string($showTime);
    
    $sql = "UPDATE showtimes SET 
            movie_id = '$movieId',
            hall_id = $hallId,
            show_date = '$showDate',
            show_time = '$showTime'
            WHERE showtime_id = $showtimeId";
    
    return $db->query($sql);
}

function deleteShowtime($showtimeId) {
    $db = getDB();
    $showtimeId = (int)$showtimeId;
    
    $sql = "DELETE FROM showtimes WHERE showtime_id = $showtimeId";
    return $db->query($sql);
}

// ---------- SEAT AVAILABILITY FUNCTIONS ----------

function getBookedSeats($showtimeId) {
    $db = getDB();
    $showtimeId = (int)$showtimeId;
    
    $result = $db->query("SELECT seat_code FROM booked_seats WHERE showtime_id = $showtimeId");
    $seats = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $seats[] = $row['seat_code'];
        }
    }
    return $seats;
}

// ---------- BOOKING FUNCTIONS ----------

function createBooking($userId, $showtimeId, $seats, $total) {
    $db = getDB();
    $userId = (int)$userId;
    $showtimeId = (int)$showtimeId;
    $total = (float)$total;
    
    $seatList = is_array($seats) ? $seats : explode(',', $seats);
    $seatList = array_values(array_filter(array_map('trim', $seatList)));
    
    if (empty($seatList)) {
        return ['success' => false, 'message' => 'No seats selected'];
    }
    
    $showtime = getShowtimeById($showtimeId);
    if (!$showtime) {
        return ['success' => false, 'message' => 'Showtime not found'];
    }
    
    // Start transaction
    $db->begin_transaction();
    
    try {
        // Re-check seat availability inside the transaction to avoid
        // two people booking the same seat at the same time
        $alreadyBooked = getBookedSeats($showtimeId);
        $conflict = array_intersect($seatList, $alreadyBooked);
        if (!empty($conflict)) {
            $db->rollback();
            return ['success' => false, 'message' => 'Seat(s) ' . implode(', ', $conflict) . ' were just taken by someone else. Please choose again.'];
        }
        
        // Insert order
        $sql = "INSERT INTO `order` (user_id, total, status) VALUES ($userId, $total, 'Confirmed')";
        $db->query($sql);
        $orderId = $db->insert_id;
        
        // Insert item (movie booking) linked to the showtime
        $movieId = $db->real_escape_string($showtime['movie_id']);
        $unit = count($seatList);
        $sql = "INSERT INTO item (order_id, product_id, showtime_id, unit, price) 
                VALUES ($orderId, '$movieId', $showtimeId, $unit, $total)";
        $db->query($sql);
        
        // Lock in each individual seat
        foreach ($seatList as $seat) {
            $seat = $db->real_escape_string($seat);
            $db->query("INSERT INTO booked_seats (showtime_id, seat_code, order_id) VALUES ($showtimeId, '$seat', $orderId)");
        }
        
        $db->commit();
        return ['success' => true, 'order_id' => $orderId];
    } catch (Exception $e) {
        $db->rollback();
        return ['success' => false, 'message' => $e->getMessage()];
    }
}

function getUserBookings($userId) {
    $db = getDB();
    $userId = (int)$userId;
    
    $sql = "SELECT o.*, i.product_id, i.unit, i.price as item_price, i.showtime_id,
                   m.movie_name, s.show_date, s.show_time, h.hall_name
            FROM `order` o 
            JOIN item i ON o.id = i.order_id 
            JOIN movies m ON i.product_id = m.movie_id 
            LEFT JOIN showtimes s ON i.showtime_id = s.showtime_id
            LEFT JOIN halls h ON s.hall_id = h.hall_id
            WHERE o.user_id = $userId 
            ORDER BY o.order_date DESC";
    
    $result = $db->query($sql);
    
    if (!$result) {
        // Table might not exist yet, return empty array
        return [];
    }
    
    $bookings = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    return $bookings;
}

function getAllBookings() {
    $db = getDB();
    
    $sql = "SELECT o.*, u.username, u.full_name, i.product_id, i.showtime_id,
                   m.movie_name, s.show_date, s.show_time, h.hall_name
            FROM `order` o 
            JOIN user u ON o.user_id = u.user_id
            JOIN item i ON o.id = i.order_id 
            JOIN movies m ON i.product_id = m.movie_id 
            LEFT JOIN showtimes s ON i.showtime_id = s.showtime_id
            LEFT JOIN halls h ON s.hall_id = h.hall_id
            ORDER BY o.order_date DESC";
    
    $result = $db->query($sql);
    
    if (!$result) {
        // Table might not exist yet, return empty array
        return [];
    }
    
    $bookings = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $bookings[] = $row;
        }
    }
    return $bookings;
}

function updateOrderStatus($orderId, $status) {
    $db = getDB();
    $orderId = (int)$orderId;
    $status = $db->real_escape_string($status);
    
    $sql = "UPDATE `order` SET status = '$status' WHERE id = $orderId";
    $result = $db->query($sql);
    
    // If a booking is cancelled, free up its seats so others can book them
    if ($result && $status === 'Cancelled') {
        $db->query("DELETE FROM booked_seats WHERE order_id = $orderId");
    }
    
    return $result;
}

// ---------- UTILITY FUNCTIONS ----------

function getRatingCode($ratingId) {
    $db = getDB();
    $ratingId = (int)$ratingId;
    $result = $db->query("SELECT rating_code FROM ratings WHERE rating_id = $ratingId");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['rating_code'];
    }
    return 'N/A';
}

function getAllRatings() {
    $db = getDB();
    $result = $db->query("SELECT * FROM ratings ORDER BY rating_code");
    $ratings = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ratings[] = $row;
        }
    }
    return $ratings;
}

function getAllGenres() {
    $db = getDB();
    $result = $db->query("SELECT * FROM genres ORDER BY genre_name");
    $genres = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    return $genres;
}

function getMovieGenres($movieId) {
    $db = getDB();
    $movieId = $db->real_escape_string($movieId);
    
    $result = $db->query("SELECT g.genre_id, g.genre_name 
                          FROM genres g 
                          JOIN movie_genres mg ON g.genre_id = mg.genre_id 
                          WHERE mg.movie_id = '$movieId'");
    
    $genres = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $genres[] = $row;
        }
    }
    return $genres;
}

function formatPrice($price) {
    return 'RM ' . number_format($price, 2);
}

function generateMovieId() {
    $db = getDB();
    $result = $db->query("SELECT MAX(CAST(SUBSTRING(movie_id, 2) AS UNSIGNED)) as max_id FROM movies");
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $maxId = $row['max_id'] ?? 0;
        return 'M' . str_pad($maxId + 1, 3, '0', STR_PAD_LEFT);
    }
    return 'M001';
}

function getMovieStatistics() {
    $db = getDB();
    $result = $db->query("SELECT 
        COUNT(*) as total_movies,
        MIN(price) as min_price,
        MAX(price) as max_price,
        AVG(price) as avg_price,
        SUM(price) as total_value
    FROM movies");
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

function getBookingsStatistics() {
    $db = getDB();
    
    // Check if order table exists first
    $checkTable = $db->query("SHOW TABLES LIKE 'order'");
    if (!$checkTable || $checkTable->num_rows == 0) {
        // Table doesn't exist, return null
        return null;
    }
    
    $result = $db->query("SELECT 
        COUNT(*) as total_bookings,
        SUM(total) as total_revenue,
        COUNT(DISTINCT user_id) as unique_customers
    FROM `order`");
    
    if ($result && $result->num_rows > 0) {
        return $result->fetch_assoc();
    }
    return null;
}

// ---------- REDIRECT & MESSAGE FUNCTIONS ----------

function redirect($url) {
    header("Location: $url");
    exit();
}

function setFlashMessage($type, $message) {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        echo '<div class="alert alert-' . $flash['type'] . '">' . $flash['message'] . '</div>';
    }
}
?>