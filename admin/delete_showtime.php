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

if (deleteShowtime($showtimeId)) {
    setFlashMessage('success', 'Showtime deleted successfully!');
} else {
    setFlashMessage('danger', 'Failed to delete showtime.');
}

redirect('showtimes.php');
?>