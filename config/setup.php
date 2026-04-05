<?php
$host = 'localhost';
$user = 'root';
$pass = ''; // Default XAMPP password

try {
    // 1. Connect to MySQL without database
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Connected to MySQL server successfully.<br>";

    // 2. Create Database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS book_store");
    echo "Database 'book_store' created or checked successfully.<br>";

    // 3. Select Database
    $pdo->exec("USE book_store");

    // 4. Create Tables (Users, Categories, Books)
    // We use CREATE TABLE IF NOT EXISTS. 
    // NOTE: This won't modify existing tables if columns changed. 
    // Ideally, for an upgrade, we might want ALTER TABLE commands, but for this setup script we assume fresh or compatible start.
    
    $sql = "
    CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        email VARCHAR(100) NOT NULL UNIQUE,
        role ENUM('user', 'admin') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    CREATE TABLE IF NOT EXISTS categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        image_url VARCHAR(255) NOT NULL
    );

    CREATE TABLE IF NOT EXISTS books (
        id INT AUTO_INCREMENT PRIMARY KEY,
        title VARCHAR(255) NOT NULL,
        author VARCHAR(255) NOT NULL,
        description TEXT,
        category_id INT,
        image_url VARCHAR(255),
        pdf_url VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id)
    );
    ";

    $pdo->exec($sql);
    echo "Tables created successfully.<br>";

    // Attempt to update books table if it exists but lacks pdf_url (Migration logic)
    try {
        $pdo->exec("ALTER TABLE books ADD COLUMN pdf_url VARCHAR(255)");
        echo "Added pdf_url column to books table.<br>";
    } catch (Exception $e) {
        // Column likely exists
    }
    
    try {
        $pdo->exec("ALTER TABLE books DROP COLUMN price");
        echo "Removed price column from books table.<br>";
    } catch (Exception $e) {
        // Column likely doesn't exist
    }


    // 5. Check if we need to insert dummy data (only if categories are empty)
    $stmt = $pdo->query("SELECT COUNT(*) FROM categories");
    if ($stmt->fetchColumn() == 0) {
        $pdo->exec("
            INSERT INTO categories (name, image_url) VALUES 
            ('Fiction', 'assets/img/img5.webp'),
            ('Non-fiction', 'assets/img/img4.webp'),
            ('Science', 'assets/img/img1.jpg'),
            ('Fantasy', 'assets/img/img2.webp');
        ");
        echo "Dummy categories inserted.<br>";
    }

    // 6. Check if admin exists
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = 'admin'");
    $stmt->execute();
    if ($stmt->fetchColumn() == 0) {
        // Create Admin: admin / admin123
        $password = password_hash('admin123', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (username, password, email, role) VALUES ('admin', ?, 'admin@bookstore.com', 'admin')");
        $stmt->execute([$password]);
        echo "Default Admin user created (User: admin, Pass: admin123).<br>";
    }

    echo "<h1>Setup Complete!</h1>";
    echo "<p>You can now <a href='index.php'>Go to Home Page</a> or <a href='login.php'>Login</a>.</p>";

} catch (PDOException $e) {
    die("Setup failed: " . $e->getMessage());
}
?>
