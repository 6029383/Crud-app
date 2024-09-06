<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Post.php';
require_once 'classes/User.php';

$db = new Database();
$post = new Post($db);
$user = new User($db);

// Controleer of de gebruiker is ingelogd
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Controleer of er een post ID is meegegeven
if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit();
}

$post_id = $_GET['id'];
$post_data = $post->getPostById($post_id);

// Controleer of de post bestaat en of de gebruiker rechten heeft om deze te bewerken
if (!$post_data || !$user->canEditPost($_SESSION['role'], $post_data['user_id'], $_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

// Verwerk het formulier als het is verzonden
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $content = $_POST['content'];

    $post->updatePost($post_id, $title, $content);
    header('Location: dashboard.php');
    exit();
}

// ... (rest van de HTML-code blijft hetzelfde)
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bewerk Post - Mijn Blog</title>
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
        <div class="form-container">
            <h2>Bewerk Post</h2>
            <form action="edit_post.php?id=<?php echo $post_id; ?>" method="post">
                <div class="form-group">
                    <label for="title">Titel:</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($post['title']); ?>" required>
                </div>
                <div class="form-group">
                    <label for="content">Inhoud:</label>
                    <textarea id="content" name="content" rows="10" required><?php echo htmlspecialchars($post['content']); ?></textarea>
                </div>
                <button type="submit" class="btn">Opslaan</button>
            </form>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Mijn Blog</p>
    </footer>
</body>
</html>