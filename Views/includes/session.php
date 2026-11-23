<?php
declare(strict_types=1);

// Include this at the very top of every page (before any HTML), instead of session_start().
// It starts the session and re-reads the logged-in user from the database, so a change
// made by an admin (role, status, deleted account) is applied at the next page load,
// without having to log out and in again.

session_start();
require_once __DIR__ . '/../../Controllers/UserController.php';

if (isset($_SESSION['id_user'])) {
    $currentUser = (new UserController())->getUserById($_SESSION['id_user']);

    if ($currentUser === null || $currentUser['statut'] !== 'actif') {
        // Account deleted or disabled: log the user out
        $_SESSION = [];
        session_destroy();
    } else {
        $_SESSION['nom'] = $currentUser['nom'];
        $_SESSION['prenom'] = $currentUser['prenom'];
        $_SESSION['role'] = $currentUser['role'];
    }
}
