<?php
declare(strict_types=1);

session_start();

// 1. Empty the session data
$_SESSION = [];

// 2. Delete the session cookie in the browser
if (ini_get('session.use_cookies')) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// 3. Destroy the session on the server
session_destroy();

header('Location: login.php?deconnecte=1');
exit;
