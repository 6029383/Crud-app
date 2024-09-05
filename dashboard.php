<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ?");
$stmt->execute([$_SESSION['user_id']]);
$posts = $stmt->fetchAll();

$is_admin = isAdmin($pdo, $_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <h1>Dashboard</h1>
    <a href="create_post.php">Nieuwe blogpost maken</a>
    
    <?php if ($is_admin): ?>
    <h2>Beheerdersfuncties</h2>
    <ul>
        <li><a href="manage_users.php">Gebruikers beheren</a></li>
        <li><a href="manage_all_posts.php">Alle posts beheren</a></li>
    </ul>
    <?php endif; ?>

    <h2>Mijn blogposts</h2>
    <ul>
        <?php foreach ($posts as $post): ?>
            <li>
                <?php echo htmlspecialchars($post['title']); ?>
                <a href="edit_post.php?id=<?php echo $post['id']; ?>">Bewerken</a>
                <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Weet u zeker dat u deze post wilt verwijderen?')">Verwijderen</a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="index.php">Terug naar homepage</a>
    <a href="logout.php">Uitloggen</a>
</body>
</html>