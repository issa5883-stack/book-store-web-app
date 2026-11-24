# BooksWeb

BooksWeb is a free digital library built with Laravel. Visitors browse and download PDF books, registered users submit books of their own, and administrators review every submission before it goes public. The whole site is available in **English** and **Arabic** (with full right-to-left layout).

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Demo Accounts](#demo-accounts)
- [How It Works](#how-it-works)
- [Routes Overview](#routes-overview)
- [Project Structure](#project-structure)
- [Languages (English / Arabic)](#languages-english--arabic)
- [Running Tests](#running-tests)
- [Code Style](#code-style)
- [License](#license)

---

## Features

### Visitors
- Browse approved books on the home page: popular, latest and community-submitted books.
- Search by title or author, filter by category and source (library or users), and sort by newest, most popular or title.
- View a book's details and download its PDF (downloads are counted).

### Registered users
- Sign up and sign in.
- Submit books (PDF up to 50 MB, optional cover image up to 4 MB).
- Track each request in **My Books**: pending, approved or rejected (with the admin's reason).
- Edit or delete a request until it is approved. An edited request goes back to review.

### Administrators
- Separate admin sign-in and dashboard with library statistics.
- Add, edit and delete library books directly (published right away).
- Review user requests: approve, or reject with a reason.
- Manage categories and registered users.

### Interface
- One centered, responsive card for sign-in and sign-up, with a show/hide password button, a Caps Lock warning, a password strength meter and a live "passwords match" check.
- Each validation error appears under its own field.
- Language switcher on every page.

---

## Tech Stack

| Layer | Technology |
|---|---|
| Backend | PHP 8.3+, Laravel 13 |
| Database | MySQL (sessions, cache and queue use the database driver) |
| Views | Blade templates and Blade components |
| Styling | Hand-written CSS in `public/css/app.css` (no build step required) |
| Fonts | Geist (English), Cairo (Arabic) from Google Fonts |
| Testing | PHPUnit 12 |
| Code style | Laravel Pint |

---

## Requirements

- PHP **8.3 or newer** with the usual Laravel extensions (`pdo_mysql`, `mbstring`, `openssl`, `fileinfo`, …)
- Composer 2
- MySQL 8 (or MariaDB)
- Node.js and npm are **optional**. The pages load plain CSS from `public/`, so no frontend build is needed to run the site.

---

## Installation

1. **Clone the repository**

   ```bash
   git clone https://github.com/issa5883-stack/book-store-web-app.git booksweb
   cd booksweb
   ```

2. **Install PHP dependencies**

   ```bash
   composer install
   ```

3. **Create the environment file and app key**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Create the database and set the connection** in `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=booksweb
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Run the migrations and seed demo data**

   ```bash
   php artisan migrate --seed
   ```

   This creates an admin, a test user, the book categories and a set of demo books.

6. **Link the storage folder** so uploaded PDFs and covers are reachable:

   ```bash
   php artisan storage:link
   ```

7. **Start the server**

   ```bash
   php artisan serve
   ```

   Open <http://localhost:8000>.

---

## Demo Accounts

Created by `php artisan db:seed`. **For local development only. Change or remove them before deploying.**

| Role | Sign-in page | Email | Password |
|---|---|---|---|
| Administrator | `/admin/login` | `admin@example.com` | `Admin@12345` |
| User | `/login` | `user@example.com` | `User@12345` |

---

## How It Works

### Book life cycle

```
 User submits a book ──► pending ──► Admin approves ──► approved (public)
                            │
                            └──────► Admin rejects  ──► rejected (reason shown to the user)

 User edits a pending or rejected request ──► back to pending
 Admin adds a book ──────────────────────────► approved immediately
```

Every book has a **source**:

- `admin`: added by an administrator (the library's own books)
- `user`: submitted by a registered user

Only `approved` books are shown to the public.

### Authentication

| Who | Guard / check | Middleware |
|---|---|---|
| Users | Laravel's default `web` guard (`users` table) | `auth`, `guest` |
| Administrators | Separate `admins` table; the admin's id is kept in the session | `admin` (`App\Http\Middleware\AdminAuth`) |

### Files

Uploaded files are stored on the `public` disk:

- PDFs: `storage/app/public/books`
- Covers: `storage/app/public/covers`

---

## Routes Overview

| Area | Method & URL | Description |
|---|---|---|
| Public | `GET /` | Home page |
| | `GET /books` | Browse, search and filter books |
| | `GET /books/{book}` | Book details |
| | `GET /books/{book}/download` | Download the PDF |
| | `GET /language/{locale}` | Switch language (`en` / `ar`) |
| Guests | `GET/POST /sign_up` | Create an account |
| | `GET/POST /login` | Sign in |
| Users | `/my-books` (resource, no `show`) | Submit and manage book requests |
| | `POST /logout` | Sign out |
| Admin | `GET/POST /admin/login` | Admin sign in |
| | `GET /admin/dashboard` | Dashboard |
| | `/admin/books` (resource, no `show`) | Manage library books |
| | `GET /admin/requests`, `/admin/requests/{book}` | Review requests |
| | `PATCH /admin/requests/{book}/approve` · `/reject` | Approve or reject |
| | `/admin/categories` | Manage categories |
| | `/admin/users` | List and delete users |

Run `php artisan route:list --except-vendor` for the full list.

---

## Project Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── AuthController.php         # user sign up / sign in / logout
│   │   ├── BookController.php         # public library
│   │   ├── UserBookController.php     # "My Books" requests
│   │   ├── AdminController.php        # admin sign in, dashboard
│   │   ├── LanguageController.php     # language switch
│   │   └── Admin/                     # books, requests, categories, users
│   ├── Middleware/
│   │   ├── AdminAuth.php              # protects /admin
│   │   └── SetLocale.php              # applies the chosen language
│   └── Requests/BookRequest.php       # book upload validation
└── Models/                            # Admin, Book, Category, User

database/
├── migrations/
├── factories/
└── seeders/                           # AdminSeeder, CategorySeeder, DatabaseSeeder

lang/
├── ar.json                            # Arabic translations of the interface
├── ar/                                # Arabic validation messages
└── en/

public/css/app.css                     # the site's stylesheet

resources/views/
├── layouts/                           # app, admin, auth
├── components/                        # book-card, icon, flash, auth-field, …
├── partials/                          # shared scripts, form fields, pagination
├── auth/                              # sign_in, sign_up
├── admin/                             # admin pages
├── books/                             # public library pages
└── my-books/                          # user requests

tests/Feature/                         # feature tests
```

---

## Languages (English / Arabic)

- Supported languages are defined in `config/app.php` under `supported_locales`, each with its text direction (`ltr` / `rtl`).
- The chosen language is stored in the session and applied by the `SetLocale` middleware.
- Interface text is wrapped in `__('...')`. English text is the key, and the Arabic version lives in `lang/ar.json`.

To add a new string, write it in English in the view with `__('Your text')`, then add its translation to `lang/ar.json`.

---

## Running Tests

The tests run on an in-memory SQLite database (see `phpunit.xml`), so they never touch your MySQL data. The PHP `pdo_sqlite` extension is required.

```bash
php artisan test            # the whole suite
php artisan test --compact  # shorter output
php artisan test --filter=AuthPagesTest
```

Covered areas include the book submission and review flow, the home page, book sources, category seeding, language switching and the authentication pages.

---

## Code Style

PHP code follows the Laravel preset of [Laravel Pint](https://laravel.com/docs/pint):

```bash
vendor/bin/pint
```

---

## License

This project is open-source software licensed under the [MIT license](https://opensource.org/licenses/MIT).
