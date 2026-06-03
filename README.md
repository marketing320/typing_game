# 🐒 Typing Monkey

A mobile-first typing challenge game with rehearsal and official challenge modes, OTP verification, geofence enforcement, Phaser.js animated game scenes, and a full admin panel.

## Tech Stack

| Layer | Tech |
|-------|------|
| Backend | Laravel 13, PHP 8.3 |
| Frontend | Blade, Tailwind CSS v4, Alpine.js |
| Game Engine | Phaser.js 3 |
| Build | Vite 8 |
| Database | MySQL 8 |
| Mailer | Node.js + Nodemailer |
| Package Manager | pnpm (workspace) |

## Project Structure

```
typing-game/               ← monorepo root (Laravel app lives here)
├── app/
│   ├── Http/Controllers/  ← frontend + admin controllers
│   ├── Models/            ← 10 Eloquent models
│   └── Services/          ← 7 service classes
├── database/
│   ├── migrations/        ← 10 tables
│   ├── seeders/           ← admin, challenge, text, geofence seeds
│   └── factories/         ← test factories
├── resources/
│   ├── js/phaser/         ← Phaser.js game (scenes, objects, utils)
│   └── views/             ← Blade pages (frontend + admin)
├── routes/web.php         ← all routes
├── apps/
│   └── mailer/            ← Node.js OTP email service
└── packages/
    └── shared/            ← shared JS utilities (scoring, geofence, constants)
```

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+ and npm (or pnpm)
- MySQL 8+

## Installation

### 1. Clone / navigate to project

```bash
cd typing-game
```

### 2. Install PHP dependencies

```bash
composer install
```

### 3. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` and set:
```env
DB_DATABASE=typing_game
DB_USERNAME=root
DB_PASSWORD=yourpassword
MAILER_SERVICE_URL=http://localhost:4001
```

### 4. Create database

```sql
CREATE DATABASE typing_game;
```

### 5. Run migrations and seed

```bash
php artisan migrate --seed
```

### 6. Install Node dependencies and build assets

```bash
npm install
npm run dev     # development with HMR
# OR
npm run build   # production build
```

### 7. Start the mailer service

```bash
cd apps/mailer
cp .env.example .env
# Edit .env with your SMTP credentials
npm install
npm start
```

### 8. Start Laravel

```bash
# From project root:
php artisan serve
```

Visit: **http://localhost:8000**

## Admin Login

| Field | Value |
|-------|-------|
| URL | http://localhost:8000/admin/login |
| Email | admin@typingmonkey.local |
| Password | password |

**Change the password immediately after first login.**

## Testing

```bash
php artisan test
```

Covers: OTP generation/expiry, scoring calculation, geofence distance, challenge one-attempt rule, leaderboard ranking.

## Game Modes

### Rehearsal Mode
- No login required
- Unlimited practice attempts
- Live WPM, accuracy, mistakes displayed
- Results saved anonymously

### Challenge Mode
1. Geolocation check (if geofence enabled)
2. Email + username entry
3. OTP verification (expires in 10 min)
4. One attempt per challenge (unless admin enables daily retry)
5. Phaser.js game with animated monkey mascot
6. Backend recalculates final score

## OTP Setup

The mailer service sends OTP emails. Configure `apps/mailer/.env`:

```env
MAILER_PORT=4001
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USER=your@gmail.com
SMTP_PASS=yourapppassword
SMTP_FROM_EMAIL=noreply@yourapp.com
```

## Geofence Setup

1. Admin → Geofence → Create Rule
2. Enter venue lat/lng and radius in meters
3. Assign to a challenge → enable "Require Geofence"

## Scoring Formula

```
WPM = (correct_characters / 5) / (duration_seconds / 60)
Accuracy = (correct_characters / total_characters) × 100
```

Backend always recalculates — frontend scores are display only.

## Troubleshooting

| Problem | Solution |
|---------|----------|
| Migrations fail | Ensure MySQL is running and `typing_game` DB exists |
| OTP not received | Check mailer service is running on port 4001 |
| Vite assets missing | Run `npm run dev` or `npm run build` |
| 419 CSRF error | Clear browser cookies, ensure session driver is working |
| Geofence always blocks | Check lat/lng format (decimal degrees), check radius |

## Known Limitations

- Phaser game animation is embedded in the challenge/play page but typing input is DOM-based (not Phaser keyboard) for mobile compatibility
- Leaderboard shows the most recently active challenge by default
- No email resend cooldown UI (backend enforces 60s cooldown)
