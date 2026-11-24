<?php
declare(strict_types=1);

// Include this at the very top of every page (before any HTML), instead of session_start().
// It starts the session, applies the inactivity timeout, and re-reads the logged-in user
// from the database, so a change made by an admin (role, status, deleted account) is
// applied at the next page load.

// 30 min d'inactivité (+ 2 min de tolérance car le navigateur ping une fois par minute)
if (!defined('SESSION_TIMEOUT')) {
    define('SESSION_TIMEOUT', 1800);
}

session_start();
require_once __DIR__ . '/../../Controllers/UserController.php';

if (isset($_SESSION['id_user'])) {
    $lastActivity = $_SESSION['last_activity'] ?? time();

    if (time() - $lastActivity > SESSION_TIMEOUT + 120) {
        // Session expirée : on la détruit
        $_SESSION = [];
        session_destroy();

        // keepalive.php définit SESSION_NO_REDIRECT et répond en JSON
        if (!defined('SESSION_NO_REDIRECT')) {
            $root = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT']));
            $views = str_ireplace($root, '', str_replace('\\', '/', realpath(__DIR__ . '/..')));
            header('Location: ' . $views . '/login.php?expire=1');
            exit;
        }
    } else {
        $_SESSION['last_activity'] = time();

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
}
