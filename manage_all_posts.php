<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isAdmin($pdo, $_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id");
$posts = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alle posts beheren</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <h1>Alle posts beheren</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Titel</th>
            <th>Auteur</th>
            <th>Datum</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($posts as $post): ?>
        <tr>
            <td><?php echo $post['id']; ?></td>
            <td><?php echo htmlspecialchars($post['title']); ?></td>
            <td><?php echo htmlspecialchars($post['username']); ?></td>
            <td><?php echo $post['created_at']; ?></td>
            <td>
                <a href="edit_post.php?id=<?php echo $post['id']; ?>">Bewerken</a>
                <a href="delete_post.php?id=<?php echo $post['id']; ?>" onclick="return confirm('Weet u zeker dat u deze post wilt verwijderen?')">Verwijderen</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="dashboard.php">Terug naar dashboard</a>
</body>
</html>