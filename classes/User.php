<?php
class User {
    private $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function login($username, $password) {
        $stmt = $this->db->getPdo()->prepare("SELECT users.*, roles.name as role_name FROM users JOIN roles ON users.role_id = roles.id WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role_name'];
            return true;
        }
        return false;
    }

    public function logout() {
        session_destroy();
    }

    public function getAllUsers() {
        $stmt = $this->db->getPdo()->query("SELECT users.*, roles.name as role_name FROM users JOIN roles ON users.role_id = roles.id");
        return $stmt->fetchAll();
    }

    public function changeUserRole($userId, $newRoleId) {
        $stmt = $this->db->getPdo()->prepare("UPDATE users SET role_id = ? WHERE id = ?");
        return $stmt->execute([$newRoleId, $userId]);
    }

    public function deleteUser($userId) {
        $stmt = $this->db->getPdo()->prepare("DELETE FROM users WHERE id = ?");
        return $stmt->execute([$userId]);
    }

    public function canDeletePost($userRole, $postUserId, $currentUserId) {
        return $userRole == 'Eigenaar' || 
               ($userRole == 'Admin' && $postUserId == $currentUserId) || 
               ($userRole == 'Gebruiker' && $postUserId == $currentUserId);
    }

    public function getAllRoles() {
        $stmt = $this->db->getPdo()->query("SELECT * FROM roles");
        return $stmt->fetchAll();
    }
}