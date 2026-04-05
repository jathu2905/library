<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name']);
    $image_url = trim($_POST['image_url']);

    if (!empty($name)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, image_url) VALUES (?, ?)");
            $stmt->execute([$name, $image_url]);
            $message = "Category added successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        // Optional: Check if books exist in this category before deleting (or let foreign key fail/cascade depending on setup)
        // Here we just try to delete
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        $message = "Category deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error (Ensure no books are in this category first): " . $e->getMessage();
    }
}

// Fetch Categories
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

include '../includes/header.php';
?>

<div class="admin-container">
    <h2>Manage Categories</h2>
    <p style="color: green;"><?php echo $message; ?></p>

    <!-- Add Category Form -->
    <div class="admin-form-container">
        <h3>Add New Category</h3>
        <form action="admin_categories.php" method="POST" class="admin-form-grid">
            <input type="text" name="name" placeholder="Category Name" required>
            <input type="text" name="image_url" placeholder="Image URL (e.g., img/cat1.jpg)">
            <button type="submit" name="add_category" class="btn btn-full-width">Add Category</button>
        </form>
    </div>

    <!-- Category List -->
    <div class="table-container">
        <h3>Existing Categories</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; background: #f4f4f4;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Name</th>
                    <th style="padding: 10px;">Image URL</th>
                    <th style="padding: 10px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?php echo $cat['id']; ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($cat['name']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($cat['image_url']); ?></td>
                        <td style="padding: 10px;">
                            <a href="admin_categories.php?delete=<?php echo $cat['id']; ?>" style="color: red;" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
