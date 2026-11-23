<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../../Controllers/UserController.php';

// Protection : admin seulement
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$pageTitle = 'Ajouter un utilisateur - AgriTech';
$currentPage = 'users';
$base = '../';

$userController = new UserController();
$errors = [];
$old = ['nom' => '', 'prenom' => '', 'email' => '', 'role' => 'agriculteur', 'statut' => 'actif'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lire les champs
    foreach (['nom', 'prenom', 'email', 'role', 'statut'] as $champ) {
        $old[$champ] = trim($_POST[$champ] ?? '');
    }
    $password = $_POST['password'] ?? '';

    // 2. Verifications cote serveur (memes regles que users.js)
    foreach (['nom' => 'Le nom', 'prenom' => 'Le prénom'] as $champ => $label) {
        $value = $old[$champ];
        if ($value === '') {
            $errors[$champ] = $label . ' est obligatoire.';
        } elseif (mb_strlen($value) < 2 || mb_strlen($value) > 100) {
            $errors[$champ] = $label . ' doit contenir entre 2 et 100 caractères.';
        } elseif (!preg_match("/^[\p{L} '\-]+$/u", $value)) {
            $errors[$champ] = $label . ' ne doit contenir que des lettres.';
        }
    }

    if ($old['email'] === '') {
        $errors['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 190) {
        $errors['email'] = 'Adresse email invalide.';
    } elseif ($userController->emailExists($old['email'])) {
        $errors['email'] = 'Cet email est déjà utilisé.';
    }

    if ($password === '') {
        $errors['password'] = 'Le mot de passe est obligatoire.';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Le mot de passe doit contenir au moins 8 caractères.';
    }

    if (!in_array($old['role'], ['admin', 'agriculteur'], true)) {
        $errors['role'] = 'Rôle invalide.';
    }
    if (!in_array($old['statut'], ['actif', 'inactif'], true)) {
        $errors['statut'] = 'Statut invalide.';
    }

    // 3. Pas d'erreur : on ajoute
    if (empty($errors)) {
        $user = new User(null, $old['nom'], $old['prenom'], $old['email'], $password, $old['role'], $old['statut']);
        $userController->addUser($user);
        header('Location: users.php?ok=ajoute');
        exit;
    }
}

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
          <div class="page-header"><h1>Ajouter un utilisateur</h1><p>Créez un nouveau compte.</p></div>

          <section class="form-card">
            <form method="post" action="add.php" novalidate onsubmit="return validerFormulaire()">
              <label>Nom
                <input type="text" id="nom" name="nom" value="<?= e($old['nom']) ?>" class="<?= isset($errors['nom']) ? 'invalid' : '' ?>">
                <span class="field-error" id="erreur-nom"><?= e($errors['nom'] ?? '') ?></span>
              </label>
              <label>Prénom
                <input type="text" id="prenom" name="prenom" value="<?= e($old['prenom']) ?>" class="<?= isset($errors['prenom']) ? 'invalid' : '' ?>">
                <span class="field-error" id="erreur-prenom"><?= e($errors['prenom'] ?? '') ?></span>
              </label>
              <label>Email
                <input type="email" id="email" name="email" value="<?= e($old['email']) ?>" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
                <span class="field-error" id="erreur-email"><?= e($errors['email'] ?? '') ?></span>
              </label>
              <label>Mot de passe
                <input type="password" id="password" name="password" class="<?= isset($errors['password']) ? 'invalid' : '' ?>">
                <span class="field-error" id="erreur-password"><?= e($errors['password'] ?? '') ?></span>
              </label>
              <label>Rôle
                <select id="role" name="role" class="<?= isset($errors['role']) ? 'invalid' : '' ?>">
                  <option value="agriculteur" <?= $old['role'] === 'agriculteur' ? 'selected' : '' ?>>Agriculteur</option>
                  <option value="admin" <?= $old['role'] === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
                <span class="field-error" id="erreur-role"><?= e($errors['role'] ?? '') ?></span>
              </label>
              <label>Statut
                <select id="statut" name="statut" class="<?= isset($errors['statut']) ? 'invalid' : '' ?>">
                  <option value="actif" <?= $old['statut'] === 'actif' ? 'selected' : '' ?>>Actif</option>
                  <option value="inactif" <?= $old['statut'] === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                </select>
                <span class="field-error" id="erreur-statut"><?= e($errors['statut'] ?? '') ?></span>
              </label>
              <div class="form-buttons">
                <button type="submit">Ajouter</button>
                <a class="button outline" href="users.php">Annuler</a>
              </div>
            </form>
          </section>
        </main>

        <?php require_once __DIR__ . '/../../includes/footer.php'; ?>
      </div>
    </div>
  </body>
</html>
