<?php
// PAGE : supprime un utilisateur (pas d'affichage, on redirige a la fin)

require_once __DIR__ . '/../../includes/session.php';

// Seul l'admin a le droit d'etre ici
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// On accepte seulement le formulaire du bouton "Supprimer" (POST)
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit;
}

$id = (int) $_POST['id'];

// Regle : l'admin ne peut pas supprimer son propre compte
if ($id === (int) $_SESSION['id_user']) {
    header('Location: users.php?erreur=self');
    exit;
}

$controller = new UserController();

if ($controller->getUserById($id) === null) {
    header('Location: users.php?erreur=introuvable');
    exit;
}

$controller->deleteUser($id);
header('Location: users.php?ok=supprime');
exit;
