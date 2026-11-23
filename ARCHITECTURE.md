# ARCHITECTURE.md — AgriTech (MVC, Front Office & Back Office)

## 1. Overview

**MVC** architecture following the Esprit web workshop. There is no router or framework: every page is a PHP file opened directly, which includes its Controller and calls it.

```
   Browser ──► Views/<page>.php ──require──► Controllers/<Entity>Controller.php ──► config::getConnexion() ──► MySQL
                    ▲                                   │
                    └──────── data (arrays / Models) ───┘        Models/<Entity>.php = entity classes
```

| Interface | Audience | Where | Identity |
|---|---|---|---|
| **Front Office** | Farmer | `index.php`, `Views/*.php`, `Views/<module>/` | Green & gold (`style.css`) |
| **Back Office** | Administrator | `Views/admin/` | Olive `#606C38`, dark olive `#283618`, sage `#A3B18A`, beige `#DAD7CD`, cream `#F5F3EB` (`back.css` + `admin.css`) |

## 2. Folder structure

✅ = exists today · 🕓 = planned (link already wired in the menus, or to be created)

```
AgriTech/                          (served by XAMPP at http://localhost/web/AgriTech/)
├── index.php                  ✅  Home page (Front Office)
├── config.php                 ✅  class config — PDO singleton: config::getConnexion()
├── schema.sql                 ✅  Database schema (creates the `agritech` database)
├── seed.sql                   🕓  Test data (admin + farmer + samples)
├── README.md / RULES.md / PROJECT.md / ARCHITECTURE.md / TASKS.md
├── docs/                      ✅  diagram.md (database diagram), images/database.png|svg, generate-diagram.js
├── Models/                    ✅  (empty) entity classes: User.php, Drone.php, MissionDrone.php,
│                                  Materiel.php, CategorieMateriel.php, Energie.php, CategorieEnergie.php,
│                                  ProductionConsommation.php, Publication.php, Commentaire.php,
│                                  Plante.php, Traitement.php
├── Controllers/               ✅  (empty) UserController.php, DroneController.php, MaterielController.php,
│                                  EnergieController.php, ForumController.php, PlanteController.php
└── Views/
    ├── includes/              ✅  Front Office shared parts
    │   ├── head.php               <meta>, <title>, fonts, style.css, main.js
    │   ├── navbar.php             top menu (modules + Connexion / Inscription)
    │   └── footer.php             footer (also used by the admin pages)
    ├── login.php              ✅  Login page (static prototype for now)
    ├── register.php           ✅  Sign-up page (static prototype for now)
    ├── admin/                 ✅  Back Office
    │   ├── dashboard.php          dashboard (sample figures for now)
    │   └── includes/
    │       ├── head.php           <head> content: style.css + back.css + admin.css
    │       └── sidebar.php        side menu (dashboard, modules, users)
    ├── assets/                ✅
    │   ├── css/style.css          Front theme + shared base
    │   ├── css/back.css           Back theme (overrides the colour variables)
    │   ├── css/admin.css          Back layout (sidebar, topbar)
    │   ├── js/main.js             UI only: mobile menu, scroll reveal
    │   └── img/logo.png
    ├── drones/drones.php            🕓  module entry pages — see §4.3
    ├── materiel/materiel.php        🕓
    ├── energie/installations.php    🕓
    ├── forum/forum.php              🕓
    ├── plantes/plantes.php          🕓
    └── users/users.php              🕓  user management (admin)
```

## 3. Responsibilities per layer

| Layer | Does | Never does |
|---|---|---|
| **Model** (`Models/User.php`) | Private attributes = table columns, constructor, getters/setters | SQL, HTML, `$_POST`, `$_SESSION` |
| **Controller** (`Controllers/UserController.php`) | CRUD methods with **prepared** PDO queries via `config::getConnexion()`, validation, role checks | HTML output |
| **View** (`Views/...php`) | Read the request, call the Controller, check CSRF/session, display escaped data and forms, JS validation | SQL |

### Example (pattern to follow)

```php
// Models/User.php
class User
{
    public function __construct(
        private ?int $idUser, private string $nom, private string $prenom,
        private string $email, private string $password, private string $role = 'agriculteur'
    ) {}
    public function getNom(): string { return $this->nom; }
    // ... other getters / setters
}

// Controllers/UserController.php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../Models/User.php';

class UserController
{
    public function addUser(User $u): void
    {
        $sql = 'INSERT INTO User (nom, prenom, email, password, role)
                VALUES (:nom, :prenom, :email, :password, :role)';
        $query = config::getConnexion()->prepare($sql);
        $query->execute([
            'nom' => $u->getNom(), 'prenom' => $u->getPrenom(), 'email' => $u->getEmail(),
            'password' => password_hash($u->getPassword(), PASSWORD_DEFAULT), 'role' => $u->getRole(),
        ]);
    }
}

// Views/users/users.php
require_once __DIR__ . '/../../Controllers/UserController.php';
$users = (new UserController())->listUsers();
```

## 4. Views: shared includes and paths

### 4.1 Front Office page

Each page first includes `session.php` (mandatory, see `RULES.md` §4), sets three variables, then includes the shared parts:

```php
<?php
require_once __DIR__ . '/includes/session.php';  // ALWAYS first: session + up-to-date role/status

$pageTitle   = 'Connexion - AgriTech';
$currentPage = 'login';   // highlights the menu entry
$basePath    = '';        // relative path from the page to Views/  ('' in Views/, 'Views/' in index.php, '../' in Views/<module>/)
?>
<head><?php require_once __DIR__ . '/includes/head.php'; ?></head>
<body>
  <?php require_once __DIR__ . '/includes/navbar.php'; ?>
  <main>...</main>
  <?php require_once __DIR__ . '/includes/footer.php'; ?>
</body>
```

### 4.2 Back Office page

Pages in `Views/admin/` start with `require_once __DIR__ . '/../includes/session.php';`, then include `admin/includes/head.php`, `admin/includes/sidebar.php` (set `$currentPage`: `dashboard`, `drones`, `materiel`, `energie`, `forum`, `plantes`, `users`) and the shared `../includes/footer.php`. Layout: `.admin-wrap` › `.sidebar` + `.admin-main` (`.topbar` + `<main>`). See `dashboard.php`.

### 4.3 Module entry pages (auto-linked)

`Views/includes/navbar.php` and `Views/admin/includes/sidebar.php` already link to the module pages below. A link stays `#` ("bientôt disponible") until the file exists, then activates by itself.

| Module | `$currentPage` key | Entry file |
|---|---|---|
| Drones | `drones` | `Views/drones/drones.php` |
| Matériel | `materiel` | `Views/materiel/materiel.php` |
| Énergie | `energie` | `Views/energie/installations.php` |
| Forum | `forum` | `Views/forum/forum.php` |
| Plantes | `plantes` | `Views/plantes/plantes.php` |
| Utilisateurs (admin) | `users` | `Views/users/users.php` |

Per the navbar comment, a module may have its own menu in `Views/<module>/includes/navbar.php`.

> ⚠️ The admin sidebar points to the **same** files as the front navbar (`Views/<module>/...`). The team must decide whether a module's admin pages live in `Views/<module>/` (with a role check) or in `Views/admin/<module>/` (and then update the sidebar links).

## 5. Database (`schema.sql`)

InnoDB, `utf8mb4`, `INT UNSIGNED AUTO_INCREMENT` primary keys. `ON DELETE CASCADE` from `User` to the user's data; `ON DELETE RESTRICT` on reference tables (`Drone`, `CategorieMateriel`, `Categorie_Energie`, `Energie`).

### 5.1 Entities and attributes

| Table | Columns (PK / FK) |
|---|---|
| **User** | `id_user` PK, `nom`, `prenom`, `email` (unique), `password` (hash), `role` ENUM(`admin`,`agriculteur`), `statut` ENUM(`actif`,`inactif`, default `actif`) — *`statut` added to the original diagram for enable/disable* |
| **MissionDrone** | `id_mission` PK, `id_user` FK, `id_drone` FK, `date_mission`, `zone`, `statut` |
| **Drone** | `id_drone` PK, `nom_drone`, `modele`, `etat` |
| **Materiel** | `id_materiel` PK, `id_user` FK, `id_categorie` FK, `nom`, `etat` |
| **CategorieMateriel** | `id_categorie` PK, `nom_categorie` (unique) |
| **Production_Consommation** | `id_pc` PK, `id_user` FK, `id_energie` FK, `type_operation` ENUM(`Production`,`Consommation`), `date_operation` |
| **Energie** | `id_energie` PK, `id_categorie` FK, `nom_energie`, `type` |
| **Categorie_Energie** | `id_categorie` PK, `nom_categorie` (unique) |
| **Publication** | `id_publication` PK, `id_user` FK, `contenu`, `date_pub`, `image` (nullable) |
| **Commentaire** | `id_commentaire` PK, `id_user` FK, `id_publication` FK, `contenu`, `date_com` |
| **Plante** | `id_plante` PK, `id_user` FK, `nom`, `description` (nullable), `type` |
| **Traitement** | `id_traitement` PK, `id_plante` FK, `description`, `date_traitement`, `statut` |

### 5.2 Diagram

Full diagram (PK, FK, 1-N and N-N relationships, data dictionary): [`docs/diagram.md`](docs/diagram.md)

![AgriTech database diagram](docs/images/database.png)

## 6. Security in the architecture

| Mechanism | Where |
|---|---|
| PDO connection | `config.php` (`config::getConnexion()`, exceptions enabled, `FETCH_ASSOC`) |
| Prepared queries | Controllers |
| Password hashing | `UserController` (`password_hash` / `password_verify`) |
| Roles | Two roles only: `admin` and `agriculteur` (`User.role`, details in `PROJECT.md` §6). Stored in `$_SESSION['role']` at login |
| Session | `Views/includes/session.php`, included at the top of every page: starts the session and re-reads the user from the database (role/status changes apply without re-login; deleted or `inactif` account = logged out). Task B1 adds the cookie settings and the timeout to this file |
| Role check | Top of every protected page (`Views/admin/*` = `admin` only, modules = logged in) — shared guard functions still to be created (task B1) |
| CSRF token | Hidden field in POST forms, checked before calling the Controller |
| Server-side validation | Controllers (or the page, before calling the Controller) |
| Client-side validation | `Views/assets/js/validation.js` (rules as `data-*` attributes on the inputs) |
| XSS escaping | Views (`htmlspecialchars`) |

## 7. User management module (detail)

| Action | Page | Controller method | Allowed for |
|---|---|---|---|
| Sign up (always creates `agriculteur`) | `Views/register.php` ✅ | `UserController::addUser`, `emailExists` | visitor |
| Log in / log out (redirect by role) | `Views/login.php` ✅, `Views/logout.php` ✅ | `UserController::getUserByEmail` + `password_verify` | visitor / logged in |
| Profile, edit, password | `Views/profile.php` | `UserController::updateUser`, `changePassword` | `agriculteur`, `admin` |
| List (search, role filter, sort) | `Views/users/users.php` | `UserController::listUsers` | `admin` |
| Detail / edit (including role and statut) | `Views/users/editUser.php` | `getUserById`, `updateUser` | `admin` |
| Create (can choose the role) | `Views/users/addUser.php` | `addUser` | `admin` |
| Delete (POST + CSRF) | `Views/users/deleteUser.php` | `deleteUser` | `admin` |

✅ = implemented.

## 8. Known gaps in the current code

- `Views/admin/*` does not check the `admin` role yet, and forms have no CSRF token yet (tasks B1–B2).
- `Views/admin/dashboard.php` shows sample figures, including "Sols" and "Climat" cards that have no table in the schema.
- `config.php` prints the PDO error message with `die()` on connection failure; that should be logged instead (see `RULES.md` §4).
- `Views/includes/navbar.php`: the home link is computed as `index.php` when `$basePath` is not `''`, which is wrong for pages in `Views/<module>/` (`$basePath = '../'`).
