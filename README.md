# Proposal Backend - Laravel 12.x

Backend API untuk sistem proposal yang dibangun dengan Laravel 12.x dan teknologi modern.

## 🚀 Fitur Utama

- **Laravel 12.x** dengan PHP 8.2+
- **Filament 3.2** untuk admin panel yang modern dan responsif
- **Laravel Sanctum** untuk API authentication
- **Spatie Laravel Permission + Laravel Shield** untuk authorization dan role management
- **Redis** untuk caching dan session storage
- **Laravel Queue** dengan Redis driver untuk background jobs
- **AWS S3** support untuk cloud storage
- **Laravel Octane** dengan FrankenPHP untuk performa tinggi
- **PHPUnit** untuk testing framework

## 📋 Persyaratan Sistem

- PHP 8.2 atau lebih tinggi
- Composer
- MySQL 8.0+
- Redis Server
- Node.js & NPM (untuk asset compilation)

## 🛠️ Instalasi

1. **Clone repository**
   ```bash
   git clone <repository-url>
   cd proposal-backend
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi database di .env**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=proposal_db
   DB_USERNAME=root
   DB_PASSWORD=
   ```

5. **Konfigurasi Redis di .env**
   ```env
   REDIS_CLIENT=predis
   REDIS_HOST=127.0.0.1
   REDIS_PASSWORD=null
   REDIS_PORT=6379
   
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

6. **Konfigurasi AWS S3 (opsional)**
   ```env
   AWS_ACCESS_KEY_ID=your_access_key
   AWS_SECRET_ACCESS_KEY=your_secret_key
   AWS_DEFAULT_REGION=us-east-1
   AWS_BUCKET=your_bucket_name
   ```

7. **Jalankan migrasi**
   ```bash
   php artisan migrate
   ```

8. **Seed data awal**
   ```bash
   php artisan db:seed
   ```

## 🚀 Menjalankan Aplikasi

### Development Server
```bash
# Server biasa
php artisan serve

# Atau dengan Laravel Octane untuk performa tinggi
php artisan octane:start --server=frankenphp
```

### Queue Worker
```bash
php artisan queue:work redis
```

## 🔐 Authentication & Authorization

### API Authentication (Laravel Sanctum)
- Endpoint login: `POST /api/login`
- Endpoint register: `POST /api/register`
- Endpoint logout: `POST /api/logout`
- Header authentication: `Authorization: Bearer {token}`

### Admin Panel (Filament)
- URL: `/admin`
- Dilengkapi dengan Laravel Shield untuk role dan permission management
- Dashboard yang responsif dan modern

## 📦 Package Utama

- `laravel/framework`: ^12.0
- `filament/filament`: ^3.2
- `laravel/sanctum`: ^4.0
- `spatie/laravel-permission`: ^6.0
- `bezhansalleh/filament-shield`: ^3.0
- `laravel/octane`: ^2.0
- `predis/predis`: ^2.0
- `league/flysystem-aws-s3-v3`: ^3.0

---

**Dibuat dengan ❤️ menggunakan Laravel 12.x dan Filament 3.2**
