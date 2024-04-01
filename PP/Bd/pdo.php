<?php
$pdo = new PDO('mysql:host=localhost;dbname=gradebook', 'Admin', '12345');
define('USER', 'Admin');
define('PASSWORD', '12345');
define('HOST', 'localhost');
define('DATABASE', 'gradebook');
try {
    $connection = new PDO("mysql:host=".HOST.";dbname=".DATABASE, USER, PASSWORD);
} catch (PDOException $e) {
    echo $e->getMessage();
    exit("Error: " . $e->getMessage());
}
$stmt = $connection->prepare("SELECT * FROM users");
$stmt->execute();
?>