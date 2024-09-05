<?php
require_once 'config.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die("Post niet gevonden");
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <img src="<?php echo htmlspecialchars($post['thumbnail']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
    <div><?php echo nl2br(htmlspecialchars($post['content'])); ?></div>
    <a href="index.php">Terug naar homepage</a>
</body>
</html>