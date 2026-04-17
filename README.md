# Elementary School Learning Hub

A reusable, offline-capable **Student Learning Hub** for small elementary schools. Fork it, set a few environment variables, drop in your own logo, and run.

School identity (name, address, LRN, logo) is fully driven by configuration — no code changes required to adopt this for your school.

---

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configure Your School](#configure-your-school)
- [Replace the Logo](#replace-the-logo)
- [Default Seeded Accounts](#default-seeded-accounts)
- [Common Commands](#common-commands)
- [Project Structure](#project-structure)
- [Testing](#testing)
- [Contributing / For the Next Developer](#contributing--for-the-next-developer)

---

## Features

- **Student Management** — CRUD with 12-digit LRN validation, photo upload, grade-level scoping
- **Attendance Tracking** — Daily bulk entry, monthly summaries, dropout-risk flagging
- **Grades (Grades 1–6)** — DepEd-standard weighted calculation (WW 40% / PT 40% / QA 20%) with a draft → submitted → approved workflow
- **Kindergarten Assessments** — Five developmental domains, qualitative ratings (Beginning / Developing / Proficient)
- **Assignments & Learning Materials** — Teacher-created assignments with scoring, file upload for materials
- **DepEd Reports** — SF9 (Report Card) and SF10 (Form 137) generated as PDF via DomPDF
- **School Administration** — Teacher management, school year transitions, year-end student promotion workflow
- **Announcements** — Head Teacher posts with read-tracking
- **Offline Support (PWA)** — Service Worker, IndexedDB queue, sync on reconnect — designed for areas with intermittent internet
- **Role-Based Access** — Head Teacher (full access) and Teacher (own grade level only) roles enforced via Laravel Policies

---

## Tech Stack

| Layer | Technology |
|---|---|
| Framework | Laravel 12.x |
| Language | PHP 8.2+ |
| Database | MySQL 8.0+ (SQLite works for local testing) |
| Auth | Laravel Breeze |
| Frontend | Blade + Tailwind CSS 3.x + Alpine.js 3.x |
| Build | Vite |
| Offline | Workbox (Service Worker) + IndexedDB (`idb`) |
| PDF | barryvdh/laravel-dompdf |

---

## Requirements

- **PHP** 8.2 or higher
- **Composer** 2.x
- **Node.js** 18+ and **npm** 9+
- **MySQL** 8.0+ (or MariaDB 10.6+)
- A web server (Laravel's built-in `php artisan serve` is fine for development; XAMPP / Nginx / Apache for production)

---

## Installation

```bash
# 1. Clone the repository
git clone <your-repository-url>
cd elementary-learning-hub

# 2. Install dependencies
composer install
npm install

# 3. Set up environment
cp .env.example .env
php artisan key:generate

# 4. Edit .env — at minimum, set your database credentials and SCHOOL_* values
#    (see "Configure Your School" below)

# 5. Create the database (in MySQL)
#    CREATE DATABASE elementary_learning_hub CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

# 6. Run migrations and seeders
php artisan migrate --seed

# 7. Build front-end assets
npm run build      # for production
# OR
npm run dev        # for development with hot reload

# 8. Start the dev server
php artisan serve
```

Visit **http://localhost:8000** and log in with one of the [default seeded accounts](#default-seeded-accounts).

---

## Configure Your School

All school identity is driven by four environment variables in `.env`. Set them once and every page, PDF, and report will pick them up automatically.

```env
SCHOOL_NAME="Your Elementary School Name"
SCHOOL_LRN_ID="123456"
SCHOOL_ADDRESS="Street, Barangay, Municipality, Province"
SCHOOL_REGION="Region X"
```

| Variable | Used in |
|---|---|
| `SCHOOL_NAME` | Sidebar header, login page title, SF9 / SF10 PDF headers, browser tab title |
| `SCHOOL_LRN_ID` | DepEd school identifier on SF9 / SF10 reports |
| `SCHOOL_ADDRESS` | Login page subtitle, SF9 / SF10 PDF headers |
| `SCHOOL_REGION` | SF9 / SF10 PDF headers |

These are read by `config/school.php`. If you skip this step, the app will run using generic placeholder values.

You may also want to update:

- `APP_NAME="..."` — appears in browser tabs and Vite bundles
- `APP_URL=http://localhost:8000` — set to your production URL when deploying

### Customizing the PWA install name

The PWA install name (what shows up if a user installs the app to their home screen) is in `public/manifest.json`. This file is **static** and cannot read environment variables, so edit it directly:

```json
{
    "name": "Your School Learning Hub",
    "short_name": "Learning Hub",
    "description": "Student Learning Hub for Your School"
}
```

---

## Replace the Logo

The app ships with a generic graduation-cap-and-book SVG at `public/images/school-logo.svg`. To use your own school logo:

### Option 1 — Replace the SVG (recommended)

1. Save your school logo as **SVG** with the filename `school-logo.svg`.
2. Make sure it's roughly square (the layouts render it inside `w-10 h-10` and `w-20 h-20` rounded containers).
3. Replace the file at:
   ```
   public/images/school-logo.svg
   ```
4. Hard-refresh the browser (`Ctrl+Shift+R` / `Cmd+Shift+R`) to bypass cached favicons.

That's it. The Blade layouts (`resources/views/layouts/app.blade.php` and `resources/views/layouts/guest.blade.php`) reference this exact path, so no code changes are needed.

### Option 2 — Use a PNG or JPG instead

If you only have a raster logo:

1. Save it as `school-logo.png` (or `.jpg`) at `public/images/school-logo.png`.
2. Edit the two layout files to update the favicon `type` and `href`:

   **`resources/views/layouts/app.blade.php`** (around line 15 and line 45):
   ```html
   <link rel="icon" type="image/png" href="{{ asset('images/school-logo.png') }}">
   ...
   <img src="{{ asset('images/school-logo.png') }}" alt="{{ config('school.name') }} Logo" ...>
   ```

   **`resources/views/layouts/guest.blade.php`** (around line 11 and line 23): same two changes.

3. For best results, use a square image at least **256×256 px** with a transparent background.

### Option 3 — Update PWA icons

The installable PWA icons live at `public/icons/icon-192.svg` and `public/icons/icon-512.svg`. Replace these with your own square icons (192×192 and 512×512) if you want a custom icon when users install the PWA to their home screen.

---

## Default Seeded Accounts

Running `php artisan migrate --seed` creates one Head Teacher and seven Teacher accounts. **All passwords are `password` — change them immediately on first login.**

| Role | Email | Grade Level |
|---|---|---|
| Head Teacher | `headteacher@school.local` | (all) |
| Teacher | `kinder@school.local` | Kindergarten |
| Teacher | `grade1@school.local` | Grade 1 |
| Teacher | `grade2@school.local` | Grade 2 |
| Teacher | `grade3@school.local` | Grade 3 |
| Teacher | `grade4@school.local` | Grade 4 |
| Teacher | `grade5@school.local` | Grade 5 |
| Teacher | `grade6@school.local` | Grade 6 |

The `@school.local` domain is a non-routable placeholder. To customize, edit `database/seeders/UserSeeder.php` before running the seeder, or rename the accounts via the Teachers page after first login.

---

## Common Commands

```bash
# Development
composer install            # Install PHP dependencies
npm install                 # Install JS dependencies
npm run dev                 # Vite dev server with hot reload
npm run build               # Build production assets
php artisan serve           # Start Laravel dev server (http://localhost:8000)

# Database
php artisan migrate         # Run pending migrations
php artisan migrate:fresh --seed   # Drop all tables and re-run + seed (DESTRUCTIVE)
php artisan db:seed         # Run seeders only

# Cache (run after changing config or .env)
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Testing
php artisan test            # Run full test suite
php artisan test --filter=GradeCalculatorServiceTest   # Run a single test class
```

---

## Project Structure

```
elementary-learning-hub/
├── app/
│   ├── Http/
│   │   ├── Controllers/    # Route handlers (15 controllers)
│   │   └── Requests/       # Form request validation
│   ├── Models/             # Eloquent models (16 models)
│   ├── Services/           # Business logic (GradeCalculator, Report, Promotion, Sync, AuditLog)
│   └── Policies/           # Role-based authorization (7 policies)
├── config/
│   └── school.php          # School identity (driven by .env)
├── database/
│   ├── factories/          # Model factories for tests/seeding
│   ├── migrations/         # Schema migrations (20 files)
│   └── seeders/            # Initial data (users, school years, subjects)
├── public/
│   ├── images/
│   │   └── school-logo.svg # ← Replace this with your logo
│   ├── icons/              # PWA install icons (192/512)
│   ├── manifest.json       # PWA metadata
│   └── sw.js               # Service Worker (Workbox)
├── resources/
│   ├── css/                # Tailwind entry
│   ├── js/
│   │   ├── app.js
│   │   └── pwa/            # IndexedDB, sync queue, offline forms
│   └── views/              # Blade templates
├── routes/
│   ├── web.php             # All HTTP routes (web + offline-sync API)
│   └── auth.php            # Breeze auth routes
├── tests/
│   ├── Feature/            # Integration tests
│   └── Unit/               # Service unit tests
├── .env.example            # Template for environment vars
└── README.md               # This file
```

---

## Testing

```bash
php artisan test
```

The test suite covers:

- **Grade calculation** — `GradeCalculatorServiceTest` verifies the WW/PT/QA weighted formula and final grade averaging
- **Authentication** — Breeze login/logout/registration flows
- **Student CRUD** — Creation, validation, LRN uniqueness, role-based access
- **Grade workflow** — Draft → Submitted → Approved transitions and authorization

Tests use SQLite in-memory by default — no MySQL setup needed for testing.

---

## Contributing / For the Next Developer

If you're picking up this project for the first time:

1. **Run the tests** (`php artisan test`) — if they all pass, your environment is set up correctly.
2. **Spin up the dev server** and log in as the head teacher to explore the UI.
3. **Review the Features section above** for a feature-by-feature overview of what the app does.

### Architecture at a glance

- **Controllers** live in `app/Http/Controllers/` — one per primary resource (students, grades, attendance, etc.).
- **Business logic** lives in `app/Services/`, not in controllers. Notable services: `GradeCalculatorService`, `ReportService`, `PromotionService`, `SyncService`, `AuditLogService`.
- **Authorization** is enforced by Laravel Policies in `app/Policies/`. Teachers are scoped to their own grade level; head teachers see everything.
- **Views** use Blade layouts with Tailwind. The main authenticated layout is `resources/views/layouts/app.blade.php`; the guest/auth layout is `resources/views/layouts/guest.blade.php`.
- **Offline support** lives in `resources/js/pwa/` (IndexedDB queue) and `public/sw.js` (Workbox service worker). Queued changes sync when the client reconnects.
- **PDF generation** (SF9 / SF10) uses DomPDF; templates live under `resources/views/reports/`.

### Key business rules you should not break

- **Grade calculation:** WW 40% + PT 40% + QA 20%, final = average of Q1–Q4. Lives in `app/Services/GradeCalculatorService.php`. Tested.
- **Role scoping:** Teachers can only see/edit their own grade level. Enforced in Policies (`app/Policies/`) and at the query level. Never bypass this.
- **Kindergarten is special:** Uses qualitative ratings (Beginning / Developing / Proficient), not numbers. No general average computed.
- **LRN must be 12 digits and unique** — enforced in `StoreStudentRequest` and at the database level.
- **Decimal precision for grades:** `DECIMAL(5,2)`, never `FLOAT`.
- **Grade workflow:** `draft → submitted → approved → locked`. SF9/SF10 should only pull `approved` or `locked` grades.

---

## License

This codebase is provided as-is for educational and non-commercial use by elementary schools. If you use it for your school, consider sharing improvements back upstream so other schools benefit.
