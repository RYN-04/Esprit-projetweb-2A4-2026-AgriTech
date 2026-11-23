<?php
declare(strict_types=1);

require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/User.php';

// All SQL queries for the "User" table (prepared statements only)
class UserController
{
    // Add a user. The password is hashed here, never stored in plain text.
    public function addUser(User $user): void
    {
        $sql = 'INSERT INTO User (nom, prenom, email, password, role, statut)
                VALUES (:nom, :prenom, :email, :password, :role, :statut)';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([
            'nom'      => $user->getNom(),
            'prenom'   => $user->getPrenom(),
            'email'    => $user->getEmail(),
            'password' => password_hash($user->getPassword(), PASSWORD_DEFAULT),
            'role'     => $user->getRole(),
            'statut'   => $user->getStatut(),
        ]);
    }

    // List all users
    public function listUsers(): array
    {
        $sql = 'SELECT id_user, nom, prenom, email, role, statut FROM User ORDER BY nom, prenom';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute();
        return $query->fetchAll();
    }

    // Search users by email (partial match, case-insensitive)
    public function searchUsersByEmail(string $email): array
    {
        $sql = 'SELECT id_user, nom, prenom, email, role, statut FROM User
                WHERE email LIKE :email ORDER BY nom, prenom';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['email' => '%' . addcslashes($email, '%_\\') . '%']);
        return $query->fetchAll();
    }

    // One user by id (returns null if not found)
    public function getUserById(int $id): ?array
    {
        $sql = 'SELECT * FROM User WHERE id_user = :id';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
        $user = $query->fetch();
        return $user ?: null;
    }

    // One user by email (used by the login)
    public function getUserByEmail(string $email): ?array
    {
        $sql = 'SELECT * FROM User WHERE email = :email';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['email' => $email]);
        $user = $query->fetch();
        return $user ?: null;
    }

    // true if the email is already used (optionally ignoring one user, for the edit form)
    public function emailExists(string $email, ?int $exceptId = null): bool
    {
        $sql = 'SELECT COUNT(*) FROM User WHERE email = :email AND id_user <> :id';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['email' => $email, 'id' => $exceptId ?? 0]);
        return (int) $query->fetchColumn() > 0;
    }

    // Update a user's information (not the password)
    public function updateUser(User $user, int $id): void
    {
        $sql = 'UPDATE User
                SET nom = :nom, prenom = :prenom, email = :email, role = :role, statut = :statut
                WHERE id_user = :id';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute([
            'nom'    => $user->getNom(),
            'prenom' => $user->getPrenom(),
            'email'  => $user->getEmail(),
            'role'   => $user->getRole(),
            'statut' => $user->getStatut(),
            'id'     => $id,
        ]);
    }

    // Delete a user (their data is deleted too: ON DELETE CASCADE)
    public function deleteUser(int $id): void
    {
        $sql = 'DELETE FROM User WHERE id_user = :id';
        $db = config::getConnexion();
        $query = $db->prepare($sql);
        $query->execute(['id' => $id]);
    }
}
