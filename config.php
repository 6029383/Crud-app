<?php
$host = 'localhost';
$dbname = 'blog_db';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Databaseverbinding mislukt: " . $e->getMessage());
}

function getUserRole($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT role_id FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    return $result['role_id'];
}

function isAdmin($pdo, $user_id) {
    $role_id = getUserRole($pdo, $user_id);
    return $role_id == 1 || $role_id == 2; // 1 voor eigenaar, 2 voor admin
}