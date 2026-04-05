<?php
require_once 'config/db.php';

// Fetch categories
try {
    $stmt = $pdo->query("SELECT * FROM categories");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    $categories = []; // Fallback
}

include 'includes/header.php';
?>

    <main>
        <section class="categories">
            <h2>All Categories</h2>
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
                    <p>No categories found.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>
