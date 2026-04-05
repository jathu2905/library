<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Online Book Store</title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>assets/css/style.css">
    <link rel="stylesheet" href="https://unpkg.com/boxicons@latest/css/boxicons.min.css">
</head>
<body>
    <header>
        <nav class="navbar">
            <div class="logo">
                <h2><span class="book">Book</span><span class="store">Store</span></h2>
            </div>
            <ul class="navmenu">
                <li><a href="<?php echo BASE_URL; ?>index.php" class="nav-link">Home</a></li>
                <li><a href="<?php echo BASE_URL; ?>categories.php" class="nav-link">Categories</a></li>
                <li><a href="<?php echo BASE_URL; ?>contact.php" class="nav-link">Contact</a></li>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
                        <li><a href="<?php echo BASE_URL; ?>admin/admin_dashboard.php" class="nav-link" style="color: var(--accent-color);">Admin Panel</a></li>
                    <?php endif; ?>
                    <li><a href="<?php echo BASE_URL; ?>logout.php" class="nav-link">Logout (<?php echo htmlspecialchars($_SESSION['username']); ?>)</a></li>
                <?php else: ?>
                    <li><a href="<?php echo BASE_URL; ?>login.php" class="nav-link">Login</a></li>
                <?php endif; ?>
            </ul>
            <div class="search-box">
                <form action="<?php echo BASE_URL; ?>search.php" method="GET" style="display: flex; align-items: center; width: 100%; height: 100%;">
                    <input type="text" name="q" placeholder="Search..." class="search" style="border: none; outline: none; background: transparent; padding-right: 10px;">
                    <button type="submit" style="background: none; border: none; cursor: pointer; color: var(--text-color); display: flex; align-items: center;">
                        <i class='bx bx-search-alt'></i>
                    </button>
                </form>
            </div>
            <div class="menu-toggle" id="mobile-menu">
                <i class='bx bx-menu'></i>
            </div>
        </nav>
    </header>
