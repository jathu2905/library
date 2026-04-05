<?php
require_once 'config/db.php';

$query = isset($_GET['q']) ? trim($_GET['q']) : '';
$books = [];
$search_performed = false;

if (!empty($query)) {
    $search_performed = true;
    try {
        $stmt = $pdo->prepare("
            SELECT books.*, categories.name as category_name 
            FROM books 
            LEFT JOIN categories ON books.category_id = categories.id 
            WHERE books.title LIKE ? OR books.author LIKE ?
            ORDER BY books.created_at DESC
        ");
        $like_query = '%' . $query . '%';
        $stmt->execute([$like_query, $like_query]);
        $books = $stmt->fetchAll();
    } catch (PDOException $e) {
        // Handle error silently or log
    }
}

include 'includes/header.php';
?>

    <main>
        <section class="category-books" style="min-height: 50vh; padding: 40px 20px;">
            <h2 style="text-align: center; margin-bottom: 30px;">Search Results for "<?php echo htmlspecialchars($query); ?>"</h2>
            <div class="book-grid">
                <?php if ($search_performed && count($books) > 0): ?>
                    <?php foreach ($books as $book): ?>
                        <div class="book-card">
                            <?php 
                            $imgSrc = BASE_URL . "assets/img/default-book.png";
                            if (!empty($book['image_url'])) {
                                if (strpos($book['image_url'], 'http') === 0) {
                                    $imgSrc = $book['image_url'];
                                } else {
                                    $imgSrc = BASE_URL . $book['image_url'];
                                }
                            }
                            ?>
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($book['title']); ?>">
                            <h3><?php echo htmlspecialchars($book['title']); ?></h3>
                            <p class="author">By <?php echo htmlspecialchars($book['author']); ?></p>
                            <p class="category"><?php echo htmlspecialchars($book['category_name'] ?? 'N/A'); ?></p>
                            <?php if (!empty($book['pdf_url'])): ?>
                                <a href="<?php echo htmlspecialchars(strpos($book['pdf_url'], 'http') === 0 ? $book['pdf_url'] : BASE_URL . $book['pdf_url']); ?>" target="_blank" class="btn">Read Book</a>
                            <?php else: ?>
                                <p class="unavailable">PDF Unavailable</p>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                <?php elseif ($search_performed): ?>
                    <p style="text-align: center; width: 100%; grid-column: 1 / -1; font-size: 1.2rem; color: #555;">No books found matching your query.</p>
                <?php else: ?>
                    <p style="text-align: center; width: 100%; grid-column: 1 / -1; font-size: 1.2rem; color: #555;">Please enter a search term.</p>
                <?php endif; ?>
            </div>
        </section>
    </main>

<?php include 'includes/footer.php'; ?>
