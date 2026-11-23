<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../Controllers/UserController.php';

$pageTitle = 'Inscription - AgriTech';
$currentPage = 'register';

$userController = new UserController();
$errors = [];
$old = ['nom' => '', 'prenom' => '', 'email' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 1. Read and clean the inputs
    $old['nom'] = trim($_POST['nom'] ?? '');
    $old['prenom'] = trim($_POST['prenom'] ?? '');
    $old['email'] = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $password2 = $_POST['password2'] ?? '';

    // 2. Server-side validation (same rules as validation.js)
    foreach (['nom' => 'Le nom', 'prenom' => 'Le prénom'] as $field => $label) {
        $value = $old[$field];
        if ($value === '') {
            $errors[$field] = $label . ' est obligatoire.';
        } elseif (mb_strlen($value) < 2 || mb_strlen($value) > 100) {
            $errors[$field] = $label . ' doit contenir entre 2 et 100 caractères.';
        } elseif (!preg_match("/^[\p{L} '\-]+$/u", $value)) {
            $errors[$field] = $label . ' ne doit contenir que des lettres.';
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

    if ($password2 !== $password) {
        $errors['password2'] = 'Les mots de passe ne correspondent pas.';
    }

    // 3. No error: create the account (role is always "agriculteur" here)
    if (empty($errors)) {
        $user = new User(null, $old['nom'], $old['prenom'], $old['email'], $password);
        $userController->addUser($user);
        header('Location: login.php?inscrit=1');
        exit;
    }
}

// Small helper for the view: escape a value before printing it
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
      <div class="auth-bg" aria-hidden="true">
        <svg width="0" height="0" style="position:absolute"><symbol id="leaf" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10Z"/></symbol></svg>
        <svg class="leaf gold" style="left:8%;animation-delay:0s;animation-duration:14s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf green" style="left:30%;animation-delay:-5s;animation-duration:16s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf gold" style="left:62%;animation-delay:-9s;animation-duration:13s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf green" style="left:84%;animation-delay:-3s;animation-duration:15s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
      </div>
      <section class="auth-card">
        <h1>Inscription</h1>
        <p class="lead">Rejoignez la plateforme AgriTech.</p>
        <form method="post" action="register.php" novalidate data-validate>
          <label>Nom
            <input type="text" name="nom" value="<?= e($old['nom']) ?>" data-required data-min="2" data-max="100" data-letters data-label="Le nom" class="<?= isset($errors['nom']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['nom'])): ?><span class="field-error"><?= e($errors['nom']) ?></span><?php endif; ?>
          </label>
          <label>Prénom
            <input type="text" name="prenom" value="<?= e($old['prenom']) ?>" data-required data-min="2" data-max="100" data-letters data-label="Le prénom" class="<?= isset($errors['prenom']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['prenom'])): ?><span class="field-error"><?= e($errors['prenom']) ?></span><?php endif; ?>
          </label>
          <label>Email
            <input type="email" name="email" value="<?= e($old['email']) ?>" data-required data-email data-max="190" data-label="L'email" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
          </label>
          <label>Mot de passe
            <input type="password" name="password" data-required data-min="8" data-label="Le mot de passe" class="<?= isset($errors['password']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
          </label>
          <label>Confirmer le mot de passe
            <input type="password" name="password2" data-required data-match="password" data-label="La confirmation" class="<?= isset($errors['password2']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['password2'])): ?><span class="field-error"><?= e($errors['password2']) ?></span><?php endif; ?>
          </label>
          <button type="submit">Créer le compte</button>
        </form>
        <p class="alt">Déjà inscrit ? <a href="login.php">Connexion</a></p>
      </section>
    </main>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>
