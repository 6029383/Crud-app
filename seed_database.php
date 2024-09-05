<?php
require_once 'config.php';

// Maak tabellen aan
$pdo->exec("
CREATE TABLE IF NOT EXISTS roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL
);

CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role_id INT,
    FOREIGN KEY (role_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS posts (
    id INT PRIMARY KEY AUTO_INCREMENT,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    thumbnail VARCHAR(255) NOT NULL,
    user_id INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
");

// Voeg rollen toe
$roles = [
    ['name' => 'Eigenaar'],
    ['name' => 'Admin'],
    ['name' => 'Gebruiker']
];

$stmt = $pdo->prepare("INSERT INTO roles (name) VALUES (?)");
foreach ($roles as $role) {
    $stmt->execute([$role['name']]);
}

// Voeg gebruikers toe
$users = [
    ['username' => 'eigenaar', 'email' => 'eigenaar@voorbeeld.com', 'password' => 'eigenaar123', 'role_id' => 1],
    ['username' => 'admin', 'email' => 'admin@voorbeeld.com', 'password' => 'admin123', 'role_id' => 2],
    ['username' => 'gebruiker1', 'email' => 'gebruiker1@voorbeeld.com', 'password' => 'gebruiker123', 'role_id' => 3],
    ['username' => 'gebruiker2', 'email' => 'gebruiker2@voorbeeld.com', 'password' => 'gebruiker123', 'role_id' => 3],
    ['username' => 'gebruiker3', 'email' => 'gebruiker3@voorbeeld.com', 'password' => 'gebruiker123', 'role_id' => 3]
];

$stmt = $pdo->prepare("INSERT INTO users (username, email, password, role_id) VALUES (?, ?, ?, ?)");
foreach ($users as $user) {
    $stmt->execute([
        $user['username'],
        $user['email'],
        password_hash($user['password'], PASSWORD_DEFAULT),
        $user['role_id']
    ]);
}

// Voeg blogposts toe
$posts = [];
for ($i = 1; $i <= 20; $i++) {
    $posts[] = [
        'title' => "Blogpost $i",
        'content' => "Dit is de inhoud van blogpost $i. Het bevat interessante informatie over diverse onderwerpen.",
        'thumbnail' => "uploads/thumbnail" . ($i % 5 + 1) . ".jpg", // We gebruiken 5 thumbnails en herhalen ze
        'user_id' => rand(1, count($users)) // Willekeurige gebruiker
    ];
}

$stmt = $pdo->prepare("INSERT INTO posts (title, content, thumbnail, user_id) VALUES (?, ?, ?, ?)");
foreach ($posts as $post) {
    $stmt->execute([
        $post['title'],
        $post['content'],
        $post['thumbnail'],
        $post['user_id']
    ]);
}

echo "Database-tabellen zijn aangemaakt en gevuld met voorbeeldgegevens.";