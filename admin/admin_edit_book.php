<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';
$book = null;

// Fetch book details if ID is provided
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
    $stmt->execute([$id]);
    $book = $stmt->fetch();
    
    if (!$book) {
        die("Book not found.");
    }
}

// Handle Update Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_book'])) {
    $id = (int)$_POST['id'];
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = $_POST['category_id'];
    $image_url = trim($_POST['image_url']);
    $pdf_url = trim($_POST['existing_pdf_url']); // Default to existing

    // Handle File Upload if provided
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['pdf_file']['tmp_name'];
        $fileName = $_FILES['pdf_file']['name'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('pdf');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = __DIR__ . '/../uploads/pdfs/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                $pdf_url = 'uploads/pdfs/' . $newFileName;
            } else {
                $message = 'Error moving uploaded file.';
            }
        } else {
            $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    } elseif (!empty($_POST['pdf_url_input'])) {
        // Handle URL Input (only if file not uploaded)
        $pdf_url = trim($_POST['pdf_url_input']);
    }

    if (!empty($title) && !empty($author)) {
        try {
            $stmt = $pdo->prepare("UPDATE books SET title = ?, author = ?, category_id = ?, image_url = ?, pdf_url = ? WHERE id = ?");
            $stmt->execute([$title, $author, $category_id, $image_url, $pdf_url, $id]);
            $message = "Book updated successfully!";
            // Refresh book data
            $stmt = $pdo->prepare("SELECT * FROM books WHERE id = ?");
            $stmt->execute([$id]);
            $book = $stmt->fetch();
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

$categories = $pdo->query("SELECT * FROM categories")->fetchAll();
include '../includes/header.php';
?>

<div class="admin-container">
    <h2>Edit Book</h2>
    <p style="color: green;"><?php echo $message; ?></p>
    
    <div style="margin-bottom: 20px;">
        <a href="admin_books.php" class="btn" style="background-color: #7f8c8d;">&larr; Back to Books</a>
    </div>

    <?php if ($book): ?>
    <div class="admin-form-container">
        <form action="admin_edit_book.php?id=<?php echo $book['id']; ?>" method="POST" enctype="multipart/form-data" class="admin-form-grid">
            <input type="hidden" name="id" value="<?php echo $book['id']; ?>">
            <input type="hidden" name="existing_pdf_url" value="<?php echo htmlspecialchars($book['pdf_url'] ?? ''); ?>">

            <input type="text" name="title" value="<?php echo htmlspecialchars($book['title']); ?>" placeholder="Book Title" required>
            <input type="text" name="author" value="<?php echo htmlspecialchars($book['author']); ?>" placeholder="Author" required>
            
            <select name="category_id" style="padding: 12px; border: 1px solid #eee; border-radius: 8px;">
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>" <?php echo $book['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" name="image_url" value="<?php echo htmlspecialchars($book['image_url']); ?>" placeholder="Image URL">

            <!-- PDF Section -->
            <div class="form-group-full-width" style="border-top: 1px solid #eee; padding-top: 15px; margin-top: 10px;">
                <p><strong>Current PDF:</strong> <a href="<?php echo htmlspecialchars($book['pdf_url'] ?? '#'); ?>" target="_blank" style="color: var(--accent-color);"><?php echo htmlspecialchars($book['pdf_url'] ?? 'None'); ?></a></p>
                
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">Change PDF Source:</label>
                <div class="radio-group">
                    <div>
                        <input type="radio" id="source_keep" name="pdf_source" value="keep" checked onchange="togglePdfInput()">
                        <label for="source_keep">Keep Current</label>
                    </div>
                    <div>
                        <input type="radio" id="source_file" name="pdf_source" value="file" onchange="togglePdfInput()">
                        <label for="source_file">Upload New File</label>
                    </div>
                    <div>
                        <input type="radio" id="source_url" name="pdf_source" value="url" onchange="togglePdfInput()">
                        <label for="source_url">New URL</label>
                    </div>
                </div>

                <div id="pdf_file_container" style="display: none; flex-direction: column;">
                    <label for="pdf_file">Upload New PDF:</label>
                    <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" style="padding: 10px;">
                </div>

                <div id="pdf_url_container" style="display: none; flex-direction: column;">
                    <label for="pdf_url_input">New PDF URL:</label>
                    <input type="url" name="pdf_url_input" id="pdf_url_input" placeholder="https://example.com/book.pdf" style="padding: 10px; border: 1px solid #ccc;">
                </div>
            </div>

            <button type="submit" name="update_book" class="btn btn-full-width">Update Book</button>
        </form>
    </div>

    <script>
        function togglePdfInput() {
            const source = document.querySelector('input[name="pdf_source"]:checked').value;
            const fileContainer = document.getElementById('pdf_file_container');
            const urlContainer = document.getElementById('pdf_url_container');
            
            fileContainer.style.display = 'none';
            urlContainer.style.display = 'none';

            if (source === 'file') {
                fileContainer.style.display = 'flex';
            } else if (source === 'url') {
                urlContainer.style.display = 'flex';
            }
        }
    </script>
    <?php endif; ?>
</div>

<?php include '../includes/footer.php'; ?>
