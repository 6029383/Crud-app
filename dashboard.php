<?php
session_start();
require_once 'config.php';

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Haal de posts op afhankelijk van de gebruikersrol
if ($_SESSION['role'] == 'Eigenaar') {
    $stmt = $pdo->query("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id ORDER BY created_at DESC");
} elseif ($_SESSION['role'] == 'Admin') {
    $stmt = $pdo->prepare("SELECT posts.*, users.username FROM posts JOIN users ON posts.user_id = users.id WHERE posts.user_id = ? OR users.role_id = 3 ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
} else {
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
}
$posts = $stmt->fetchAll();

// Verwijder post
if (isset($_POST['delete_post'])) {
    $post_id = $_POST['delete_post'];
    $post_user_id = $_POST['post_user_id'];

    if ($_SESSION['role'] == 'Eigenaar' || ($_SESSION['role'] == 'Admin' && $post_user_id == $_SESSION['user_id']) || ($_SESSION['role'] == 'Gebruiker' && $post_user_id == $_SESSION['user_id'])) {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Mijn Blog</title>
    <link rel="stylesheet" href="styles/main.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
</head>
<body>
    <header>
        <div class="header-content">
            <h1>Mijn Blog</h1>
            <nav>
                <a href="index.php">Home</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="dashboard.php">Dashboard</a>
                    <?php if ($_SESSION['role'] == 'Eigenaar' || $_SESSION['role'] == 'Admin'): ?>
                        <a href="user_management.php">Gebruikers</a>
                    <?php endif; ?>
                    <div class="user-dropdown">
                        <div class="user-info">
                            <img src="https://picsum.photos/40" alt="Profielfoto" class="avatar">
                            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </div>
                        <div class="user-dropdown-content">
                            <a href="logout.php">Uitloggen</a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="login.php">Inloggen</a>
                    <a href="register.php">Registreren</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main>
        <div class="dashboard">
            <h2>Mijn Posts</h2>
            <ul class="post-list">
                <?php foreach ($posts as $post): ?>
                    <li>
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <?php if (isset($post['username'])): ?>
                            <p>Auteur: <?php echo htmlspecialchars($post['username']); ?></p>
                        <?php endif; ?>
                        <div class="post-actions">
                            <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn">Bewerken</a>
                            <?php if ($_SESSION['role'] == 'Eigenaar' || ($_SESSION['role'] == 'Admin' && $post['user_id'] == $_SESSION['user_id']) || ($_SESSION['role'] == 'Gebruiker' && $post['user_id'] == $_SESSION['user_id'])): ?>
                                <form method="post" style="display: inline;" onsubmit="return confirm('Weet je zeker dat je deze post wilt verwijderen?');">
                                    <input type="hidden" name="delete_post" value="<?php echo $post['id']; ?>">
                                    <input type="hidden" name="post_user_id" value="<?php echo $post['user_id']; ?>">
                                    <button type="submit" class="btn btn-danger">Verwijderen</button>
                                </form>
                            <?php endif; ?>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </main>
</body>
</html>