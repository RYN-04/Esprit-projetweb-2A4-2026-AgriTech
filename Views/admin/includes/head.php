<?php $base = $base ?? ''; // prefixe pour les pages placees dans un sous-dossier (ex: admin_users/) ?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? $pageTitle : 'Admin - AgriTech' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="<?= $base ?>../assets/css/style.css">
<link rel="stylesheet" href="<?= $base ?>../assets/css/back.css">
<link rel="stylesheet" href="<?= $base ?>../assets/css/admin.css">
<?php if (isset($_SESSION['id_user'])): ?>
<script src="<?= $base ?>../assets/js/idle-timeout.js" data-timeout="<?= defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 1800 ?>" data-logout="<?= $base ?>../logout.php?expire=1" data-ping="<?= $base ?>../keepalive.php" defer></script>
<?php endif; ?>
