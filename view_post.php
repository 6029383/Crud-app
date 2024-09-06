<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Post.php';

$db = new Database();
$post = new Post($db);

// Controleer of er een post ID is meegegeven
if (!isset($_GET['id'])) {
    header('Location: index.php');
    exit();
}

$post_id = $_GET['id'];
$post_data = $post->getPostById($post_id);

// Als de post niet bestaat, redirect naar de homepage
if (!$post_data) {
    header('Location: index.php');
    exit();
}

?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post_data['title']); ?> - Mijn Blog</title>
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
        <a href="index.php" class="back-to-home">Terug naar home</a>
        <article class="post-full">
            <h2><?php echo htmlspecialchars($post_data['title']); ?></h2>
            <div class="post-meta">
                <span>Door: <?php echo htmlspecialchars($post_data['username']); ?></span>
                <span>Geplaatst op: <?php echo date('d-m-Y H:i', strtotime($post_data['created_at'])); ?></span>
            </div>
            <img src="https://picsum.photos/800/400?random=<?php echo $post_data['id']; ?>" alt="<?php echo htmlspecialchars($post_data['title']); ?>" class="post-image">
            <div class="post-content">
                <?php echo nl2br(htmlspecialchars($post_data['content'])); ?>
            </div>
        </article>
    </main>
</body>
</html>