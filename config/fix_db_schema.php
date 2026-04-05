<?php
require_once 'db.php';

try {
    echo "Checking schema...<br>";
    
    // Check if column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM books LIKE 'pdf_url'");
    $exists = $stmt->fetch();

    if ($exists) {
        echo "Column 'pdf_url' already exists.<br>";
    } else {
        echo "Column 'pdf_url' missing. Adding it...<br>";
        $pdo->exec("ALTER TABLE books ADD COLUMN pdf_url VARCHAR(255) AFTER image_url");
        echo "Column 'pdf_url' added successfully.<br>";
    }

    echo "Current Columns in `books`: <br>";
    $columns = $pdo->query("SHOW COLUMNS FROM books")->fetchAll();
    echo "<pre>";
    print_r($columns);
    echo "</pre>";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
