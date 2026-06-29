<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    setFlashMessage('warning', 'Please login to view your profile.');
    redirect('login.php');
}

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $db = getDB();
    $full_name = $db->real_escape_string($_POST['full_name'] ?? '');
    $phone = $db->real_escape_string($_POST['phone'] ?? '');
    $email = $db->real_escape_string($_POST['email'] ?? '');
    $userId = (int)$_SESSION['user_id'];
    
    $sql = "UPDATE user SET full_name = '$full_name', phone = '$phone', email = '$email' WHERE user_id = $userId";
    
    if ($db->query($sql)) {
        $_SESSION['full_name'] = $full_name;
        setFlashMessage('success', 'Profile updated successfully!');
        redirect('profile.php');
    } else {
        setFlashMessage('danger', 'Failed to update profile.');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?> - Profile</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <div class="logo">🎬 <span>CINEMA</span>BOOKING</div>
            <nav>
                <ul>
                    <li><a href="index.php">Home</a></li>
                    <li><a href="movies.php">Movies</a></li>
                    <li><a href="my-bookings.php">My Bookings</a></li>
                    <li><a href="profile.php" class="active">Profile</a></li>
                    <?php if (isAdmin()): ?>
                        <li><a href="admin/">Admin</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php" class="btn-logout">Logout</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <main class="container">
        <div class="form-container">
            <h2>My Profile</h2>
            <?php displayFlashMessage(); ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" value="<?= htmlspecialchars($user['username']) ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Role</label>
                    <input type="text" value="<?= htmlspecialchars($user['role']) ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label>Account Status</label>
                    <input type="text" value="<?= $user['is_blocked'] ? 'Blocked' : 'Active' ?>" disabled style="color: <?= $user['is_blocked'] ? '#dc3545' : '#28a745' ?>;">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </div>
            </form>
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> <?= SITE_NAME ?>. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>