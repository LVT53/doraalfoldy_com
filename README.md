<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

## Deployment Guide (Virtualmin Linux Server)

This project requires PHP 8.3, Tailwind CSS 4, and custom image processing. Follow these steps to deploy to a Virtualmin environment.

### 1. Prerequisites
Ensure your Virtualmin server has the following installed:
- **PHP 8.3** (with extensions: `bcmath`, `ctype`, `fileinfo`, `json`, `mbstring`, `openssl`, `pdo`, `tokenizer`, `xml`, `gd`)
- **MariaDB** or **MySQL**
- **Composer**
- **Node.js (v18+)** and **NPM**
- **Git**

### 2. Virtualmin Configuration
1. **Create Virtual Server**: In Virtualmin, go to **Create Virtual Server** and enter your domain.
2. **Set Public Directory**:
   - Go to **Server Configuration** > **Website Documents**.
   - Change **Website content directory** to `public_html/public`.
3. **PHP Version**: 
   - Go to **Server Configuration** > **PHP Versions**.
   - Ensure the domain is set to use **PHP 8.3**.

### 3. Deployment Steps

#### A. Clone the Repository
SSH into your server as the virtual server's user and run:
```bash
cd $HOME/public_html
git clone https://your-repository-url.git .
```

#### B. Environment Configuration
Create your `.env` file:
```bash
cp .env.example .env
nano .env 
```
Set `APP_ENV=production`, `APP_DEBUG=false`, and your `DB_*` settings.

#### C. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
php artisan key:generate --force
npm install
```

#### D. Build Assets & Process Images
```bash
npm run build
npm run generate:images
```

#### E. Database Migrations
```bash
php artisan migrate --force
```

### 4. Permissions & Ownership
Ensure the following directories are writable by the web server:
```bash
chmod -R 775 storage bootstrap/cache
```

### 5. Production Optimizations
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 6. SSL (Let's Encrypt)
In Virtualmin, go to **Server Configuration** > **SSL Certificate** > **Let's Encrypt** and click **Request Certificate**.

---

## Post-Deployment Workflow

### 1. Updating the Site (Git Pull)
When you push new changes to your repository, you can update the live server with a single command:

1. **SSH into the server** as the virtual server user.
2. **Navigate to the project directory**:
   ```bash
   cd $HOME/public_html
   ```
3. **Run the update command**:
   ```bash
   php artisan update
   ```

*Note: This custom command automatically performs `git pull`, updates Composer/NPM dependencies, builds assets, and refreshes the application cache.*

---

### 3. Customization & Assets

#### Changing the Favicon
The site uses two primary image files for the favicon and apple touch icon:
1.  **Standard Favicon:** Replace `public/images/content/favicon.jpg` with your new icon.
2.  **Apple Touch Icon:** Replace `public/images/content/webclip.jpg` with your new icon.
*Note: After replacement, you may need to clear your browser cache to see the new icons.*

#### Standardized Text Sizes
The project uses global defaults for typography to ensure visual consistency:
- **Default (Mobile):** 14px
- **Default (Desktop):** 16px
- **Headings:** Defined via `h1`, `h2`, `h3` tags in `app.css`.
To maintain this, avoid adding manual `text-base`, `text-lg`, etc., classes to paragraph elements unless absolutely necessary for a specific design exception.

---

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.
