<?php
require_once '../includes/functions.php';

if (!isLoggedIn() || !isAdmin()) {
    setFlashMessage('danger', 'Access denied. Admin only.');
    redirect('../index.php');
}

$orderId = $_GET['id'] ?? '';
$status = $_GET['status'] ?? '';

if (empty($orderId) || empty($status)) {
    setFlashMessage('danger', 'Invalid request.');
    redirect('index.php');
}

if (updateOrderStatus($orderId, $status)) {
    setFlashMessage('success', 'Order status updated successfully!');
} else {
    setFlashMessage('danger', 'Failed to update order status.');
}

redirect('index.php');
?>