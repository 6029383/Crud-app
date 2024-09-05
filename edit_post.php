<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$id = $_GET['id'];
$is_admin = isAdmin($pdo, $_SESSION['user_id']);

if ($is_admin) {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ?");
    $stmt->execute([$id]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE id = ? AND user_id = ?");
    $stmt->execute([$id, $_SESSION['user_id']]);
}

$post = $stmt->fetch();

if (!$post) {
    die("Post niet gevonden of geen toestemming om te bewerken");
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $stmt = $pdo->prepare("UPDATE posts SET title = ?, content = ? WHERE id = ?");
    $stmt->execute([$title, $content, $id]);

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blogpost bewerken</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <h1>Blogpost bewerken</h1>
    <form method="post">
        <input type="text" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
        <textarea name="content" required><?php echo htmlspecialchars($post['content']); ?></textarea>
        <button type="submit">Bijwerken</button>
    </form>
    <a href="dashboard.php">Terug naar dashboard</a>
</body>
</html>