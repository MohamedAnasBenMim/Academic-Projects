<?php
$host = 'localhost';
$dbname = 'locavroom';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Ensure 'admin' column exists in 'users' table
    $result = $pdo->query("SHOW COLUMNS FROM users LIKE 'admin'");
    if ($result->rowCount() === 0) {
        $pdo->exec("ALTER TABLE users ADD admin TINYINT(1) DEFAULT 0");
    }
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}
?>