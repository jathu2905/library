CREATE DATABASE IF NOT EXISTS book_store;
USE book_store;

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

-- Insert dummy data for categories
INSERT INTO categories (name, image_url) VALUES 
('Fiction', 'assets/img/img5.webp'),
('Non-fiction', 'assets/img/img4.webp'),
('Science', 'assets/img/img1.jpg'),
('Fantasy', 'assets/img/img2.webp');

-- Insert default admin user (Password: admin123)
-- Hash generated using password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, email, role) VALUES 
('admin', '$2y$10$8.X/Jq.8.X/Jq.8.X/JqeExampleHashPlaceHolder...', 'admin@bookstore.com', 'admin');
