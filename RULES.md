# RULES.md — Rules for the AI agent

This file is addressed to the AI agent (Claude Code, Copilot, Cursor, etc.) working on this project.
**Read this file, `PROJECT.md` and `ARCHITECTURE.md` before making any change, then look at the actual files.**
If the docs and the code disagree, trust the code and point out the mismatch to the user.

---

## 1. Git rules (NON-NEGOTIABLE)

The user handles Git personally: the work is graded on traceable individual contributions on GitHub.

- ❌ By default, **never** run `git commit`, `git push`, `git add`, `git merge`, `git rebase`, `git reset`, `git checkout -b`, `git stash`, `git tag`, and never create or delete a branch or Pull Request.
- ✅ **Exception**: when the user explicitly asks in chat ("commit", "push"…), you may do exactly what was asked (`add`, `commit`, create a branch, `push`) — nothing more, and only for that request.
- ❌ Never use `--force` or `--no-verify`, never rewrite history, never merge into `main` / `develop` without being asked.
- ✅ Read-only commands are always allowed: `git status`, `git diff`, `git log`, `git branch` (list).
- ✅ When a task is finished, **suggest** at the end of your reply:
  - the list of modified/created files,
  - a suggested commit message (format `type(scope): description`, e.g. `feat(users): add user CRUD`),
  - a suggested branch name (e.g. `feature/users`).
- The user reviews, commits and pushes.

## 2. Scope of work

- Work only on the requested task. Do not refactor unrelated files.
- Do not modify another team member's module folder (`Views/<module>/`, its Controller, its Model) unless the user explicitly asks.
- Shared files (`config.php`, `schema.sql`, `Views/includes/*`, `Views/admin/includes/*`, `Views/assets/css/*`) affect everyone: change them only when asked, and say so in the reply.
- Never delete a file without confirmation.
- If a request is ambiguous, ask one short question before coding.

## 3. MVC architecture (Esprit workshop style — mandatory)

Follow `ARCHITECTURE.md`. The project uses the MVC pattern taught in the Esprit web workshop:

- **Models** (`Models/`): entity classes only — private attributes matching the table columns, constructor, getters/setters. No SQL, no HTML.
- **Controllers** (`Controllers/`): one class per entity with the CRUD methods. They get the PDO connection with `config::getConnexion()` and run **prepared** queries. Validation and authorization checks go here too. No HTML.
- **Views** (`Views/`): pages that display data and forms. A View includes its Controller, calls its methods, and renders the result (HTML + minimal PHP: `foreach`, `if`, `echo`). No SQL.
- There is **no router**: each page is a PHP file opened directly in the browser.
- Two interfaces: **Front Office** (farmer, pages in `Views/`) and **Back Office** (admin, `Views/admin/`). Reuse the existing includes; do not copy the header/navbar/sidebar into pages.
- Do not change the folder layout or the `config` class without asking.

## 4. Security (mandatory)

- **SQL**: PDO prepared statements only (`prepare` + `execute` with bound values). Never concatenate variables into a query.
- **Passwords**: `password_hash()` and `password_verify()`. Never store plain-text passwords, never use MD5/SHA1.
- **Sessions — always include `Views/includes/session.php`**:
  - Every page (Front and Back Office) must start with this include, **as its first line of code, before any HTML**. Never call `session_start()` directly in a page (only exception: `Views/logout.php`, which just destroys the session).
  - What it does: starts the session and re-reads the logged-in user from the database on every page. A role change (`agriculteur` ⇄ `admin`) is applied at the next page load without logging out, and a deleted or `inactif` account is logged out automatically.
  - The path depends on where the page is:
    ```php
    require_once __DIR__ . '/Views/includes/session.php';     // index.php (project root)
    require_once __DIR__ . '/includes/session.php';           // Views/login.php, Views/register.php…
    require_once __DIR__ . '/../includes/session.php';        // Views/admin/*.php, Views/<module>/*.php
    ```
    (A module can wrap it in its own `includes/init.php`, like `Views/materiel/includes/init.php`.)
  - Shared file: improve it (cookie settings, timeout — task B1) instead of creating a second session file.
  - Other session rules: `session_regenerate_id(true)` after login, full destruction on logout (`Views/logout.php`), inactivity timeout, `HttpOnly` / `SameSite` cookies. Follow the workshop provided by the teacher.
- **Authorization (RBAC)**: the app supports exactly two roles, `admin` and `agriculteur` (see `PROJECT.md` §6). Every protected page and action checks the role server-side. Hiding a menu link is not enough — `Views/admin/*` must refuse non-admins. Sign-up always creates `agriculteur`; never let a form choose `admin` except the admin's user management. Do not add a role without asking.
- **XSS**: every displayed value goes through `htmlspecialchars($v, ENT_QUOTES, 'UTF-8')`.
- **CSRF**: token in every POST form (including edit and delete). Forms that change data use `method="post"`; deletions use POST, never a GET link.
- **Uploads** (forum images): check the real MIME type (`finfo`), extension and size; rename the file; never trust the client file name.
- Never show raw PHP/SQL errors to the user (no `$e->getMessage()` in the page); log them with `error_log()`.
- `config.php` holds the local XAMPP credentials (`root` / empty password). Never put a real/production password in it.

## 5. Input validation (mandatory)

Every form (sign-up, login, profile, password, admin forms, every module form) **must be validated on BOTH sides — JavaScript AND PHP**. One side alone is never acceptable, even though the teacher's instructions say "PHP et/ou JavaScript".

1. **JavaScript** (user comfort, before submit): required fields, email format, minimum password length, password confirmation, lengths, dates, allowed values, clear error messages. HTML5 attributes (`required`, `type="email"`) alone do **not** count as the JS validation.
2. **Server-side PHP** (security, source of truth): `trim`, required fields, `filter_var(FILTER_VALIDATE_EMAIL)`, lengths, allowed values (whitelists for `role`, `statut` (`actif` / `inactif` for users), `etat`, `type_operation`), foreign keys exist, unique email.

Both sides check the same rules. JS validation never replaces PHP validation (it can be bypassed). Show errors next to the fields and keep the entered values.
A task that adds or changes a form is not done until both validations exist and have been tested (JS errors in the browser, and PHP errors with JS disabled or a forged request).
`Views/assets/js/main.js` is UI only (mobile menu, scroll animations): put form validation in a separate script.

## 6. Code conventions

- PHP 8+. Add `declare(strict_types=1);` at the top of new Model and Controller files.
- Classes in `PascalCase` (`User`, `UserController`), methods/variables in `camelCase`, tables and columns **exactly as in `schema.sql`** (`id_user`, `date_pub`, etc.).
- One file = one class. One Model and one Controller per entity.
- The UI is in **French** (`<html lang="fr">`). Code comments: short, French or English, consistent with the file; only when the "why" is not obvious.
- Include files with `__DIR__` (`require_once __DIR__ . '/../../Controllers/UserController.php';`), never with paths relative to the current working directory.
- Page order: 1) `require_once` of `session.php` (§4), 2) Controllers, 3) `$pageTitle`, `$currentPage`, `$basePath`, 4) request handling, 5) HTML with the shared head/navbar (see `ARCHITECTURE.md` §4).
- No dead code, no leftover `var_dump` / `print_r` / `die` debug output.

## 7. Database

- The single source of truth is `schema.sql` (at the project root), which follows the diagram in `docs/diagram.md`. Do not rename or add tables/columns without asking; if the schema changes, update `docs/diagram.md` and regenerate `docs/images/database.png`.
- If test data is needed, put it in `seed.sql` at the root (one admin + one farmer + sample rows, passwords hashed with `password_hash()`).

## 8. Quality and verification

Before saying a task is done:

- Check syntax: `php -l` on every modified PHP file (with XAMPP: `C:\xampp\php\php.exe -l file.php`).
- Open the pages in the browser (`http://localhost/web/AgriTech/...`) and test the flows: login, access denied by role, create / read / update / delete, invalid input.
- Update `README.md` if installation or features change.
- State clearly what was tested and what was not.

## 9. Reply style

- Reply in the user's language.
- Be concise: summarize what was done, list touched files, suggest the commit message.
- If you cannot do something, say so; do not fake it.
