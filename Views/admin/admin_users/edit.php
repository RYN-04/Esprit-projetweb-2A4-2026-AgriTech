<?php
// PAGE : formulaire pour MODIFIER un utilisateur (edit.php?id=3)

require_once __DIR__ . '/../../includes/session.php';

// Seul l'admin a le droit d'etre ici
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

$pageTitle = 'Modifier un utilisateur - AgriTech';
$currentPage = 'users';
$base = '../';

$controller = new UserController();

// On lit l'id dans l'adresse, puis on cherche l'utilisateur dans la base
$id = (int) $_GET['id'];
$user = $controller->getUserById($id);

if ($user === null) {
    header('Location: users.php?erreur=introuvable');
    exit;
}

// true si l'admin modifie son propre compte
$moi = ($id === (int) $_SESSION['id_user']);

// Au depart, le formulaire contient les valeurs actuelles de l'utilisateur
$nom = $user['nom'];
$prenom = $user['prenom'];
$email = $user['email'];
$role = $user['role'];
$statut = $user['statut'];

// Messages d'erreur (vides au depart)
$erreur_nom = '';
$erreur_prenom = '';
$erreur_email = '';
$erreur_role = '';
$erreur_statut = '';

// Quand on clique sur "Enregistrer", le formulaire est envoye ici (methode POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $email = trim($_POST['email']);
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
    } elseif ($controller->emailExists($email, $id)) {
        // $id = on ignore l'utilisateur lui-meme dans la verification
        $erreur_email = 'Cet email est déjà utilisé.';
        $ok = false;
    }

    // Securite : le role et le statut doivent etre des valeurs connues
    if ($role !== 'admin' && $role !== 'agriculteur') {
        $ok = false;
    }
    if ($statut !== 'actif' && $statut !== 'inactif') {
        $ok = false;
    }

    // Regles : l'admin ne peut pas se retrograder ni se desactiver
    if ($moi && $role !== 'admin') {
        $erreur_role = 'Vous ne pouvez pas retirer votre propre rôle administrateur.';
        $ok = false;
    }
    // Regle : on ne retire jamais le role du dernier administrateur
    if ($user['role'] === 'admin' && $role !== 'admin' && $controller->countAdmins() <= 1) {
        $erreur_role = 'Impossible : c\'est le dernier administrateur.';
        $ok = false;
    }
    if ($moi && $statut !== 'actif') {
        $erreur_statut = 'Vous ne pouvez pas désactiver votre propre compte.';
        $ok = false;
    }

    // Aucune erreur : on met a jour la base puis on retourne a la liste
    // (le mot de passe ne change pas ici : on envoie '')
    if ($ok) {
        $modif = new User($id, $nom, $prenom, $email, '', $role, $statut);
        $controller->updateUser($modif, $id);
        header('Location: users.php?ok=modifie');
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
          <div class="page-header"><h1>Modifier un utilisateur</h1><p>Mettez à jour les informations du compte.</p></div>

          <section class="form-card">
            <form method="post" action="edit.php?id=<?= $id ?>" novalidate onsubmit="return validerFormulaire()">

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

              <label>Rôle
                <select id="role" name="role">
                  <option value="agriculteur" <?= $role === 'agriculteur' ? 'selected' : '' ?>>Agriculteur</option>
                  <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Administrateur</option>
                </select>
                <span class="field-error" id="erreur-role"><?= e($erreur_role) ?></span>
              </label>

              <label>Statut
                <select id="statut" name="statut">
                  <option value="actif" <?= $statut === 'actif' ? 'selected' : '' ?>>Actif</option>
                  <option value="inactif" <?= $statut === 'inactif' ? 'selected' : '' ?>>Inactif</option>
                </select>
                <span class="field-error" id="erreur-statut"><?= e($erreur_statut) ?></span>
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
