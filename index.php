<?php
require_once 'config/db.php';

// Fetch categories
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = []; // Fallback empty array on error
}

include 'includes/header.php';
?>

    <section class="hero" id="home">
        <div class="hero-content">
            <h1 class="horror-font">Your Favorite Online Book Store</h1>
            <p>Explore the world of books from the comfort of your home</p>
            <a href="categories.php" class="btn">Browse Categories</a>
        </div>
    </section>

    <section class="categories">
        <h2>Categories</h2>
        <div class="category-grid">
            <?php if (!empty($categories)): ?>
                <?php foreach ($categories as $category): ?>
                    <div class="category-item">
                        <a href="category.php?id=<?php echo $category['id']; ?>">
                            <img src="<?php echo htmlspecialchars($category['image_url']); ?>" alt="<?php echo htmlspecialchars($category['name']); ?>">
                            <h3><?php echo htmlspecialchars($category['name']); ?></h3>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <!-- Fallback if database is empty or connection fails (for demonstration) -->
                 <div class="category-item">
                    <a href="fiction.html">
                        <img src="assets/img/img5.webp" alt="Fiction Books">
                        <h3>Fiction</h3>
                    </a>
                </div>
                <div class="category-item">
                    <a href="nonfiction.html">
                        <img src="assets/img/img4.webp" alt="Non-fiction Books">
                        <h3>Non-fiction</h3>
                    </a>
                </div>
                <div class="category-item">
                    <a href="science.html">
                        <img src="assets/img/img1.jpg" alt="Science Books">
                        <h3>Science</h3>
                    </a>
                </div>
                <div class="category-item">
                    <a href="fantasy.html">
                        <img src="assets/img/img2.webp" alt="Fantasy Books">
                        <h3>Fantasy</h3>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </section>

<?php include 'includes/footer.php'; ?>
