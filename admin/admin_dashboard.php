<?php
require_once '../config/db.php';
session_start();

// Check if user is admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch stats
$userCount = 0;
$bookCount = 0;
$categoryCount = 0;

try {
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $bookCount = $pdo->query("SELECT COUNT(*) FROM books")->fetchColumn();
    $categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
} catch (PDOException $e) {
    // Handle error
}

include '../includes/header.php';
?>

<div class="admin-container">
    <h2>Admin Dashboard</h2>
    <div class="admin-stats-grid">
        <div class="admin-stats-card">
            <h3>Total Users</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?php echo $userCount; ?></p>
            <a href="admin_users.php" class="btn" style="margin-top: 10px;">Manage Users</a>
        </div>
        <div class="admin-stats-card">
            <h3>Total Books</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?php echo $bookCount; ?></p>
            <a href="admin_books.php" class="btn" style="margin-top: 10px;">Manage Books</a>
        </div>
        <div class="admin-stats-card">
            <h3>Total Categories</h3>
            <p style="font-size: 2rem; font-weight: bold; color: var(--primary-color);"><?php echo $categoryCount; ?></p>
            <a href="admin_categories.php" class="btn" style="margin-top: 10px;">Manage Categories</a>
        </div>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
