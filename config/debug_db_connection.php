<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Database Connection Debugger</h2>";

$hosts = ['127.0.0.1', 'localhost'];
$ports = [3306, 3307, 3308];
$user = 'root';
$pass = ''; // Try empty password
$db = 'book_store';

foreach ($hosts as $host) {
    foreach ($ports as $port) {
        testConnection($host, $port, $db, $user, $pass);
    }
}

function testConnection($host, $port, $db, $user, $pass) {
    echo "<hr>Testing <strong>$host:$port</strong>... <br>";
    try {
        $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
        $options = [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];
        $pdo = new PDO($dsn, $user, $pass, $options);
        echo "<span style='color:green; font-weight:bold;'>SUCCESS! Connected to $host:$port</span><br>";
        
        // Check database content
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        echo "User count: " . $stmt->fetchColumn() . "<br>";
        
    } catch (PDOException $e) {
        echo "<span style='color:red;'>FAILED: " . $e->getMessage() . "</span><br>";
    }
}
?>
