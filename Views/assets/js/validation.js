// AgriTech — client-side form validation (the PHP checks stay mandatory).
//
// How to use it in a form:
//   <form method="post" novalidate data-validate>
//   <input name="nom" data-required data-min="2" data-max="100" data-label="Le nom">
//   <input name="email" data-required data-email>
//   <input name="password2" data-required data-match="password" data-label="La confirmation">
//
// Rules: data-required, data-email, data-min / data-max (length),
//        data-match="<name of the other field>", data-letters (letters, spaces, - and ').

function showFieldError(input, message) {
  input.classList.add('invalid');
  var error = document.createElement('span');
  error.className = 'field-error';
  error.textContent = message;
  input.insertAdjacentElement('afterend', error);
}

function clearFieldErrors(form) {
  // also removes the PHP messages printed by the server
  form.querySelectorAll('.field-error').forEach(function (el) { el.remove(); });
  form.querySelectorAll('.invalid').forEach(function (el) { el.classList.remove('invalid'); });
}

function checkField(input, form) {
  var value = input.value.trim();
  var label = input.dataset.label || 'Ce champ';

  if (input.hasAttribute('data-required') && value === '') {
    return label + ' est obligatoire.';
  }
  if (value === '') {
    return '';
  }
  if (input.hasAttribute('data-email') && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
    return 'Adresse email invalide.';
  }
  if (input.dataset.min && value.length < Number(input.dataset.min)) {
    return label + ' doit contenir au moins ' + input.dataset.min + ' caractères.';
  }
  if (input.dataset.max && value.length > Number(input.dataset.max)) {
    return label + ' doit contenir au maximum ' + input.dataset.max + ' caractères.';
  }
  if (input.hasAttribute('data-letters') && !/^[\p{L} '\-]+$/u.test(value)) {
    return label + ' ne doit contenir que des lettres.';
  }
  if (input.dataset.match) {
    var other = form.querySelector('[name="' + input.dataset.match + '"]');
    if (other && other.value !== input.value) {
      return 'Les mots de passe ne correspondent pas.';
    }
  }
  return '';
}

document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('form[data-validate]').forEach(function (form) {
    form.addEventListener('submit', function (event) {
      clearFieldErrors(form);
      var firstInvalid = null;

      form.querySelectorAll('input, select, textarea').forEach(function (input) {
        var message = checkField(input, form);
        if (message !== '') {
          showFieldError(input, message);
          if (!firstInvalid) {
            firstInvalid = input;
          }
        }
      });

      if (firstInvalid) {
        event.preventDefault();
        firstInvalid.focus();
      }
    });
  });
});
