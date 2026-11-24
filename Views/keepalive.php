<?php
declare(strict_types=1);

// Called by assets/js/idle-timeout.js while the user is active: including session.php
// refreshes the server-side activity time. Answers in JSON, never redirects.
define('SESSION_NO_REDIRECT', true);
require_once __DIR__ . '/includes/session.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(['active' => isset($_SESSION['id_user'])]);
