# TASKS.md — Split of the validation work (5 members)

Source: the teacher's instructions for the next validation session (user management, security & sessions, input validation, teamwork on GitHub, README).

Principle: **user management is split across the 5 members** (so everyone has traceable commits on the priority module), and **each member also owns one business module**.
**Input validation is not a separate task**: it is a rule (`RULES.md` §5) — every member validates **their own forms on both sides, JavaScript AND PHP**.

> Replace **A–E** with the real names, then create one GitHub Project Board card per task (`[A1]`, `[B2]`…), assigned to its owner.

## Overview

| Member | User-management part | Business module | Branch(es) |
|---|---|---|---|
| **A** | User Model + Controller, sign-up, login, logout | Matériel | `feature/auth`, `feature/materiel` |
| **B** | Sessions & security (session helper, timeout, CSRF, page guards) | Drones | `feature/security`, `feature/drones` |
| **C** | Admin user CRUD (list, search/filter/sort, add, edit, delete) | Énergie | `feature/users-admin`, `feature/energie` |
| **D** | Profile, password change, roles & access rules | Forum | `feature/profile`, `feature/forum` |
| **E** | Enable/disable accounts, seed data, real dashboard, README & integration | Plantes | `feature/users-status`, `feature/plantes` |

## Order of work (dependencies)

1. **Day 1 — foundations (blocking, merge into `develop` first):** A1 (User Model/Controller) and B1 (session + CSRF helpers). Everyone else can build HTML/JS meanwhile.
2. **Then in parallel:** A2–A3, B2–B3, C, D, E1–E3, and each member's module.
3. **Last day:** E4 integration on the validation machine + full test checklist (bottom of this file).

---

## A — Authentication + Matériel

- **A1 — User Model & Controller** *(blocking)*
  - `Models/User.php` (attributes = `User` columns, getters/setters).
  - `Controllers/UserController.php`: `addUser`, `getUserById`, `getUserByEmail`, `listUsers`, `updateUser`, `deleteUser`, `emailExists` — prepared PDO queries only.
- **A2 — Sign-up** (`Views/register.php`)
  - Split "Nom complet" into `nom` + `prenom`; `method="post"`; CSRF field.
  - Rules (JS + PHP): required, email format, unique email (PHP), password ≥ 8 chars, confirmation matches; role forced to `agriculteur`.
  - `password_hash()`; errors next to the fields, values kept.
  - Since this is the first form, put the reusable JS checks in `Views/assets/js/validation.js` so the others can include it.
- **A3 — Login / logout** (`Views/login.php`, `Views/logout.php`)
  - `password_verify()`, `session_regenerate_id(true)`, store `id_user`, `nom`, `role` in session.
  - Redirect by role: admin → `admin/dashboard.php`, farmer → `../index.php`. Generic error message ("Email ou mot de passe incorrect").
  - Logout: empty `$_SESSION`, delete the cookie, `session_destroy()`; the "Déconnexion" link in the dashboard points to it.
  - Navbar: show "Connexion / Inscription" for visitors, name + "Déconnexion" when logged in.
- **A4 — Matériel module**: Models `Materiel`, `CategorieMateriel`; `MaterielController`; pages in `Views/materiel/` (entry `materiel.php`): categories CRUD (admin), equipment CRUD (farmer, own only); `etat` whitelist, category exists.

## B — Sessions & security + Drones

- **B1 — Security helpers** *(blocking — follow the teacher's workshop)*
  - Secure session start: add to the **existing** `Views/includes/session.php` (already included by every page, see `RULES.md` §4): `HttpOnly`, `SameSite=Lax`, `use_strict_mode`; inactivity timeout (e.g. 30 min) → logout + message.
  - CSRF: generate a token per session, hidden-field helper, check helper (with `hash_equals`).
  - Page guards: `requireLogin()`, `requireRole('admin')` → redirect to login or a 403 page.
  - Agree with the team on the file location (e.g. `Controllers/AuthGuard.php` or a shared include) and document it in `ARCHITECTURE.md`.
- **B2 — Protect the pages**: every page in `Views/admin/` requires `admin`; every module page requires login; a logged-in user who opens login/register is redirected.
- **B3 — Error handling**: `config.php` must log the PDO error (`error_log`) and show a generic message instead of `die($e->getMessage())`; simple 403 / error page.
- **B4 — Drones module**: Models `Drone`, `MissionDrone`; `DroneController`; pages in `Views/drones/` (entry `drones.php`): CRUD drones (admin) and missions (farmer, own missions only); `etat` / `statut` whitelists, valid date, drone exists.

## C — Admin user management + Énergie

- **C1 — Users list** (`Views/users/users.php`, admin only, Back Office layout + sidebar `$currentPage = 'users'`)
  - Table: nom, prénom, email, rôle, actions.
  - Search (nom/prénom/email), filter by role, sort by column — all through prepared queries (sort column from a whitelist).
- **C2 — Add / edit user** (`addUser.php`, `editUser.php`)
  - Admin can set the role (whitelist `admin` / `agriculteur`); same rules as sign-up; unique email except for the user itself; password optional on edit.
- **C3 — Detail & delete** (`showUser.php`, `deleteUser.php`)
  - Delete via **POST + CSRF** with a JS confirmation; an admin cannot delete themselves; cannot delete the last admin.
- **C4 — Énergie module**: Models `Energie`, `CategorieEnergie`, `ProductionConsommation`; `EnergieController`; pages in `Views/energie/` (entry `installations.php`): categories + energy sources (admin), production/consumption records (farmer, own only); `type_operation` ∈ Production/Consommation, valid date, energy exists.

## D — Profile, roles & access rules + Forum

- **D1 — Profile** (`Views/profile.php`, logged in): view and edit own `nom`, `prenom`, `email` (unique); navbar link "Mon profil".
- **D2 — Password change**: current password required (`password_verify`), new ≥ 8 chars + confirmation, re-hash; regenerate the session ID afterwards.
- **D3 — Roles & permissions**
  - Write the final permission table (who can do what) in `PROJECT.md` §6 and make sure every page follows it.
  - An admin cannot demote themselves; the last admin cannot be demoted.
  - Hide admin links for farmers **and** block them server-side (with B).
- **D4 — Forum module**: Models `Publication`, `Commentaire`; `ForumController`; pages in `Views/forum/` (entry `forum.php`): publish (text + image), comment, edit/delete own posts; admin moderation (delete any post/comment). Secure image upload: real MIME (`finfo`), extension, size, renamed file.

## E — Account status, data, dashboard & integration + Plantes

- **E1 — Enable / disable accounts**
  - Uses the `User.statut` column (`actif` / `inactif`, default `actif`), already in `schema.sql`.
  - Admin toggle in the users list (POST + CSRF, coordinate with C); a disabled user cannot log in (clear message, coordinate with A); an admin cannot disable themselves.
- **E2 — `seed.sql`**: one admin + two farmers (passwords hashed with `password_hash`), sample rows for every table; fill the "Test accounts" table in `README.md`.
- **E3 — Dashboard** (`Views/admin/dashboard.php`): replace sample numbers with real counts (users per role, active/inactive, drones, matériels, energy total, publications, plantes); remove or justify the "Sols" / "Climat" cards (no table for them).
- **E4 — Integration & repository**
  - Fix shared bugs: navbar home link for pages in `Views/<module>/`.
  - Keep `README.md` up to date (features, install, run, accounts, team table); check the repository name against the teacher's naming rules.
  - Set up the GitHub Project Board (columns *To do / In progress / Review / Done*, one card per task here).
  - Merge order into `develop`, then `develop` → `main`; install and test everything on the validation machine.
- **E5 — Plantes module**: Models `Plante`, `Traitement`; `PlanteController`; pages in `Views/plantes/` (entry `plantes.php`): plants CRUD (farmer, own only) and their treatments; `statut` whitelist, valid date, plant belongs to the user.

---

## Rules for everyone

- Follow `RULES.md` and `ARCHITECTURE.md` (Esprit MVC: Model = entity, Controller = PDO, View includes the Controller).
- **Input validation on both sides for every form you build: JavaScript (reuse `validation.js`) AND PHP.** HTML5 `required` alone does not count. A form without both is not done.
- Every form: `method="post"`, CSRF token, `htmlspecialchars` on output.
- Every module page checks the session; admin parts check the `admin` role.
- Work on your own branch, small commits `type(scope): description`, Pull Request into `develop` reviewed by another member.
- Move your card on the Project Board when you start / finish.

## Final test checklist (validation machine)

- [ ] Fresh install from `README.md` works (clone, import `schema.sql` + `seed.sql`, open the URL).
- [ ] Sign-up with invalid data → JS errors in the browser; with JS disabled → PHP errors; values kept. Valid → account created with a hashed password.
- [ ] Login wrong password → generic error; farmer → Front Office; admin → dashboard; disabled account → refused.
- [ ] Farmer opening `Views/admin/dashboard.php` or `Views/users/users.php` → refused.
- [ ] Visitor opening a module page → redirected to login.
- [ ] Inactivity timeout logs the user out; logout destroys the session.
- [ ] Admin: list / search / filter / sort / add / edit role / enable-disable / delete users; cannot delete, demote or disable themselves.
- [ ] Profile edit and password change work and are validated.
- [ ] Each module: create / read / update / delete with valid and invalid input (JS and PHP).
- [ ] Forged POST without CSRF token → rejected.
- [ ] Every member has commits on GitHub and cards on the Project Board.
