<?php
require_once '../config/db.php';
session_start();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$message = '';

// Handle Add Book
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_book'])) {
    $title = trim($_POST['title']);
    $author = trim($_POST['author']);
    $category_id = $_POST['category_id'];
    $image_url = trim($_POST['image_url']);
    
    // File Upload Logic
    $pdf_url = '';
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['pdf_file']['tmp_name'];
        $fileName = $_FILES['pdf_file']['name'];
        $fileSize = $_FILES['pdf_file']['size'];
        $fileType = $_FILES['pdf_file']['type'];
        $fileNameCmps = explode(".", $fileName);
        $fileExtension = strtolower(end($fileNameCmps));

        $allowedfileExtensions = array('pdf');
        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            
            // Use absolute path for upload directory
            $uploadFileDir = __DIR__ . '/../uploads/pdfs/';
            
            // Create directory if it doesn't exist
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;

            if(move_uploaded_file($fileTmpPath, $dest_path)) {
                // Store relative path in database for portability
                $pdf_url = 'uploads/pdfs/' . $newFileName;
            } else {
                $message = 'Error moving uploaded file. Check permissions for ' . $uploadFileDir;
            }
        } else {
            $message = 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions);
        }
    } elseif (!empty($_POST['pdf_url_input'])) {
        // Handle URL Input
        $pdf_url = trim($_POST['pdf_url_input']);
    } else {
        // Fallback or error if no file uploaded and no URL
        $message = 'Please upload a PDF file or enter a valid URL.';
    }

    if (!empty($title) && !empty($author) && !empty($pdf_url)) {
        try {
            $stmt = $pdo->prepare("INSERT INTO books (title, author, pdf_url, category_id, image_url) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $author, $pdf_url, $category_id, $image_url]);
            $message = "Book added successfully!";
        } catch (PDOException $e) {
            $message = "Error: " . $e->getMessage();
        }
    }
}

// Handle Delete Book
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    try {
        $pdo->prepare("DELETE FROM books WHERE id = ?")->execute([$id]);
        $message = "Book deleted successfully!";
    } catch (PDOException $e) {
        $message = "Error: " . $e->getMessage();
    }
}

// Fetch Books and Categories
$books = $pdo->query("SELECT books.*, categories.name as category_name FROM books LEFT JOIN categories ON books.category_id = categories.id ORDER BY created_at DESC")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

include '../includes/header.php';
?>

<div class="admin-container">
    <h2>Manage Books</h2>
    <p style="color: green;"><?php echo $message; ?></p>

    <!-- Add Book Form -->
    <div class="admin-form-container">
        <h3>Add New Book</h3>
        <form action="admin_books.php" method="POST" enctype="multipart/form-data" class="admin-form-grid">
            <input type="text" name="title" placeholder="Book Title" required>
            <input type="text" name="author" placeholder="Author" required>
            <!-- PDF Source Selection -->
            <div class="form-group-full-width">
                <label style="font-weight: bold; display: block; margin-bottom: 10px;">PDF Source:</label>
                <div class="radio-group">
                    <div>
                        <input type="radio" id="source_file" name="pdf_source" value="file" checked onchange="togglePdfInput()">
                        <label for="source_file">Upload File</label>
                    </div>
                    <div>
                        <input type="radio" id="source_url" name="pdf_source" value="url" onchange="togglePdfInput()">
                        <label for="source_url">External URL</label>
                    </div>
                </div>
            </div>

            <!-- File Upload Input -->
            <div id="pdf_file_container" style="display: flex; flex-direction: column;">
                <label for="pdf_file" style="margin-bottom: 5px; font-weight: bold;">Upload PDF:</label>
                <input type="file" name="pdf_file" id="pdf_file" accept=".pdf" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <!-- URL Input -->
            <div id="pdf_url_container" style="display: none; flex-direction: column;">
                <label for="pdf_url_input" style="margin-bottom: 5px; font-weight: bold;">PDF URL:</label>
                <input type="url" name="pdf_url_input" id="pdf_url_input" placeholder="https://example.com/book.pdf" style="padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
            </div>

            <script>
                function togglePdfInput() {
                    const source = document.querySelector('input[name="pdf_source"]:checked').value;
                    const fileContainer = document.getElementById('pdf_file_container');
                    const urlContainer = document.getElementById('pdf_url_container');
                    const fileInput = document.getElementById('pdf_file');
                    const urlInput = document.getElementById('pdf_url_input');

                    if (source === 'file') {
                        fileContainer.style.display = 'flex';
                        urlContainer.style.display = 'none';
                        fileInput.required = true;
                        urlInput.required = false;
                        urlInput.value = ''; // Clear URL if switching to file
                    } else {
                        fileContainer.style.display = 'none';
                        urlContainer.style.display = 'flex';
                        fileInput.required = false;
                        urlInput.required = true;
                        fileInput.value = ''; // Clear file if switching to URL
                    }
                }
            </script>
            <select name="category_id" style="padding: 12px; border: 1px solid #eee; border-radius: 8px;">
                <option value="">Select Category</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name']); ?></option>
                <?php endforeach; ?>
            </select>
            <input type="text" name="image_url" placeholder="Image URL (e.g., img/book1.jpg)">
            <button type="submit" name="add_book" class="btn btn-full-width">Add Book</button>
        </form>
    </div>

    <!-- Books List -->
    <div class="table-container">
        <h3>Existing Books</h3>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="text-align: left; background: #f4f4f4;">
                    <th style="padding: 10px;">ID</th>
                    <th style="padding: 10px;">Title</th>
                    <th style="padding: 10px;">Author</th>
                    <th style="padding: 10px;">Category</th>
                    <th style="padding: 10px;">PDF URL</th>
                    <th style="padding: 10px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($books as $book): ?>
                    <tr style="border-bottom: 1px solid #eee;">
                        <td style="padding: 10px;"><?php echo $book['id']; ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($book['title']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($book['author']); ?></td>
                        <td style="padding: 10px;"><?php echo htmlspecialchars($book['category_name'] ?? 'N/A'); ?></td>
                        <td style="padding: 10px;">
                            <a href="<?php echo htmlspecialchars($book['pdf_url']); ?>" target="_blank" style="color: var(--accent-color);">View PDF</a>
                        </td>
                        <td style="padding: 10px;">
                            <a href="admin_edit_book.php?id=<?php echo $book['id']; ?>" style="color: var(--primary-color); margin-right: 10px;">Edit</a>
                            <a href="admin_books.php?delete=<?php echo $book['id']; ?>" style="color: red;" onclick="return confirm('Are you sure?')">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include '../includes/footer.php'; ?>
