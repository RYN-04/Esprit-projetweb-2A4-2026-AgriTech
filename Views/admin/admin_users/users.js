// Controles de saisie des formulaires add.php et edit.php.
// Si une erreur est trouvee, le formulaire n'est PAS envoye.

// Affiche un message sous le champ (ex: id = "nom" -> <span id="erreur-nom">)
function afficherErreur(id, message) {
  document.getElementById('erreur-' + id).textContent = message;
}

// Appelee quand on clique sur "Ajouter" / "Enregistrer"
function validerFormulaire() {
  var ok = true;

  // On efface d'abord les anciens messages
  afficherErreur('nom', '');
  afficherErreur('prenom', '');
  afficherErreur('email', '');
  afficherErreur('role', '');
  afficherErreur('statut', '');

  // Nom
  var nom = document.getElementById('nom').value.trim();
  if (nom === '') {
    afficherErreur('nom', 'Le nom est obligatoire.');
    ok = false;
  } else if (nom.length < 2) {
    afficherErreur('nom', 'Le nom doit contenir au moins 2 caractères.');
    ok = false;
  }

  // Prenom
  var prenom = document.getElementById('prenom').value.trim();
  if (prenom === '') {
    afficherErreur('prenom', 'Le prénom est obligatoire.');
    ok = false;
  } else if (prenom.length < 2) {
    afficherErreur('prenom', 'Le prénom doit contenir au moins 2 caractères.');
    ok = false;
  }

  // Email : doit contenir @ et un point
  var email = document.getElementById('email').value.trim();
  if (email === '') {
    afficherErreur('email', "L'email est obligatoire.");
    ok = false;
  } else if (email.indexOf('@') === -1 || email.indexOf('.') === -1) {
    afficherErreur('email', 'Email invalide.');
    ok = false;
  }

  // Mot de passe : existe seulement dans add.php
  var champPassword = document.getElementById('password');
  if (champPassword !== null) {
    afficherErreur('password', '');
    if (champPassword.value.length < 8) {
      afficherErreur('password', 'Le mot de passe doit contenir au moins 8 caractères.');
      ok = false;
    }
  }

  // Role et statut
  if (document.getElementById('role').value === '') {
    afficherErreur('role', 'Choisissez un rôle.');
    ok = false;
  }
  if (document.getElementById('statut').value === '') {
    afficherErreur('statut', 'Choisissez un statut.');
    ok = false;
  }

  return ok; // true = on envoie, false = on bloque
}

// Appelee avant la suppression
function confirmerSuppression() {
  return confirm('Voulez-vous supprimer cet utilisateur ?');
}
