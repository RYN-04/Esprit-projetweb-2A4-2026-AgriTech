<?php
declare(strict_types=1);

require_once __DIR__ . '/../../includes/session.php';
require_once __DIR__ . '/../../../Controllers/UserController.php';

// Protection : admin seulement
if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit;
}

// On ne supprime que par le formulaire (POST), jamais par un simple lien
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: users.php');
    exit;
}

$id = (int) ($_POST['id'] ?? 0);

// Regle : un admin ne peut pas supprimer son propre compte
// (donc le dernier admin ne peut jamais etre supprime)
if ($id === (int) $_SESSION['id_user']) {
    header('Location: users.php?erreur=self');
    exit;
}

$userController = new UserController();
if ($userController->getUserById($id) === null) {
    header('Location: users.php?erreur=introuvable');
    exit;
}

$userController->deleteUser($id);
header('Location: users.php?ok=supprime');
exit;
