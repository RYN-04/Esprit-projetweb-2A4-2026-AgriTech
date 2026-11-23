// AgriTech — admin_users : controles de saisie simples (cote navigateur).
// Le PHP refait les memes verifications (obligatoire).

// Affiche un message d'erreur sous un champ
function afficherErreur(id, message) {
  document.getElementById('erreur-' + id).textContent = message;
  document.getElementById(id).classList.add('invalid');
}

// Efface l'erreur d'un champ
function effacerErreur(id) {
  var span = document.getElementById('erreur-' + id);
  var champ = document.getElementById(id);
  if (span) { span.textContent = ''; }
  if (champ) { champ.classList.remove('invalid'); }
}

// Appelee au submit des formulaires add.php et edit.php : onsubmit="return validerFormulaire()"
function validerFormulaire() {
  var ok = true;
  var ids = ['nom', 'prenom', 'email', 'password', 'role', 'statut'];
  for (var i = 0; i < ids.length; i++) {
    effacerErreur(ids[i]);
  }

  // nom et prenom
  var noms = [['nom', 'Le nom'], ['prenom', 'Le prénom']];
  for (var j = 0; j < noms.length; j++) {
    var id = noms[j][0];
    var label = noms[j][1];
    var valeur = document.getElementById(id).value.trim();
    if (valeur === '') {
      afficherErreur(id, label + ' est obligatoire.');
      ok = false;
    } else if (valeur.length < 2) {
      afficherErreur(id, label + ' doit contenir au moins 2 caractères.');
      ok = false;
    } else if (!/^[\p{L} '\-]+$/u.test(valeur)) {
      afficherErreur(id, label + ' ne doit contenir que des lettres.');
      ok = false;
    }
  }

  // email
  var email = document.getElementById('email').value.trim();
  if (email === '') {
    afficherErreur('email', "L'email est obligatoire.");
    ok = false;
  } else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
    afficherErreur('email', 'Adresse email invalide.');
    ok = false;
  }

  // mot de passe (seulement dans add.php)
  var champPassword = document.getElementById('password');
  if (champPassword) {
    if (champPassword.value === '') {
      afficherErreur('password', 'Le mot de passe est obligatoire.');
      ok = false;
    } else if (champPassword.value.length < 8) {
      afficherErreur('password', 'Le mot de passe doit contenir au moins 8 caractères.');
      ok = false;
    }
  }

  // role et statut
  if (document.getElementById('role').value === '') {
    afficherErreur('role', 'Choisissez un rôle.');
    ok = false;
  }
  if (document.getElementById('statut').value === '') {
    afficherErreur('statut', 'Choisissez un statut.');
    ok = false;
  }

  return ok; // false = le formulaire n'est pas envoye
}

// Appelee avant la suppression : onsubmit="return confirmerSuppression()"
function confirmerSuppression() {
  return confirm('Voulez-vous supprimer cet utilisateur ?');
}

// L'erreur d'un champ disparait quand on recommence a ecrire
document.addEventListener('DOMContentLoaded', function () {
  var ids = ['nom', 'prenom', 'email', 'password', 'role', 'statut'];
  ids.forEach(function (id) {
    var champ = document.getElementById(id);
    if (champ) {
      champ.addEventListener('input', function () { effacerErreur(id); });
      champ.addEventListener('change', function () { effacerErreur(id); });
    }
  });
});
