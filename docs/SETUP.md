# Setup Guide

## Requirements

- PHP 8.3+
- Composer
- Node.js 18+ and pnpm
- MySQL 8+

## Step-by-Step Setup

### 1. Install PHP dependencies

```bash
composer install
```

### 2. Configure environment

```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env`:
- Set `DB_DATABASE=typing_game`, `DB_USERNAME`, `DB_PASSWORD`
- Set `MAILER_SERVICE_URL=http://localhost:4001`

### 3. Create database and migrate

```sql
CREATE DATABASE typing_game;
```

```bash
php artisan migrate --seed
```

### 4. Install Node dependencies and build

```bash
npm install   # or: pnpm install
npm run dev   # for development with HMR
```

### 5. Start the mailer service

```bash
cd apps/mailer
cp .env.example .env
# Edit .env with SMTP credentials
npm install
npm start
```

### 6. Start Laravel

```bash
php artisan serve
```

Visit: http://localhost:8000
