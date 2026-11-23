<?php
// Navbar GLOBALE : index, login, register.
// La page qui l'inclut doit inclure Views/includes/session.php tout en haut (avant le HTML).
// Chaque module a sa propre navbar dans <module>/includes/navbar.php
$currentPage = $currentPage ?? '';

function navActive($page, $currentPage) {
    return $page === $currentPage ? 'active' : '';
}

// Modules : label => page d'entrée (relative à Views/).
// Tant que la page d'entrée n'existe pas, le lien reste "#" ; il s'active tout seul
// dès que le coéquipier crée le fichier.
$modules = [
    'Drones agricoles'      => ['drones',   'drones/drones.php',          'Gestion des drones agricoles'],
    'Matériel agricole'     => ['materiel', 'materiel/materiel.php',      'Gestion du matériel agricole'],
    'Énergie'               => ['energie',  'energie/installations.php',  'Gestion de l’énergie durable et non durable'],
    'Forum'                 => ['forum',    'forum/forum.php',            'Forum agricole'],
    'Plantes et traitements'=> ['plantes',  'plantes/plantes.php',        'Gestion des plantes et des traitements'],
];
$logoBase = $basePath ?? '';
// Accueil = index.php à la racine du projet
$homeHref = $logoBase === '' ? '../index.php' : 'index.php';
?>
<header class="site-header">
  <div class="bar">
  <a class="brand" href="<?= $homeHref ?>"><img class="brand-logo" src="<?= $logoBase ?>assets/img/logo.png" alt="" width="36" height="36"><span>AgriTech</span></a>
  <button type="button" class="nav-toggle" aria-expanded="false" aria-controls="main-nav" aria-label="Ouvrir le menu">Menu</button>
  <nav id="main-nav">
    <?php foreach ($modules as $label => [$key, $entry, $full]): ?>
      <?php $exists = file_exists(__DIR__ . '/../' . $entry); ?>
      <a href="<?= $exists ? $logoBase . $entry : '#' ?>" class="<?= navActive($key, $currentPage) ?>"title="<?= $exists ? $full : $full . ' (bientôt disponible)' ?>"<?= $key === $currentPage ? ' aria-current="page"' : '' ?>><?= $label ?></a>
    <?php endforeach; ?>
    <span class="nav-sep" aria-hidden="true"></span>
    <?php if (isset($_SESSION['id_user'])): ?>
      <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="<?= $logoBase ?>admin/dashboard.php">Administration</a>
      <?php endif; ?>
      <span class="nav-user">Bonjour, <?= htmlspecialchars($_SESSION['prenom'], ENT_QUOTES, 'UTF-8') ?></span>
      <a href="<?= $logoBase ?>logout.php" class="nav-cta">Déconnexion</a>
    <?php else: ?>
      <a href="<?= $logoBase ?>login.php" class="<?= navActive('login', $currentPage) ?>">Connexion</a>
      <a href="<?= $logoBase ?>register.php" class="nav-cta <?= navActive('register', $currentPage) ?>">Inscription</a>
    <?php endif; ?>
  </nav>
  </div>
</header>
