<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';

$db = new Database();
$user = new User($db);

// Controleer of de gebruiker is ingelogd en de juiste rol heeft
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] != 'Eigenaar' && $_SESSION['role'] != 'Admin')) {
    header("Location: index.php");
    exit();
}

$users = $user->getAllUsers();
$roles = $user->getAllRoles();  // Haal alle rollen op

// Verwijder gebruiker
if (isset($_POST['delete_user'])) {
    $userId = $_POST['delete_user'];
    $userRole = $_POST['user_role'];

    // Controleer of de huidige gebruiker de juiste rechten heeft om te verwijderen
    if ($_SESSION['role'] == 'Eigenaar' || ($_SESSION['role'] == 'Admin' && $userRole == 'Gebruiker')) {
        $user->deleteUser($userId);
        header("Location: user_management.php");
        exit();
    }
}

// Wijzig gebruikersrol
if (isset($_POST['change_role']) && $_SESSION['role'] == 'Eigenaar') {
    $userId = $_POST['user_id'];
    $newRoleId = $_POST['new_role'];

    $user->changeUserRole($userId, $newRoleId);
    header("Location: user_management.php");
    exit();
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
    <style>
        .role-select {
            padding: 0.5rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius);
            background-color: #fff;
            font-size: 1rem;
            color: var(--text-color);
            cursor: pointer;
            transition: border-color 0.3s ease;
        }
        .role-select:hover, .role-select:focus {
            border-color: var(--primary-color);
            outline: none;
        }
        .inline-form {
            display: inline-block;
        }
    </style>
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
                            <td>
                                <?php if ($_SESSION['role'] == 'Eigenaar' && $user['id'] != $_SESSION['user_id']): ?>
                                    <form method="post" class="inline-form">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <select name="new_role" onchange="this.form.submit()" class="role-select">
                                            <?php foreach ($roles as $role): ?>
                                                <option value="<?php echo $role['id']; ?>" <?php echo $user['role_id'] == $role['id'] ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($role['name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <input type="hidden" name="change_role" value="1">
                                    </form>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($user['role_name']); ?>
                                <?php endif; ?>
                            </td>
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
</body>
</html>