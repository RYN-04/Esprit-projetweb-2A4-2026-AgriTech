<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../../Controllers/UserController.php';

// Protection : admin seulement
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$pageTitle = 'Utilisateurs - AgriTech';
$currentPage = 'users';
$base = '../'; // la page est dans un sous-dossier de admin/

$userController = new UserController();
$users = $userController->listUsers();

// Messages affiches apres une action (voir add.php, edit.php, delete.php)
$succes = [
    'ajoute'   => 'Utilisateur ajouté avec succès.',
    'modifie'  => 'Utilisateur modifié avec succès.',
    'supprime' => 'Utilisateur supprimé.',
];
$erreurs = [
    'self'        => 'Vous ne pouvez pas supprimer votre propre compte.',
    'introuvable' => 'Utilisateur introuvable.',
];

function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
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

          <?php if (isset($_GET['ok'], $succes[$_GET['ok']])): ?>
            <p class="alert success"><?= e($succes[$_GET['ok']]) ?></p>
          <?php endif; ?>
          <?php if (isset($_GET['erreur'], $erreurs[$_GET['erreur']])): ?>
            <p class="alert error"><?= e($erreurs[$_GET['erreur']]) ?></p>
          <?php endif; ?>

          <div class="table-wrap">
            <table class="users">
              <thead>
                <tr><th>Nom</th><th>Prénom</th><th>Email</th><th>Rôle</th><th>Statut</th><th>Actions</th></tr>
              </thead>
              <tbody>
                <?php if (empty($users)): ?>
                  <tr><td colspan="6" class="empty">Aucun utilisateur.</td></tr>
                <?php endif; ?>
                <?php foreach ($users as $u): ?>
                  <tr>
                    <td><?= e($u['nom']) ?></td>
                    <td><?= e($u['prenom']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><span class="tag <?= e($u['role']) ?>"><?= e($u['role']) ?></span></td>
                    <td><span class="tag <?= e($u['statut']) ?>"><?= e($u['statut']) ?></span></td>
                    <td>
                      <div class="actions">
                        <a class="edit" href="edit.php?id=<?= (int) $u['id_user'] ?>">Modifier</a>
                        <?php if ((int) $u['id_user'] !== (int) $_SESSION['id_user']): ?>
                          <form method="post" action="delete.php" onsubmit="return confirmerSuppression()">
                            <input type="hidden" name="id" value="<?= (int) $u['id_user'] ?>">
                            <button type="submit" class="delete">Supprimer</button>
                          </form>
                        <?php endif; ?>
                      </div>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </main>

        <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
      </div>
    </div>
  </body>
</html>
