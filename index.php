<?php
require_once __DIR__ . '/Views/includes/session.php';

$pageTitle = 'Accueil - AgriTech';
$currentPage = 'index';
$basePath = 'Views/';
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php require_once __DIR__ . '/Views/includes/head.php'; ?>
  </head>
  <body>
    <?php require_once __DIR__ . '/Views/includes/navbar.php'; ?>

    <main class="home">
      <svg width="0" height="0" style="position:absolute" aria-hidden="true">
      <symbol id="leaf" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10Z"/></symbol>
      <symbol id="tree" viewBox="0 0 64 96"><rect x="29" y="58" width="6" height="38" rx="2"/><circle cx="32" cy="34" r="26"/><circle cx="17" cy="52" r="15"/><circle cx="47" cy="52" r="15"/></symbol>
      <symbol id="pine" viewBox="0 0 64 96"><path d="M32 4 52 40H12Z"/><path d="M32 26 58 68H6Z"/><rect x="29" y="66" width="6" height="30" rx="2"/></symbol>
    </svg>
      <section class="hero">
        <svg class="leaf gold" style="left:10%;animation-delay:0s;animation-duration:13s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf green" style="left:24%;animation-delay:-4s;animation-duration:15s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf gold" style="left:42%;animation-delay:-8s;animation-duration:12s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf green" style="left:58%;animation-delay:-2s;animation-duration:14s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf gold" style="left:74%;animation-delay:-6s;animation-duration:16s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf green" style="left:88%;animation-delay:-10s;animation-duration:13s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <div class="container">
          <span class="eyebrow">Agriculture intelligente et durable</span>
          <h1>Bienvenue sur <span>AgriTech</span></h1>
          <p class="lead">Gérez vos drones, matériel, énergie, plantes et traitements au même endroit.</p>
          <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
            <div class="actions">
              <a class="button gold" href="Views/admin/dashboard.php">Accéder au tableau de bord</a>
            </div>
          <?php endif; ?>
        </div>
        <div class="scene" aria-hidden="true">
          <svg class="tree t1" viewBox="0 0 64 96" aria-hidden="true"><use href="#tree"/></svg><svg class="tree t2" viewBox="0 0 64 96" aria-hidden="true"><use href="#pine"/></svg><svg class="tree t3" viewBox="0 0 64 96" aria-hidden="true"><use href="#tree"/></svg><svg class="tree t4" viewBox="0 0 64 96" aria-hidden="true"><use href="#pine"/></svg><svg class="tree t5" viewBox="0 0 64 96" aria-hidden="true"><use href="#tree"/></svg><svg class="tree t6" viewBox="0 0 64 96" aria-hidden="true"><use href="#pine"/></svg>
          <div class="ground"></div>
        </div>
      </section>

      <section class="modules-section">
        <div class="container">
          <h2 class="reveal">Nos modules</h2>
          <div class="modules">
            <article class="module-card reveal" data-delay="0"><h3>Gestion des drones agricoles</h3><p>Drones, missions et suivi des parcelles.</p></article>
            <article class="module-card reveal" data-delay="100"><h3>Gestion du matériel agricole</h3><p>Tracteurs, outils et entretien.</p></article>
            <article class="module-card reveal" data-delay="200"><h3>Gestion de l’énergie durable et non durable</h3><p>Solaire, réseau et consommation.</p></article>
            <article class="module-card reveal" data-delay="300"><h3>Forum agricole</h3><p>Questions, astuces et échanges entre agriculteurs.</p></article>
            <article class="module-card reveal" data-delay="400"><h3>Gestion des plantes et des traitements</h3><p>Cultures, besoins et calendrier des traitements.</p></article>
          </div>
        </div>
      </section>
    </main>
    <?php require_once __DIR__ . '/Views/includes/footer.php'; ?>
  </body>
</html>
