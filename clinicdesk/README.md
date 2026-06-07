# ClinicDesk — Clinic Management Dashboard

A login-protected clinic management system built with PHP, MySQL, and AdminLTE 3.

## Tech Stack
- **Backend:** PHP 8.x (OOP, no framework)
- **Database:** MySQL / MariaDB
- **Frontend:** AdminLTE 3 (offline, no CDN)
- **Architecture:** Front Controller, MVC-like pattern

## Roles
| Role    | Access |
|---------|--------|
| Admin   | Full system management |
| Doctor  | Own schedule & prescriptions |
| Patient | Book appointments, view history |

## Setup

### 1. Database
```bash
mysql -u root -p < clinicdesk_db.sql
```

### 2. Configure credentials
```bash
cp config/database.example.php config/database.php
# Edit database.php with your MySQL credentials
```

### 3. Generate admin password hash
```php
echo password_hash('Admin@1234', PASSWORD_BCRYPT);
# Paste the output into the users seed INSERT in clinicdesk_db.sql
```

### 4. Download AdminLTE 3
- Download from https://adminlte.io (free, MIT licence)
- Extract to `public/assets/adminlte/`

### 5. Configure BASE_URL
Edit `config/config.php` and set `BASE_URL` to match your local server.

## Project Structure
```
clinicdesk/
├── index.php          ← Front controller
├── .htaccess          ← Route all requests to index.php
├── config/            ← App settings & DB credentials
├── core/              ← Database, Auth, CSRF, Paginator, helpers
├── models/            ← One class per database table
├── controllers/       ← Business logic, one per feature
├── views/             ← HTML templates (AdminLTE partials)
└── public/            ← Assets & uploads
```

## Default Login
- **Email:** admin@clinic.local  
- **Password:** Admin@1234

## Security Features
- CSRF tokens on every POST form
- Prepared statements — zero SQL injection
- Password hashing with bcrypt
- XSS protection via `e()` helper
- Session fixation prevention
- File type validation (getimagesize / finfo)
- Prescriptions served through PHP, never directly

## ملاحظات قبل التسليم

- `config/database.php` — لا يُرفع على GitHub (موجود في .gitignore)
- `config/database.example.php` — نسخة آمنة للـ GitHub بدون بيانات حساسة
- `ini_set('display_errors', '0')` — مفعّل في index.php للإنتاج
- `setup.php` — احذفه بعد الإعداد الأولي
