<?php
require_once 'config/db.php';

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$category_name = 'Unknown Category';
$books = [];

if ($category_id > 0) {
    try {
        // Get category name
        $stmt = $pdo->prepare("SELECT name FROM categories WHERE id = ?");
        $stmt->execute([$category_id]);
        $category = $stmt->fetch();
        if ($category) {
            $category_name = $category['name'];
        }

        // Get books
        $stmt = $pdo->prepare("SELECT * FROM books WHERE category_id = ?");
        $stmt->execute([$category_id]);
        $books = $stmt->fetchAll();
    } catch (PDOException $e) {
        $error = "Database error: " . $e->getMessage();
    }
}

include 'includes/header.php';
?>

    <main>
        <section class="categories">
            <h2><?php echo htmlspecialchars($category_name); ?></h2>
            
            <?php if (empty($books)): ?>
                <p style="text-align: center;">No books found in this category.</p>
            <?php else: ?>
                <div class="category-grid">
                    <?php foreach ($books as $book): ?>
                        <div class="category-item">
                            <img src="<?php echo htmlspecialchars($book['image_url']); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p><?php echo htmlspecialchars($book['author']); ?></p>
                            <!-- Removed Price Display, Added PDF Button -->
                            <div style="margin-top: 15px;">
                                <a href="<?php echo htmlspecialchars($book['pdf_url'] ?? '#'); ?>" target="_blank" class="btn" style="padding: 10px 20px; font-size: 0.9rem;">
                                    Read / Download
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>
