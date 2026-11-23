<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../Controllers/UserController.php';

// Page réservée aux utilisateurs connectés
if (!isset($_SESSION['id_user'])) {
    header('Location: login.php');
    exit;
}

$pageTitle = 'Mon profil - AgriTech';
$currentPage = 'profile';

$userController = new UserController();
$id = (int) $_SESSION['id_user'];
$user = $userController->getUserById($id);

// Jeton CSRF simple (à remplacer par le helper de la tâche B1 quand il sera prêt)
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}

$errorsProfile = [];
$errorsPwd = [];
$errorCsrf = '';
$old = ['nom' => $user['nom'], 'prenom' => $user['prenom'], 'email' => $user['email']];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!hash_equals($_SESSION['csrf'], $_POST['csrf'] ?? '')) {
        $errorCsrf = 'Requête invalide. Rechargez la page et réessayez.';
    } elseif (($_POST['action'] ?? '') === 'profil') {
        // ---- D1 : modifier le profil ----
        $old['nom'] = trim($_POST['nom'] ?? '');
        $old['prenom'] = trim($_POST['prenom'] ?? '');
        $old['email'] = trim($_POST['email'] ?? '');

        // Validation PHP (mêmes règles que validation.js)
        foreach (['nom' => 'Le nom', 'prenom' => 'Le prénom'] as $field => $label) {
            $value = $old[$field];
            if ($value === '') {
                $errorsProfile[$field] = $label . ' est obligatoire.';
            } elseif (mb_strlen($value) < 2 || mb_strlen($value) > 100) {
                $errorsProfile[$field] = $label . ' doit contenir entre 2 et 100 caractères.';
            } elseif (!preg_match("/^[\p{L} '\-]+$/u", $value)) {
                $errorsProfile[$field] = $label . ' ne doit contenir que des lettres.';
            }
        }

        if ($old['email'] === '') {
            $errorsProfile['email'] = "L'email est obligatoire.";
        } elseif (!filter_var($old['email'], FILTER_VALIDATE_EMAIL) || mb_strlen($old['email']) > 190) {
            $errorsProfile['email'] = 'Adresse email invalide.';
        } elseif ($userController->emailExists($old['email'], $id)) {
            $errorsProfile['email'] = 'Cet email est déjà utilisé.';
        }

        if (empty($errorsProfile)) {
            // Le rôle et le statut ne changent pas ici : on garde ceux de la base
            $updated = new User($id, $old['nom'], $old['prenom'], $old['email'], '', $user['role'], $user['statut']);
            $userController->updateUser($updated, $id);
            header('Location: profile.php?ok=profil');
            exit;
        }
    } elseif (($_POST['action'] ?? '') === 'password') {
        // ---- D2 : changer le mot de passe ----
        $current = $_POST['current_password'] ?? '';
        $new = $_POST['new_password'] ?? '';
        $new2 = $_POST['new_password2'] ?? '';

        if ($current === '') {
            $errorsPwd['current_password'] = 'Le mot de passe actuel est obligatoire.';
        } elseif (!password_verify($current, $user['password'])) {
            $errorsPwd['current_password'] = 'Mot de passe actuel incorrect.';
        }

        if ($new === '') {
            $errorsPwd['new_password'] = 'Le nouveau mot de passe est obligatoire.';
        } elseif (strlen($new) < 8) {
            $errorsPwd['new_password'] = 'Le nouveau mot de passe doit contenir au moins 8 caractères.';
        }

        if ($new2 !== $new) {
            $errorsPwd['new_password2'] = 'Les mots de passe ne correspondent pas.';
        }

        if (empty($errorsPwd)) {
            $userController->updatePassword($id, $new);
            session_regenerate_id(true);
            header('Location: profile.php?ok=mdp');
            exit;
        }
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
    <?php require_once __DIR__ . '/includes/head.php'; ?>
    <script src="assets/js/validation.js" defer></script>
  </head>
  <body>
    <?php require_once __DIR__ . '/includes/navbar.php'; ?>

    <main class="small">
      <section class="auth-card">
        <h1>Mon profil</h1>
        <p class="lead">Modifiez vos informations personnelles.</p>

        <?php if (($_GET['ok'] ?? '') === 'profil'): ?>
          <p class="alert success">Profil mis à jour.</p>
        <?php elseif (($_GET['ok'] ?? '') === 'mdp'): ?>
          <p class="alert success">Mot de passe modifié.</p>
        <?php endif; ?>
        <?php if ($errorCsrf !== ''): ?>
          <p class="alert error"><?= e($errorCsrf) ?></p>
        <?php endif; ?>

        <form method="post" action="profile.php" novalidate data-validate>
          <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
          <input type="hidden" name="action" value="profil">
          <label>Nom
            <input type="text" name="nom" value="<?= e($old['nom']) ?>" data-required data-min="2" data-max="100" data-letters data-label="Le nom" class="<?= isset($errorsProfile['nom']) ? 'invalid' : '' ?>">
            <?php if (isset($errorsProfile['nom'])): ?><span class="field-error"><?= e($errorsProfile['nom']) ?></span><?php endif; ?>
          </label>
          <label>Prénom
            <input type="text" name="prenom" value="<?= e($old['prenom']) ?>" data-required data-min="2" data-max="100" data-letters data-label="Le prénom" class="<?= isset($errorsProfile['prenom']) ? 'invalid' : '' ?>">
            <?php if (isset($errorsProfile['prenom'])): ?><span class="field-error"><?= e($errorsProfile['prenom']) ?></span><?php endif; ?>
          </label>
          <label>Email
            <input type="email" name="email" value="<?= e($old['email']) ?>" data-required data-email data-max="190" data-label="L'email" class="<?= isset($errorsProfile['email']) ? 'invalid' : '' ?>">
            <?php if (isset($errorsProfile['email'])): ?><span class="field-error"><?= e($errorsProfile['email']) ?></span><?php endif; ?>
          </label>
          <button type="submit">Enregistrer</button>
        </form>
      </section>

      <section class="auth-card" style="margin-top:24px">
        <h1>Mot de passe</h1>
        <p class="lead">Choisissez un nouveau mot de passe.</p>

        <form method="post" action="profile.php" novalidate data-validate>
          <input type="hidden" name="csrf" value="<?= e($_SESSION['csrf']) ?>">
          <input type="hidden" name="action" value="password">
          <label>Mot de passe actuel
            <input type="password" name="current_password" data-required data-label="Le mot de passe actuel" class="<?= isset($errorsPwd['current_password']) ? 'invalid' : '' ?>">
            <?php if (isset($errorsPwd['current_password'])): ?><span class="field-error"><?= e($errorsPwd['current_password']) ?></span><?php endif; ?>
          </label>
          <label>Nouveau mot de passe
            <input type="password" name="new_password" data-required data-min="8" data-label="Le nouveau mot de passe" class="<?= isset($errorsPwd['new_password']) ? 'invalid' : '' ?>">
            <?php if (isset($errorsPwd['new_password'])): ?><span class="field-error"><?= e($errorsPwd['new_password']) ?></span><?php endif; ?>
          </label>
          <label>Confirmer le nouveau mot de passe
            <input type="password" name="new_password2" data-required data-match="new_password" data-label="La confirmation" class="<?= isset($errorsPwd['new_password2']) ? 'invalid' : '' ?>">
            <?php if (isset($errorsPwd['new_password2'])): ?><span class="field-error"><?= e($errorsPwd['new_password2']) ?></span><?php endif; ?>
          </label>
          <button type="submit">Changer le mot de passe</button>
        </form>
      </section>
    </main>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>
