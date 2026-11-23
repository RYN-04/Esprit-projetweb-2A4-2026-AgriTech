<?php
require_once __DIR__ . '/../includes/session.php';

$pageTitle = 'Tableau de bord - AgriTech';
$currentPage = 'dashboard';
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php require_once __DIR__ . '/includes/head.php'; ?>
  </head>
  <body>
    <div class="admin-wrap">
      <?php require_once __DIR__ . '/includes/sidebar.php'; ?>

      <div class="admin-main">
        <header class="topbar">
          <h2>Espace administrateur</h2>
          <nav>
            <a href="../../index.php">Voir le site</a>
            <a href="../logout.php">Déconnexion</a>
          </nav>
        </header>

        <main>
          <div class="page-header"><h1>Tableau de bord</h1><p>Vue d'ensemble de votre exploitation.</p></div>
          <div class="stats">
            <article class="stat-card"><span class="stat-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="5" cy="5" r="2.5"/><circle cx="19" cy="5" r="2.5"/><circle cx="5" cy="19" r="2.5"/><circle cx="19" cy="19" r="2.5"/><rect x="9" y="9" width="6" height="6" rx="1"/><path d="M7 7l2 2M17 7l-2 2M7 17l2-2M17 17l-2-2"/></svg></span><div><h2>Drones</h2><span class="badge">Exemple</span><strong>5</strong><p>Nombre de drones</p></div></article>
            <article class="stat-card"><span class="stat-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="7" cy="17" r="3.5"/><circle cx="18" cy="18" r="2.5"/><path d="M10.5 17H15.5M7 13.5V8h6l3 5h3.5v3.5M13 8V5h3"/></svg></span><div><h2>Matériel</h2><span class="badge">Exemple</span><strong>4</strong><p>Équipements agricoles</p></div></article>
            <article class="stat-card"><span class="stat-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3 3 8l9 5 9-5-9-5Z"/><path d="m3 13 9 5 9-5M3 17l9 5 9-5"/></svg></span><div><h2>Sols</h2><span class="badge">Exemple</span><strong>Sol argileux</strong><p>Type de sol le plus sélectionné</p></div></article>
            <article class="stat-card"><span class="stat-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8Z"/></svg></span><div><h2>Consommation</h2><span class="badge">Exemple</span><strong>285 kWh</strong><p>Consommation d'énergie</p></div></article>
            <article class="stat-card"><span class="stat-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg></span><div><h2>Énergie</h2><span class="badge">Exemple</span><strong>365 kWh</strong><p>Énergie totale</p></div></article>
            <article class="stat-card"><span class="stat-icon"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 14.8V5a2 2 0 0 0-4 0v9.8a4 4 0 1 0 4 0Z"/></svg></span><div><h2>Climat</h2><span class="badge">Exemple</span><strong>Tempéré (15 - 30 °C)</strong><p>Climat le plus sélectionné</p></div></article>
          </div>
        </main>

        <?php require_once __DIR__ . '/../includes/footer.php'; ?>
      </div>
    </div>
  </body>
</html>
