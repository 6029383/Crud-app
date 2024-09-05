<?php
session_start();
require_once 'config.php';

// Controleer of de gebruiker is ingelogd en de juiste rol heeft
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Eigenaar' && $_SESSION['role'] != 'Admin')) {
    header("Location: index.php");
    exit();
}

// Haal alle gebruikers op
$stmt = $pdo->query("SELECT users.*, roles.name as role_name FROM users JOIN roles ON users.role_id = roles.id");
$users = $stmt->fetchAll();

// Verwijder gebruiker
if (isset($_POST['delete_user'])) {
    $user_id = $_POST['delete_user'];
    $user_role = $_POST['user_role'];

    // Controleer of de huidige gebruiker de juiste rechten heeft om te verwijderen
    if ($_SESSION['role'] == 'Eigenaar' || ($_SESSION['role'] == 'Admin' && $user_role == 'Gebruiker')) {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        header("Location: user_management.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruikersbeheer - Mijn Blog</title>
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
            <h2>Gebruikersbeheer</h2>
            <table>
                <thead>
                    <tr>
                        <th>Gebruikersnaam</th>
                        <th>E-mail</th>
                        <th>Rol</th>
                        <th>Actie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($user['username']); ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo htmlspecialchars($user['role_name']); ?></td>
                            <td>
                                <?php if ($_SESSION['role'] == 'Eigenaar' || ($_SESSION['role'] == 'Admin' && $user['role_name'] == 'Gebruiker')): ?>
                                    <form method="post" onsubmit="return confirm('Weet je zeker dat je deze gebruiker wilt verwijderen?');">
                                        <input type="hidden" name="delete_user" value="<?php echo $user['id']; ?>">
                                        <input type="hidden" name="user_role" value="<?php echo $user['role_name']; ?>">
                                        <button type="submit" class="btn btn-danger">Verwijderen</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>
    
    <footer>
        <p>&copy; <?php echo date('Y'); ?> Mijn Blog</p>
    </footer>
</body>
</html>