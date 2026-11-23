# PROJECT.md — AgriTech project context

## 1. Overview

**AgriTech** is a web application for managing and digitalizing a farm ("Agriculture intelligente et durable"). It brings together **five business modules** for farmers and an **administration interface** to manage the platform.

University group project at **Esprit School of Engineering**, built in **PHP following the MVC pattern** (Esprit web workshop style, no framework), validated in front of the teacher (Ms. IBN ELFEKIH Oumeima). The user interface is in **French**.

## 2. The two interfaces

| | Front Office | Back Office |
|---|---|---|
| Audience | Farmer | Administrator |
| Role | Use the five modules | Administer the modules and users |
| Location | `index.php`, `Views/`, `Views/<module>/` | `Views/admin/` |
| Visual identity | Green `#2E7D32` / `#1B4332` and gold `#D4A017` | Olive, sage and beige |
| Style | Top navbar, simple and intuitive | Side menu, dashboard + user management |
| CSS | `style.css` | `style.css` + `back.css` + `admin.css` |

**Back Office palette:** `#606C38` (olive) · `#283618` (dark olive) · `#A3B18A` (sage) · `#DAD7CD` (beige) · `#F5F3EB` (cream)

## 3. The five modules

| Module (menu label) | Folder | Content (from the diagram) |
|---|---|---|
| **Drones agricoles** | `Views/drones/` | Drones and drone missions (date, zone, status) |
| **Matériel agricole** | `Views/materiel/` | The user's farm equipment, grouped by category, with its condition |
| **Énergie** (durable et non durable) | `Views/energie/` | Energy production and consumption, by energy type and category |
| **Forum agricole** | `Views/forum/` | Publications (text + image) and comments between farmers |
| **Plantes et traitements** | `Views/plantes/` | The user's plants and their treatments (date, status) |

The Back Office adds `Views/admin/dashboard.php` and user management (`Views/users/users.php`).

## 4. Current state (what exists)

- ✅ Front Office shell: home page (`index.php`), login and sign-up pages (static prototypes), shared head/navbar/footer.
- ✅ Back Office shell: dashboard with sample figures, side menu.
- ✅ CSS themes for both interfaces, logo, UI script (`main.js`).
- ✅ `config.php` (PDO connection) and `schema.sql` (all 12 tables).
- 🕓 `Models/` and `Controllers/` are empty: no module, no authentication yet.
- 🕓 Module pages: menu links are already wired and activate when each module's entry file is created (see `ARCHITECTURE.md` §4.3).

## 5. Deliverable for the validation session

### 5.1 Complete user management (priority module)
- User CRUD (add, view, edit, delete) in MVC.
- Role management (`admin`, `agriculteur`).
- Authorization and access control to features by role.
- All business features of the module: sign-up, login, logout, profile, password change, list with search/filter/sort, enable/disable if relevant, etc.

### 5.2 Security and sessions
Follow the workshop (guide) provided by the teacher: authentication, password hashing, session management, page protection.

### 5.3 Input control and validation
All forms: validation on **both sides, JavaScript AND PHP** — mandatory for this project (see `RULES.md` §5).

### 5.4 Teamwork
- Each member's tasks defined and tracked in the repository's **GitHub Project Board**.
- Integration through **Git**: branches, commits, merges.
- **Validation on a single machine per group** (XAMPP): everything must be integrated and working on that machine.
- Individual contributions must be **traceable** on GitHub.

### 5.5 GitHub repository
- Complete `README.md`: presentation, features, installation, configuration, how to run.
- Strict compliance with the repository **naming rules** sent by email.

## 6. Roles and permissions

### 6.1 Supported roles

The application supports **exactly two roles**, stored in `User.role` (`ENUM('admin','agriculteur')`). No other role exists; do not add one without the team's agreement.

| Role | Value in the database | Label in the UI | How a user gets it | Interface |
|---|---|---|---|---|
| **Farmer** | `agriculteur` | Agriculteur | Default role: every account created through the sign-up page (`Views/register.php`) | Front Office |
| **Administrator** | `admin` | Administrateur | Only set by another admin (user management) or in `seed.sql` — **never** through the sign-up page | Back Office (+ Front Office) |

A **visitor** (not logged in) is not a role: it is simply the absence of a session.

Related rules:
- After login, the role is stored in the session (`$_SESSION['role']`) and checked **server-side** on every protected page.
- Login redirects by role: `admin` → `Views/admin/dashboard.php`, `agriculteur` → home page.
- Accounts also have a status (`User.statut`: `actif` / `inactif`): an `inactif` account cannot log in, whatever its role.

### 6.2 Permissions

| Action | Visitor | Farmer (`agriculteur`) | Admin (`admin`) |
|---|:-:|:-:|:-:|
| Sign up / log in | ✅ | — | — |
| Use the 5 modules (own data) | ❌ | ✅ | ✅ |
| Edit own profile / password | ❌ | ✅ | ✅ |
| Access the Back Office (`Views/admin/`) | ❌ | ❌ | ✅ |
| CRUD on all users, change roles, enable/disable accounts | ❌ | ❌ | ✅ |
| Manage reference data (equipment categories, drones, energy categories…) | ❌ | ❌ | ✅ |
| Moderate forum (delete any post/comment) | ❌ | ❌ | ✅ |

> An admin must not be able to delete, demote or disable themselves, and the last admin cannot be removed.

## 7. Tech stack

- **Language:** PHP 8+ (MVC, no framework, no router)
- **Database:** MySQL / MariaDB via PDO (`config.php`)
- **Front:** HTML5, CSS3, JavaScript; fonts Inter and Fraunces (Google Fonts)
- **Environment:** XAMPP (Apache + MySQL), project in `htdocs/web/AgriTech`
- **Versioning:** Git + GitHub (+ GitHub Project Board)

## 8. Team

| Member | Module / Responsibility | Folder | Branch |
|---|---|---|---|
| _to fill in_ | User management | `Views/users/`, login/register | `feature/users` |
| _to fill in_ | Drones | `Views/drones/` | `feature/drones` |
| _to fill in_ | Matériel | `Views/materiel/` | `feature/materiel` |
| _to fill in_ | Énergie | `Views/energie/` | `feature/energie` |
| _to fill in_ | Forum | `Views/forum/` | `feature/forum` |
| _to fill in_ | Plantes | `Views/plantes/` | `feature/plantes` |

## 9. Related documents

- `RULES.md` — rules for the AI agent
- `ARCHITECTURE.md` — MVC structure, folders, includes, database
- `README.md` — installation and running
