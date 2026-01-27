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
When you push new changes to your repository, follow these steps to update the live server:

1. **SSH into the server** as the virtual server user.
2. **Navigate to the project directory**:
   ```bash
   cd $HOME/public_html
   ```
3. **Pull the latest changes**:
   ```bash
   git pull origin main
   ```
4. **Update dependencies and assets** (if necessary):
   ```bash
   composer install --optimize-autoloader --no-dev
   npm install
   npm run build
   php artisan migrate --force
   # Only if new images were added:
   npm run generate:images
   ```
5. **Clear/Re-cache**:
   ```bash
   php artisan optimize
   ```

### 2. Adding Content

#### Adding New Images
1. Upload your high-resolution images to `public/images/`.
2. Run the image generation script on the server:
   ```bash
   npm run generate:images
   ```
3. The script will create responsive variants in `public/images/variants/` and update `manifest.json`.

#### Adding Blocks to Pages
Most pages use Blade components located in `resources/views/components/sections/`.
Example of adding a new content section:
```html
<x-sections.content-card 
    title="New Service"
    text="Description goes here..."
    image="/images/my-new-image.jpg"
/>
```

#### Updating Masonry Grids (Gallery)
Gallery subpages (e.g., `resources/views/pages/szempilla-galeria.blade.php`) use an array of images. To add a new image, simply add a new entry to the `$images` array:
```php
$images = [
    ['src' => '/images/new-gallery-image.jpg', 'alt' => 'Optional alt text'],
    // ... existing images
];
```

#### Seasonal Notice Bar
You can enable a 100% wide blue notice bar at the top of the navbar for seasonal announcements across the entire site.
1. Open `config/site.php`.
2. Change the `notice` value:
```php
// To show a notice on all pages
'notice' => 'Szezonális információ: Februárban zárva tartunk!',

// To hide the bar
'notice' => null,
```
3. After changing the config on the server, run `php artisan config:cache` to apply the changes.

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
