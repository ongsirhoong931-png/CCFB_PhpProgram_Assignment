<?php
require_once 'includes/functions.php';
logoutUser();
setFlashMessage('success', 'You have been logged out.');
redirect('index.php');
?>