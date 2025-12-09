# 📘 My Laravel API Project

This is a Laravel-based backend that supports:
- Email & Password Authentication (via Laravel Breeze)
- Google Login (via Laravel Socialite)
- REST API for mobile and web clients

---

## 🛠️ Requirements

Before running this project, make sure you have:

- **PHP** >= 8.1  
- **Composer**  
- **Node.js** & **NPM**  
- **MySQL** or **MariaDB**  
- **Git** (optional, for cloning)

---

## 🚀 Installation

### 1️⃣ Clone the repository
```bash
git clone https://github.com/muhammadrizaaa/litera_backend
cd yourproject

composer install

cp .env.example .env

php artisan key:generate

php artisan migrate

composer require laravel/breeze --dev
php artisan breeze:install api
php artisan migrate

composer require laravel/socialite

composer require smalot/pdfParser

GOOGLE_CLIENT_ID=your_google_client_id
GOOGLE_CLIENT_SECRET=your_google_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback