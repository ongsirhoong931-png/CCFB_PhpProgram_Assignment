<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$movieId = $_GET['id'] ?? '';
$movie = getMovieById($movieId);

if (!$movie) {
    setFlashMessage('danger', 'Movie not found.');
    redirect('index.php');
}

if (deleteMovie($movieId)) {
    setFlashMessage('success', 'Movie deleted successfully!');
} else {
    setFlashMessage('danger', 'Failed to delete movie.');
}

redirect('index.php');
?>