<?php
declare(strict_types=1);

require_once __DIR__ . '/includes/session.php';
require_once __DIR__ . '/../Controllers/UserController.php';

$pageTitle = 'Connexion - AgriTech';
$currentPage = 'login';

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    // Server-side validation (same rules as validation.js)
    if ($email === '') {
        $errors['email'] = "L'email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Adresse email invalide.';
    }
    if ($password === '') {
        $errors['password'] = 'Le mot de passe est obligatoire.';
    }

    if (empty($errors)) {
        $userController = new UserController();
        $user = $userController->getUserByEmail($email);

        // Same message for "unknown email" and "wrong password" (don't reveal which one)
        if ($user === null || !password_verify($password, $user['password'])) {
            $errors['login'] = 'Email ou mot de passe incorrect.';
        } elseif ($user['statut'] !== 'actif') {
            $errors['login'] = 'Votre compte est désactivé. Contactez un administrateur.';
        } else {
            // New session id after login (protection against session fixation)
            session_regenerate_id(true);
            $_SESSION['id_user'] = (int) $user['id_user'];
            $_SESSION['nom'] = $user['nom'];
            $_SESSION['prenom'] = $user['prenom'];
            $_SESSION['role'] = $user['role'];

            if ($user['role'] === 'admin') {
                header('Location: admin/dashboard.php');
            } else {
                header('Location: ../index.php');
            }
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
      <div class="auth-bg" aria-hidden="true">
        <svg width="0" height="0" style="position:absolute"><symbol id="leaf" viewBox="0 0 24 24"><path d="M11 20A7 7 0 0 1 9.8 6.1C15.5 5 17 4.5 19 2c1 2 2 4.2 2 8 0 5.5-4.8 10-10 10Z"/></symbol></svg>
        <svg class="leaf gold" style="left:8%;animation-delay:0s;animation-duration:14s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf green" style="left:30%;animation-delay:-5s;animation-duration:16s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf gold" style="left:62%;animation-delay:-9s;animation-duration:13s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
        <svg class="leaf green" style="left:84%;animation-delay:-3s;animation-duration:15s" viewBox="0 0 24 24" aria-hidden="true"><use href="#leaf"/></svg>
      </div>
      <section class="auth-card">
        <h1>Connexion</h1>
        <p class="lead">Accédez à votre espace AgriTech.</p>
        <?php if (isset($_GET['inscrit'])): ?>
          <p class="alert success">Compte créé. Vous pouvez maintenant vous connecter.</p>
        <?php endif; ?>
        <?php if (isset($_GET['deconnecte'])): ?>
          <p class="alert success">Vous êtes déconnecté.</p>
        <?php endif; ?>
        <?php if (isset($errors['login'])): ?>
          <p class="alert error"><?= e($errors['login']) ?></p>
        <?php endif; ?>
        <form method="post" action="login.php" novalidate data-validate>
          <label>Email
            <input type="email" name="email" value="<?= e($email) ?>" data-required data-email data-label="L'email" class="<?= isset($errors['email']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['email'])): ?><span class="field-error"><?= e($errors['email']) ?></span><?php endif; ?>
          </label>
          <label>Mot de passe
            <input type="password" name="password" data-required data-label="Le mot de passe" class="<?= isset($errors['password']) ? 'invalid' : '' ?>">
            <?php if (isset($errors['password'])): ?><span class="field-error"><?= e($errors['password']) ?></span><?php endif; ?>
          </label>
          <button type="submit">Se connecter</button>
        </form>
        <p class="alt">Pas de compte ? <a href="register.php">Inscription</a></p>
      </section>
    </main>
    <?php require_once __DIR__ . '/includes/footer.php'; ?>
  </body>
</html>
