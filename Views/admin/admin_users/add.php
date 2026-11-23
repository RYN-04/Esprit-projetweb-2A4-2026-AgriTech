<?php
// PAGE : formulaire pour AJOUTER un utilisateur

require_once __DIR__ . '/../../includes/session.php';

// Seul l'admin a le droit d'etre ici
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$pageTitle = 'Ajouter un utilisateur - AgriTech';
$currentPage = 'users';
$base = '../';

$controller = new UserController();

// Valeurs du formulaire (vides au depart)
$nom = '';
$prenom = '';
$email = '';
$role = 'agriculteur';
$statut = 'actif';

// Messages d'erreur (vides au depart)
$erreur_nom = '';
$erreur_prenom = '';
$erreur_email = '';
$erreur_password = '';

// Quand on clique sur "Ajouter", le formulaire est envoye ici (methode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role = $_POST['role'];
    $statut = $_POST['statut'];

    $ok = true;   // devient false des qu'il y a une erreur

    if ($nom === '') {
        $erreur_nom = 'Le nom est obligatoire.';
        $ok = false;
    } elseif (strlen($nom) < 2) {
        $erreur_nom = 'Le nom doit contenir au moins 2 caractères.';
        $ok = false;
    }

    if ($prenom === '') {
        $erreur_prenom = 'Le prénom est obligatoire.';
        $ok = false;
    } elseif (strlen($prenom) < 2) {
        $erreur_prenom = 'Le prénom doit contenir au moins 2 caractères.';
        $ok = false;
    }

    if ($email === '') {
        $erreur_email = "L'email est obligatoire.";
        $ok = false;
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreur_email = 'Email invalide.';
        $ok = false;
    } elseif ($controller->emailExists($email)) {
        $erreur_email = 'Cet email est déjà utilisé.';
        $ok = false;
    }

    if (strlen($password) < 8) {
        $erreur_password = 'Le mot de passe doit contenir au moins 8 caractères.';
        $ok = false;
    }

    // Securite : le role et le statut doivent etre des valeurs connues
    if ($role !== 'admin' && $role !== 'agriculteur') {
        $ok = false;
    }
    if ($statut !== 'actif' && $statut !== 'inactif') {
        $ok = false;
    }

    // Aucune erreur : on enregistre dans la base puis on retourne a la liste
    if ($ok) {
        $user = new User(null, $nom, $prenom, $email, $password, $role, $statut);
        $controller->addUser($user);
        header('Location: users.php?ok=ajoute');
        exit;
    }
}

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
          <div class="page-header"><h1>Ajouter un utilisateur</h1><p>Créez un nouveau compte.</p></div>

          <section class="form-card">
            <form method="post" action="add.php" novalidate onsubmit="return validerFormulaire()">

              <label>Nom
                <input type="text" id="nom" name="nom" value="<?= e($nom) ?>">
                <span class="field-error" id="erreur-nom"><?= e($erreur_nom) ?></span>
              </label>

              <label>Prénom
                <input type="text" id="prenom" name="prenom" value="<?= e($prenom) ?>">
                <span class="field-error" id="erreur-prenom"><?= e($erreur_prenom) ?></span>
              </label>

              <label>Email
                <input type="email" id="email" name="email" value="<?= e($email) ?>">
                <span class="field-error" id="erreur-email"><?= e($erreur_email) ?></span>
              </label>

              <label>Mot de passe
                <input type="password" id="password" name="password">
                <span class="field-error" id="erreur-password"><?= e($erreur_password) ?></span>
              </label>

              <label>Rôle
                <select id="role" name="role">
                  <option value="agriculteur" <?= $role === 'agriculteur' ? 'selected' : '' ?>>Agriculteur</option>
                  <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
                <span class="field-error" id="erreur-role"></span>
              </label>

              <label>Statut
                <select id="statut" name="statut">
                  <option value="actif" <?= $statut === 'actif' ? 'selected' : '' ?>>Actif</option>
                  <option value="inactif" <?= $statut === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                </select>
                <span class="field-error" id="erreur-statut"></span>
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
