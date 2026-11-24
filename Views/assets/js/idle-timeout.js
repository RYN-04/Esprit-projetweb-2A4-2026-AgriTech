// Déconnexion automatique après 30 min sans mouvement de souris / clavier.
// Chargé seulement quand l'utilisateur est connecté (voir includes/head.php).
(function () {
  var s = document.currentScript;
  var timeout = parseInt(s.dataset.timeout, 10) * 1000;
  var lastActivity = Date.now();
  var lastPing = Date.now();

  function activity() {
    lastActivity = Date.now();
    // Prévient le serveur (au plus 1 fois par minute) pour garder la session ouverte
    if (lastActivity - lastPing > 60000) {
      lastPing = lastActivity;
      fetch(s.dataset.ping, { credentials: 'same-origin' })
        .then(function (r) { return r.json(); })
        .then(function (d) { if (!d.active) window.location.href = s.dataset.logout; });
    }
  }

  ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart'].forEach(function (e) {
    window.addEventListener(e, activity);
  });

  // Vérifie chaque seconde
  setInterval(function () {
    if (Date.now() - lastActivity >= timeout) {
      window.location.href = s.dataset.logout;
    }
  }, 1000);
})();
