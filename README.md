# 🌱 AgriTech

Farm management web application in **PHP (MVC architecture)**, with a **Front Office** for farmers and a **Back Office** for administrators.

> University project — Esprit School of Engineering — _[course / year]_
> Group: _[group name]_

## 📋 Table of contents
1. [Features](#-features)
2. [Technologies](#-technologies)
3. [Architecture](#-architecture)
4. [Installation](#-installation)
5. [Configuration](#️-configuration)
6. [Running](#️-running)
7. [Test accounts](#-test-accounts)
8. [Security](#-security)
9. [Teamwork](#-teamwork)
10. [Team](#-team)

## ✨ Features

> Status: the interface shells (home, login, sign-up, admin dashboard) are in place; the modules below are being developed by each team member.

### Front Office (farmer — green & gold theme)
- 🚁 **Drones agricoles**: drones and missions (date, zone, status)
- 🚜 **Matériel agricole**: farm equipment by category and condition
- ⚡ **Énergie**: energy production and consumption tracking
- 💬 **Forum agricole**: publications (with image) and comments
- 🌿 **Plantes et traitements**: grown plants and treatment tracking
- 👤 Sign-up, login, profile, password change

### Back Office (administrator — olive, sage & beige theme)
- 📊 Dashboard
- 👥 **Complete user management**: CRUD, roles, search/filter, access control
- Module administration: drones, matériel, énergie, forum (moderation), plantes

### Cross-cutting
- Role management and per-role authorization — two roles (details: [`PROJECT.md`](PROJECT.md) §6):
  - `agriculteur` (farmer): default role at sign-up, uses the Front Office modules with their own data
  - `admin` (administrator): Back Office, user management, module administration — assigned only by another admin
- Account status `actif` / `inactif` (a disabled account cannot log in)
- Secure sessions
- Input validation on the client (JavaScript) and server (PHP)

## 🛠 Technologies

| | |
|---|---|
| Language | PHP 8+ |
| Database | MySQL / MariaDB (PDO) |
| Front | HTML5, CSS3, JavaScript |
| Local server | XAMPP (Apache + MySQL) |
| Versioning | Git, GitHub, GitHub Project Board |

## 🏗 Architecture

MVC (Esprit workshop style, no framework): **Models** are entity classes, **Controllers** run the PDO queries, **Views** are the pages, which include their Controller.

```
index.php  config.php  schema.sql
Models/  Controllers/
Views/  includes/  admin/  assets/  login.php  register.php  <module>/
```

- Details: [`ARCHITECTURE.md`](ARCHITECTURE.md) (folders, includes)
- Database diagram: [`docs/diagram.md`](docs/diagram.md) — image: [`docs/images/database.png`](docs/images/database.png)
- Context and scope: [`PROJECT.md`](PROJECT.md)

## 🚀 Installation

### Prerequisites
- XAMPP with PHP ≥ 8.0 (extensions `pdo_mysql`, `mbstring`, `fileinfo` — enabled by default)
- Git

### Steps

1. Clone the repository into XAMPP's `htdocs/web/` folder:
   ```bash
   cd C:/xampp/htdocs/web
   git clone https://github.com/<organization-or-user>/<repository-name>.git AgriTech
   ```
2. Start **Apache** and **MySQL** from the XAMPP Control Panel.
3. Create the database: in phpMyAdmin (http://localhost/phpmyadmin) open the **Import** tab and import `schema.sql`. The script creates the `agritech` database itself.
   Command-line alternative:
   ```bash
   C:/xampp/mysql/bin/mysql -u root < schema.sql
   ```

## ⚙️ Configuration

The database connection is in [`config.php`](config.php) (`config::getConnexion()`). Default values match a fresh XAMPP install:

| Setting | Value |
|---|---|
| Host | `localhost` |
| Database | `agritech` |
| User | `root` |
| Password | _(empty)_ |

If your MySQL setup differs, edit these values locally and do not commit them.

## ▶️ Running

| Interface | URL |
|---|---|
| Front Office (home) | http://localhost/web/AgriTech/ |
| Login | http://localhost/web/AgriTech/Views/login.php |
| Sign-up | http://localhost/web/AgriTech/Views/register.php |
| Back Office (dashboard) | http://localhost/web/AgriTech/Views/admin/dashboard.php |

## 🔑 Test accounts

No test accounts yet: they will be provided in `seed.sql` once authentication is implemented.

| Role | Email | Password |
|---|---|---|
| Admin | _to fill in_ | _to fill in_ |
| Farmer | _to fill in_ | _to fill in_ |

## 🔒 Security

Required for every module (see [`RULES.md`](RULES.md) §4):

- Hashed passwords (`password_hash`)
- PDO prepared statements (SQL injection protection)
- Secure sessions (ID regeneration, timeout, HttpOnly cookies)
- Server-side role-based access control
- CSRF protection on POST forms
- Output escaping (XSS protection)
- JS + PHP validation of all inputs

## 🤝 Teamwork

- **Tasks** tracked in the repository's **GitHub Project Board**.
- **Branches**: `main` (stable) ← `develop` ← `feature/<module>`.
- **Commits**: format `type(scope): description` (e.g. `feat(users): add CRUD`).
- Integration through **Pull Requests** reviewed by another member, then merged.
- Final validation on **a single machine** per group.

## 👥 Team

| Member | Responsibility |
|---|---|
| _First Last_ | User management |
| _First Last_ | Drones |
| _First Last_ | Matériel |
| _First Last_ | Énergie |
| _First Last_ | Forum |
| _First Last_ | Plantes |

## 📄 License

Academic project — educational use.
