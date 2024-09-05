<?php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || !isAdmin($pdo, $_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$stmt = $pdo->query("SELECT * FROM users");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gebruikers beheren</title>
    <link rel="stylesheet" href="styles/main.css">
</head>
<body>
    <h1>Gebruikers beheren</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Gebruikersnaam</th>
            <th>E-mail</th>
            <th>Rol</th>
            <th>Acties</th>
        </tr>
        <?php foreach ($users as $user): ?>
        <tr>
            <td><?php echo $user['id']; ?></td>
            <td><?php echo htmlspecialchars($user['username']); ?></td>
            <td><?php echo htmlspecialchars($user['email']); ?></td>
            <td><?php echo $user['role_id']; ?></td>
            <td>
                <a href="edit_user.php?id=<?php echo $user['id']; ?>">Bewerken</a>
                <a href="delete_user.php?id=<?php echo $user['id']; ?>" onclick="return confirm('Weet u zeker dat u deze gebruiker wilt verwijderen?')">Verwijderen</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <a href="dashboard.php">Terug naar dashboard</a>
</body>
</html>