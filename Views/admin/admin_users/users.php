<?php
// PAGE : liste des utilisateurs (avec boutons Modifier / Supprimer)

require_once __DIR__ . '/../../includes/session.php';

// Seul l'admin a le droit d'etre ici
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$pageTitle = 'Utilisateurs - AgriTech';
$currentPage = 'users';
$base = '../';   // cette page est dans un sous-dossier de admin/

// On recupere tous les utilisateurs dans la base
$controller = new UserController();
$users = $controller->listUsers();

// Message affiche apres une action (ajout, modification, suppression)
$message = '';
if (isset($_GET['ok'])) {
    if ($_GET['ok'] === 'ajoute')   { $message = 'Utilisateur ajouté.'; }
    if ($_GET['ok'] === 'modifie')  { $message = 'Utilisateur modifié.'; }
    if ($_GET['ok'] === 'supprime') { $message = 'Utilisateur supprimé.'; }
}
$erreur = '';
if (isset($_GET['erreur'])) {
    if ($_GET['erreur'] === 'self')        { $erreur = 'Vous ne pouvez pas supprimer votre propre compte.'; }
    if ($_GET['erreur'] === 'introuvable') { $erreur = 'Utilisateur introuvable.'; }
}

// Raccourci pour afficher du texte en securite
function e($texte)
{
    return htmlspecialchars($texte, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="fr">
  <head>
    <?php require_once __DIR__ . '/../includes/head.php'; ?>
    <link rel="stylesheet" href="users.css">
    <script src="users.js" defer></script>
  </head>
  <body>
    <div class="admin-wrap">
      <?php require_once __DIR__ . '/../includes/sidebar.php'; ?>

      <div class="admin-main">
        <header class="topbar">
          <h2>Espace administrateur</h2>
          <nav>
            <a href="../../../index.php">Voir le site</a>
            <a href="../../logout.php">Déconnexion</a>
          </nav>
        </header>

        <main>
          <div class="bar">
            <div class="page-header"><h1>Utilisateurs</h1><p>Gérez les comptes de la plateforme.</p></div>
            <a class="button" href="add.php">+ Ajouter un utilisateur</a>
          </div>

          <?php if ($message !== '') { ?>
            <p class="alert success"><?= e($message) ?></p>
          <?php } ?>
          <?php if ($erreur !== '') { ?>
            <p class="alert error"><?= e($erreur) ?></p>
          <?php } ?>

          <div class="table-wrap">
            <table class="users">
              <tr>
                <th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th>
              </tr>

              <?php foreach ($users as $u) { ?>
                <tr>
                  <td><?= e($u['nom']) ?></td>
                  <td><?= e($u['prenom']) ?></td>
                  <td><?= e($u['email']) ?></td>
                  <td><span class="tag <?= e($u['role']) ?>"><?= e($u['role']) ?></span></td>
                  <td><span class="tag <?= e($u['statut']) ?>"><?= e($u['statut']) ?></span></td>
                  <td>
                    <div class="actions">
                      <a class="edit" href="edit.php?id=<?= (int) $u['id_user'] ?>">Modifier</a>

                      <?php if ((int) $u['id_user'] !== (int) $_SESSION['id_user']) { ?>
                        <form method="post" action="delete.php" onsubmit="return confirmerSuppression()">
                          <input type="hidden" name="id" value="<?= (int) $u['id_user'] ?>">
                          <button type="submit" class="delete">Supprimer</button>
                        </form>
                      <?php } ?>
                    </div>
                  </td>
                </tr>
              <?php } ?>
            </table>
          </div>
        </main>

        <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
      </div>
    </div>
  </body>
</html>
