<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../../Controllers/UserController.php';

// Protection : admin seulement
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$pageTitle = 'Modifier un utilisateur - AgriTech';
$currentPage = 'users';
$base = '../';

$userController = new UserController();

// L'utilisateur a modifier (id dans l'URL : edit.php?id=3)
$id = (int) ($_GET['id'] ?? 0);
$user = $userController->getUserById($id);
if ($user === null) {
    header('Location: users.php?erreur=introuvable');
    exit;
}

// true si l'admin connecte modifie son propre compte
$moiMeme = ((int) $_SESSION['id_user'] === $id);

$errors = [];
$old = [
    'nom'    => $user['nom'],
    'prenom' => $user['prenom'],
    'email'  => $user['email'],
    'role'   => $user['role'],
    'statut' => $user['statut'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Lire les champs
    foreach (['nom', 'prenom', 'email', 'role', 'statut'] as $champ) {
        $old[$champ] = trim($_POST[$champ] ?? '');
    }

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
    } elseif ($userController->emailExists($old['email'], $id)) {
        $errors['email'] = 'Cet email est déjà utilisé.';
    }

    if (!in_array($old['role'], ['admin', 'agriculteur'], true)) {
        $errors['role'] = 'Rôle invalide.';
    }
    if (!in_array($old['statut'], ['actif', 'inactif'], true)) {
        $errors['statut'] = 'Statut invalide.';
    }

    // Regles de securite : un admin ne se retrograde pas et ne se desactive pas
    // (cela protege aussi le dernier admin)
    if ($moiMeme && !isset($errors['role']) && $old['role'] !== 'admin') {
        $errors['role'] = 'Vous ne pouvez pas retirer votre propre rôle administrateur.';
    }
    if ($moiMeme && !isset($errors['statut']) && $old['statut'] !== 'actif') {
        $errors['statut'] = 'Vous ne pouvez pas désactiver votre propre compte.';
    }

    // 3. Pas d'erreur : on met a jour (le mot de passe ne change pas ici)
    if (empty($errors)) {
        $modif = new User($id, $old['nom'], $old['prenom'], $old['email'], '', $old['role'], $old['statut']);
        $userController->updateUser($modif, $id);
        header('Location: users.php?ok=modifie');
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
          <div class="page-header"><h1>Modifier un utilisateur</h1><p>Mettez à jour les informations du compte.</p></div>

          <section class="form-card">
            <form method="post" action="edit.php?id=<?= $id ?>" novalidate onsubmit="return validerFormulaire()">
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
                <button type="submit">Enregistrer</button>
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
