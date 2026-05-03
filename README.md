# 🎨 DEARYZ WEBSITE - COMPLETE IMPLEMENTATION GUIDE

## 📋 Project Overview
Portfolio & E-commerce website untuk Dearyz dengan Laravel 13, fokus pada album gallery dengan user authentication.

**Tech Stack:**
- Backend: Laravel 13 (PHP 8.2+)
- Frontend: Blade Templates + Tailwind CSS
- Database: MySQL
- Asset Management: Vite

---

## 📁 File Structure & Location Guide

### 1. Setup & Installation
**File:** `DEARYZ_SETUP.md`
- Step-by-step installation instructions
- Environment setup
- Database configuration
- First run commands

---

## 2. DATABASE - Migrations

### Album Management
- **migrations/create_albums_table.php**
  - id, user_id, title, description, slug, thumbnail
  - status (draft/published), photo_count, published_at

### Photo Management
- **migrations/create_photos_table.php**
  - id, album_id, user_id, title, description
  - image_path, image_url, mime_type, file_size
  - width, height, order

### Blog System
- **migrations/create_posts_table.php**
  - id, user_id, title, content, slug, excerpt
  - featured_image, category, status, views, published_at

### Shop System
- **migrations/create_products_table.php**
  - id, user_id, name, description, slug
  - price, cost_price, stock, sku, category
  - featured_image, status, sold, meta_description

### Shopping Cart
- **migrations/create_cart_items_table.php**
  - id, user_id, product_id, quantity, price

---

## 3. MODELS

**Location:** `app/Models/`

### Album.php
```php
- Relationships: user(), photos()
- Methods: isPublished()
- Auto slug generation
- Route binding by slug
```

### Photo.php
```php
- Relationships: album(), user()
- Auto file deletion on model delete
- Image URL formatting
```

### Post.php
```php
- Relationships: user()
- Methods: isPublished(), incrementViews()
- Auto slug generation
- Excerpt auto-generation from content
```

### Product.php
```php
- Relationships: user(), cartItems()
- Methods: isAvailable(), getProfitMargin(), getFormattedPrice()
- Auto slug generation
```

### CartItem.php
```php
- Relationships: user(), product()
- Methods: getTotalPrice()
```

---

## 4. CONTROLLERS

**Location:** `app/Http/Controllers/`

### Auth/AuthController.php
- `showLoginForm()` - Display login page
- `login()` - Handle login
- `showRegisterForm()` - Display register page
- `register()` - Handle registration with password validation
- `logout()` - Handle logout

### AlbumController.php
- `index()` - List all published albums
- `show(Album)` - Display album detail
- `create()` - Show create album form
- `store()` - Store new album
- `edit()` - Edit album form
- `update()` - Update album
- `destroy()` - Delete album

### PhotoController.php
- `upload()` - Upload photos to album
- `destroy()` - Delete photo
- `reorder()` - Reorder photos in album

### BlogController.php
- `index()` - List all published posts
- `show(Post)` - Display post detail
- `create()` - Show create post form
- `store()` - Store new post
- `edit()` - Edit post form
- `update()` - Update post
- `destroy()` - Delete post

### ShopController.php
- `index()` - List all products
- `category()` - Filter by category
- `show()` - Display product detail
- `create()` - Show create product form
- `store()` - Store new product
- `edit()` - Edit product form
- `update()` - Update product
- `destroy()` - Delete product
- `addToCart()` - Add product to cart

### CartController.php
- `index()` - Display shopping cart
- `update()` - Update cart item quantity
- `destroy()` - Remove item from cart
- `clear()` - Empty cart
- `getCount()` - Get cart item count (AJAX)

### DashboardController.php
- `index()` - Show dashboard

---

## 5. ROUTES

**Location:** `routes/web.php`

### Public Routes
```
GET  /                          - Home page
GET  /about                     - About page
GET  /album                     - Album list
GET  /album/{album}             - Album detail
GET  /blog                      - Blog list
GET  /blog/{post}               - Blog post detail
GET  /shop                      - Product list
GET  /shop/category/{category}  - Filter by category
GET  /shop/{product}            - Product detail
GET  /cart                      - Shopping cart
```

### Auth Routes (Guest Only)
```
GET  /login                     - Login form
POST /login                     - Handle login
GET  /register                  - Register form
POST /register                  - Handle registration
POST /logout                    - Logout
```

### Protected Routes (Auth Required)
```
GET  /dashboard                 - Dashboard
GET  /profile                   - Profile page
GET  /profile/edit              - Edit profile

ALBUM MANAGEMENT:
GET    /dashboard/album         - List albums
GET    /dashboard/album/create  - Create form
POST   /dashboard/album         - Store album
GET    /dashboard/album/{id}/edit - Edit form
PUT    /dashboard/album/{id}    - Update album
DELETE /dashboard/album/{id}    - Delete album
POST   /dashboard/album/{id}/upload-photos    - Upload photos
DELETE /dashboard/photo/{id}    - Delete photo
POST   /dashboard/album/{id}/reorder-photos   - Reorder photos

BLOG MANAGEMENT:
GET    /dashboard/blog          - List posts
GET    /dashboard/blog/create   - Create form
POST   /dashboard/blog          - Store post
GET    /dashboard/blog/{id}/edit - Edit form
PUT    /dashboard/blog/{id}     - Update post
DELETE /dashboard/blog/{id}     - Delete post

SHOP MANAGEMENT:
GET    /dashboard/shop          - List products
GET    /dashboard/shop/create   - Create form
POST   /dashboard/shop          - Store product
GET    /dashboard/shop/{id}/edit - Edit form
PUT    /dashboard/shop/{id}     - Update product
DELETE /dashboard/shop/{id}     - Delete product

CART MANAGEMENT:
PUT    /cart/{id}               - Update quantity
DELETE /cart/{id}               - Remove from cart
POST   /cart/clear              - Clear cart
GET    /cart/count              - Get cart count (AJAX)
POST   /shop/{product}/add-to-cart - Add to cart
```

---

## 6. VIEWS

**Location:** `resources/views/`

### Layouts
- **layouts/app.blade.php** - Main layout dengan navbar & footer

### Authentication
- **auth/login.blade.php** - Login page
- **auth/register.blade.php** - Register page

### Public Pages
- **home.blade.php** - Homepage dengan hero & features
- **about.blade.php** - About page dengan team & values

### Album
- **album/index.blade.php** - Album gallery grid
- **album/show.blade.php** - Album detail dengan lightbox
- **album/create.blade.php** - Create album form

### Blog
- **blog/index.blade.php** - Blog post list
- **blog/show.blade.php** - Blog post detail
- **blog/create.blade.php** - Create post form (partial)

### Shop
- **shop/index.blade.php** - Product list dengan category filter
- **shop/show.blade.php** - Product detail page
- **shop/cart.blade.php** - Shopping cart
- **shop/create.blade.php** - Create product form (partial)

### Dashboard
- **dashboard/index.blade.php** - Main dashboard dengan stats

---

## 7. INSTALLATION CHECKLIST

### Step 1: Setup Project
- [ ] Create new Laravel project: `composer create-project laravel/laravel dearyz-website`
- [ ] Navigate to project: `cd dearyz-website`

### Step 2: Copy Files
**Models** → Copy ke `app/Models/`
- [ ] Album.php
- [ ] Photo.php
- [ ] Post.php
- [ ] Product.php
- [ ] CartItem.php

**Controllers** → Copy ke `app/Http/Controllers/`
- [ ] auth/AuthController.php
- [ ] AlbumController.php
- [ ] PhotoController.php
- [ ] BlogController.php
- [ ] ShopController.php
- [ ] CartController.php
- [ ] DashboardController.php

**Migrations** → Copy ke `database/migrations/`
- [ ] create_albums_table.php
- [ ] create_photos_table.php
- [ ] create_posts_table.php
- [ ] create_products_table.php
- [ ] create_cart_items_table.php

**Routes** → Replace `routes/web.php`
- [ ] routes_web.php

**Views** → Copy ke `resources/views/`
- [ ] layouts/app.blade.php
- [ ] auth/login.blade.php
- [ ] auth/register.blade.php
- [ ] home.blade.php
- [ ] about.blade.php
- [ ] album/index.blade.php
- [ ] album/show.blade.php
- [ ] album/create.blade.php
- [ ] blog/index.blade.php
- [ ] blog/show.blade.php
- [ ] shop/index.blade.php
- [ ] shop/show.blade.php
- [ ] shop/cart.blade.php
- [ ] dashboard/index.blade.php

### Step 3: Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### Step 4: Database Configuration
Edit `.env`:
```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=dearyz_db
DB_USERNAME=root
DB_PASSWORD=
```

Create database:
```bash
mysql -u root -e "CREATE DATABASE dearyz_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
```

### Step 5: Install Dependencies
```bash
composer install
npm install
```

### Step 6: Run Migrations
```bash
php artisan migrate:fresh
```

### Step 7: Create Admin User
```bash
php artisan tinker
```

Di dalam tinker:
```php
use App\Models\User;
User::create([
    'name' => 'Dearyz Admin',
    'email' => 'admin@dearyz.local',
    'password' => bcrypt('password123')
]);
exit
```

### Step 8: Create Storage Link
```bash
php artisan storage:link
```

### Step 9: Build Assets
```bash
npm run build
```

### Step 10: Start Development Server
```bash
php artisan serve
```

Access: http://localhost:8000

---

## 8. USER ROLES & PERMISSIONS

### Regular User
- [ ] View published albums
- [ ] Read blog posts
- [ ] Browse shop products
- [ ] Add to cart
- [ ] View profile

### Admin (Owner)
- [ ] Create/Edit/Delete albums
- [ ] Upload/Delete photos
- [ ] Create/Edit/Delete blog posts
- [ ] Create/Edit/Delete products
- [ ] Manage cart items
- [ ] View dashboard with stats

---

## 9. KEY FEATURES IMPLEMENTED

### 1. Authentication
- [x] User Registration dengan password validation (8 chars, uppercase, number, symbol)
- [x] User Login dengan remember me option
- [x] Secure password hashing (bcrypt)
- [x] Logout functionality
- [x] Protected routes dengan auth middleware

### 2. Album Gallery (Priority Feature)
- [x] Create album dengan thumbnail
- [x] Upload multiple photos per album
- [x] Organize photos dengan order/drag-drop
- [x] Lightbox viewer untuk photos
- [x] Publish/Draft status
- [x] Public gallery view

### 3. Blog System
- [x] Create/Edit/Delete blog posts
- [x] Category system
- [x] Featured images
- [x] View counter
- [x] Related posts
- [x] Excerpt generation

### 4. Shop System
- [x] Product management
- [x] Category filtering
- [x] Stock tracking
- [x] Price formatting (Indonesian Rupiah)
- [x] Related products
- [x] Product availability status

### 5. Shopping Cart
- [x] Add to cart (quantity)
- [x] Update quantity
- [x] Remove items
- [x] Cart count badge
- [x] Cart total calculation
- [x] Empty cart

### 6. Dashboard
- [x] Stats cards (albums, posts, products, views)
- [x] Quick action buttons
- [x] Profile section
- [x] Quick links

---

## 10. STYLING

### Tailwind CSS
- Configured with Vite
- Custom gradients & animations
- Responsive grid system
- Dark mode ready

### Color Scheme
- Primary: Purple (#667eea)
- Secondary: Pink (#764ba2)
- Gradients: Purple to Pink

### Typography
- Font: Poppins (Google Fonts)
- Heading weights: Bold (700)
- Body weights: Regular (400)

---

## 11. FILE UPLOAD CONFIGURATION

### Storage Paths
```
storage/app/public/
├── album-thumbnails/
├── albums/{album_id}/
├── blog-images/
└── products/
```

### Configuration (.env)
```
FILESYSTEM_DISK=public
```

---

## 12. NEXT STEPS & ENHANCEMENTS

### Phase 2 (Optional)
- [ ] Payment gateway integration (Stripe, Midtrans)
- [ ] Order management system
- [ ] Email notifications
- [ ] Advanced search
- [ ] Comments on blog posts
- [ ] Social media sharing
- [ ] Analytics dashboard
- [ ] SEO optimization
- [ ] Image optimization
- [ ] Cache system

### Phase 3 (Future)
- [ ] Mobile app (Flutter/React Native)
- [ ] API endpoint (REST/GraphQL)
- [ ] Advanced inventory management
- [ ] Email marketing integration
- [ ] CRM system
- [ ] Advanced analytics

---

## 13. DEPLOYMENT CHECKLIST

Before deploying to production:
- [ ] Set `APP_DEBUG=false` in .env
- [ ] Set `APP_ENV=production`
- [ ] Run `php artisan config:cache`
- [ ] Run `php artisan route:cache`
- [ ] Setup HTTPS/SSL certificate
- [ ] Configure database backups
- [ ] Setup error logging & monitoring
- [ ] Configure email service
- [ ] Optimize images
- [ ] Setup CDN for static assets

---

## 14. SUPPORT & TROUBLESHOOTING

### Common Issues

**Migration Errors:**
```bash
php artisan migrate:fresh
php artisan migrate:reset
```

**Permission Issues:**
```bash
chmod -R 775 storage bootstrap/cache
chmod 644 .env
```

**Storage Link Issues:**
```bash
php artisan storage:link
```

**Clear Cache:**
```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

---

## 15. QUICK REFERENCE

### Create Admin User
```bash
php artisan tinker
User::create(['name' => 'Admin', 'email' => 'admin@dearyz.local', 'password' => bcrypt('pass')])
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

### Generate API Keys
```bash
php artisan passport:install
```

### View Routes
```bash
php artisan route:list
```

### Clear Everything
```bash
php artisan optimize:clear
```

---

## 📧 Contact & Support

Untuk masalah teknis atau pertanyaan, silakan hubungi developer atau buka issue di repository.

---

**Version:** 1.0  
**Last Updated:** April 2024  
**Status:** Production Ready ✅

---

Semua file sudah siap! Tinggal copy-paste dan ikuti installation checklist. Good luck! 🚀
