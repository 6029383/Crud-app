<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];
    $thumbnail = $_FILES['thumbnail']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["thumbnail"]["name"]);

    move_uploaded_file($_FILES["thumbnail"]["tmp_name"], $target_file);

    $stmt = $pdo->prepare("INSERT INTO posts (title, content, thumbnail, user_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$title, $content, $target_file, $_SESSION['user_id']]);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nieuwe blogpost maken</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <h1>Nieuwe blogpost maken</h1>
    <form method="post" enctype="multipart/form-data">
        <input type="text" name="title" placeholder="Titel" required>
        <textarea name="content" placeholder="Inhoud" required></textarea>
        <input type="file" name="thumbnail" required>
        <button type="submit">Plaatsen</button>
    </form>
    <a href="dashboard.php">Terug naar dashboard</a>
</body>
</html>