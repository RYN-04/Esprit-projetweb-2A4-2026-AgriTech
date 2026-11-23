<?php
// $basePath = chemin relatif vers Views/ ('' à la racine)
$basePath = $basePath ?? '';
?>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= isset($pageTitle) ? $pageTitle : 'AgriTech' ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Fraunces:ital,wght@1,500&family=Inter:wght@400;500;600;700;800&display=swap">
<link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
<script src="<?= $basePath ?>assets/js/main.js" defer></script>
