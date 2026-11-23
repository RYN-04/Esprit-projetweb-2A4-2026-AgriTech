<?php
// $currentPage doit etre defini dans la page appelante
$currentPage = $currentPage ?? '';
<<<<<<< HEAD
=======
$base = $base ?? ''; // '' pour admin/, '../' pour admin/admin_users/
>>>>>>> d5b9f36 (Initial commit)

function sideActive($page, $currentPage) {
    return $page === $currentPage ? 'active' : '';
}

// Liens du menu : cle (= $currentPage) => [label, page relative a admin/, icone SVG]
// Tant que la page n'existe pas, le lien reste "#" ; il s'active tout seul
// des que le coequipier cree le fichier.
$sideModules = [
    'drones'   => ['Gestion des drones agricoles',                '../drones/drones.php',          '<circle cx="5" cy="5" r="2.5"/><circle cx="19" cy="5" r="2.5"/><circle cx="5" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/><rect x="9" y="9" width="6" height="6" rx="1"/>'],
    'materiel' => ['Gestion du matériel agricole',                '../materiel/materiel.php',      '<circle cx="7" cy="17" r="3.5"/><circle cx="18" cy="18" r="2.5"/><path d="M10.5 17H15.5M7 13.5V8h6l3 5h3.5v3.5M13 8V5h3"/>'],
    'energie'  => ['Gestion de l’énergie durable et non durable', '../energie/installations.php',  '<path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/>'],
    'forum'    => ['Forum agricole',                              '../forum/forum.php',            '<path d="M21 12a8 8 0 0 1-11.6 7.1L4 20l1-4.6A8 8 0 1 1 21 12Z"/><path d="M8.5 11h7M8.5 14h4"/>'],
    'plantes'  => ['Gestion des plantes et des traitements',      '../plantes/plantes.php',        '<path d="M12 22V10M12 10C12 6 9 3 4 3c0 5 3 7 8 7ZM12 14c0-4 3-7 8-7 0 5-3 7-8 7Z"/>'],
];
<<<<<<< HEAD
$sideUsers = ['users', 'Utilisateurs', '../users/users.php', '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M16 4.5a3.5 3.5 0 0 1 0 7M18 14c2.2.6 3.5 2.4 3.5 6"/>'];

function sideLink($key, $label, $href, $icon, $currentPage) {
    $exists = $href === '#' || file_exists(__DIR__ . '/../' . $href);
    $url = $exists ? $href : '#';
=======
$sideUsers = ['users', 'Utilisateurs', 'admin_users/users.php', '<circle cx="9" cy="8" r="3.5"/><path d="M2.5 20c0-3.6 2.9-6 6.5-6s6.5 2.4 6.5 6M16 4.5a3.5 3.5 0 0 1 0 7M18 14c2.2.6 3.5 2.4 3.5 6"/>'];

function sideLink($key, $label, $href, $icon, $currentPage) {
    global $base;
    $exists = $href === '#' || file_exists(__DIR__ . '/../' . $href);
    $url = ($exists && $href !== '#') ? $base . $href : '#';
>>>>>>> d5b9f36 (Initial commit)
    $cls = sideActive($key, $currentPage);
    $title = $exists ? '' : ' title="Bientôt disponible"';
    $cur = $key === $currentPage ? ' aria-current="page"' : '';
    return '<a href="' . $url . '" class="' . $cls . '"' . $title . $cur . '>'
         . '<svg viewBox="0 0 24 24" aria-hidden="true">' . $icon . '</svg><span>' . $label . '</span></a>';
}
?>
<aside class="sidebar">
<<<<<<< HEAD
  <a class="side-brand" href="dashboard.php">
    <img class="side-logo" src="../assets/img/logo.png" alt="" width="40" height="40">
=======
  <a class="side-brand" href="<?= $base ?>dashboard.php">
    <img class="side-logo" src="<?= $base ?>../assets/img/logo.png" alt="" width="40" height="40">
>>>>>>> d5b9f36 (Initial commit)
    <span><strong>AgriTech</strong><small class="role">Espace administrateur</small></span>
  </a>
  <nav aria-label="Menu administrateur">
    <?= sideLink('dashboard', 'Tableau de bord', 'dashboard.php', '<rect x="3" y="3" width="7" height="9" rx="1.5"/><rect x="14" y="3" width="7" height="5" rx="1.5"/><rect x="14" y="12" width="7" height="9" rx="1.5"/><rect x="3" y="16" width="7" height="5" rx="1.5"/>', $currentPage) ?>
    <p class="side-label">Modules</p>
    <?php foreach ($sideModules as $key => [$label, $href, $icon]): ?>
      <?= sideLink($key, $label, $href, $icon, $currentPage) ?>
    <?php endforeach; ?>
    <p class="side-label">Gestion</p>
    <?= sideLink($sideUsers[0], $sideUsers[1], $sideUsers[2], $sideUsers[3], $currentPage) ?>
    <p class="side-label">Site</p>
    <?= sideLink('site', 'Retour au site', '../../index.php', '<path d="M3 11.5 12 4l9 7.5"/><path d="M5 10v10h5v-6h4v6h5V10"/>', $currentPage) ?>
  </nav>
</aside>
