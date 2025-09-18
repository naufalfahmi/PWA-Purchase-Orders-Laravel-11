# Admin PWA - Laravel Mobile Application

Aplikasi admin PWA (Progressive Web App) untuk manajemen barang dan purchase order dengan interface mobile yang responsif.

## 🚀 Fitur

- **Authentication System** - Login/logout dengan Laravel Auth
- **Dashboard** - Overview statistik dan quick actions
- **Purchase Order Management** - CRUD purchase order dengan status tracking
- **Data Barang Management** - CRUD data barang dengan kategori dan stok
- **Profile Management** - Update profile user
- **Mobile-First Design** - Interface khusus mobile dengan bottom navigation
- **PWA Support** - Installable sebagai aplikasi mobile
- **Responsive Design** - Menggunakan Tailwind CSS
- **CSS Mobile Fix** - Triple fallback protection untuk CSS loading di mobile

## 📱 Teknologi

- **Backend**: Laravel 11
- **Frontend**: Tailwind CSS + Alpine.js
- **Database**: SQLite (default) / MySQL / PostgreSQL
- **PWA**: Service Worker + Web App Manifest

## 🛠️ Instalasi

### Prerequisites
- PHP 8.2+
- Composer
- Node.js & NPM
- Database (SQLite/MySQL/PostgreSQL)

### Langkah-langkah

1. **Clone repository**
   ```bash
   git clone https://gitlab.com/mnaufalfahmi/munah-pwa.git
   cd munah-pwa
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Setup environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database setup**
   ```bash
   # Untuk SQLite (default)
   touch database/database.sqlite
   
   # Atau setup database lain di .env
   php artisan migrate
   php artisan db:seed
   ```

5. **Build assets**
   ```bash
   npm run build
   ```

6. **Jalankan server**
   ```bash
   php artisan serve
   ```

## 🔐 Login Credentials

**Demo Account:**
- Email: `admin@example.com`
- Password: `password`

## 📱 PWA Features

### Install App
- Buka aplikasi di browser mobile
- Tap "Add to Home Screen" atau "Install App"
- Aplikasi akan tersedia seperti native app

### Offline Support
- Service Worker menyimpan cache untuk offline access
- Aplikasi tetap berfungsi tanpa internet (terbatas)

### Mobile Optimizations
- Touch-friendly interface
- Bottom navigation bar
- Responsive design untuk berbagai ukuran layar
- Fast loading dengan optimized assets
- **CSS Mobile Fix** - Triple fallback protection

## 🗂️ Struktur Aplikasi

```
├── app/
│   ├── Http/Controllers/     # Controllers
│   └── Models/              # Eloquent Models
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Tailwind CSS
│   └── js/                 # JavaScript files
├── public/
│   ├── build/              # Compiled assets
│   ├── css/                # Fallback CSS
│   ├── icons/              # PWA icons
│   ├── manifest.json       # PWA manifest
│   └── sw.js              # Service Worker
└── routes/
    └── web.php             # Web routes
```

## 🎨 UI Components

### Mobile Layout
- **Header**: Title dan user info
- **Content**: Main content area dengan padding
- **Bottom Navigation**: 4-5 tab navigation (Dashboard, PO, Laporan, Barang, Profile)

### Navigation Tabs
1. **Dashboard** - Overview dan quick actions
2. **Purchase Order** - Manage purchase orders
3. **Laporan** - Reports (Owner only)
4. **Data Barang** - Manage inventory
5. **Profile** - User profile dan settings

## 📊 Database Schema

### Tables
- `users` - User authentication
- `roles` - User roles (Owner, Sales)
- `products` - Product/inventory data
- `suppliers` - Supplier data
- `sales` - Sales data
- `sales_transactions` - Purchase order management

### Relationships
- `User` belongs to `Role`
- `SalesTransaction` belongs to `Product` and `Sales`
- `Product` belongs to `Supplier`

## 🔧 Development

### Running in Development
```bash
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite dev server
npm run dev
```

### Building for Production
```bash
npm run build
```

## 📱 Mobile Testing

### Chrome DevTools
1. Buka Chrome DevTools
2. Toggle device toolbar
3. Pilih mobile device
4. Test responsive design

### Real Device Testing
1. Jalankan server dengan IP address
2. Akses dari device mobile
3. Test PWA installation
4. Test offline functionality

## 🚀 Deployment

### Production Setup
1. Set environment ke production
2. Build assets: `npm run build`
3. Optimize: `php artisan optimize`
4. Deploy ke server

### PWA Deployment Checklist
- [x] HTTPS enabled
- [x] Service Worker registered
- [x] Manifest.json accessible
- [x] Icons available
- [x] Offline functionality tested
- [x] CSS mobile fallback implemented

## 📝 API Endpoints

### Authentication
- `GET /login` - Login page
- `POST /login` - Login process
- `POST /logout` - Logout

### Protected Routes
- `GET /dashboard` - Dashboard
- `GET /sales-transaction` - List PO
- `POST /sales-transaction` - Create PO
- `GET /data-barang` - List barang
- `POST /data-barang` - Create barang
- `GET /reports` - Reports (Owner only)
- `GET /profile` - Profile page

## 🤝 Contributing

1. Fork repository
2. Create feature branch
3. Commit changes
4. Push to branch
5. Create Merge Request

## 📄 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## 🆘 Support

Untuk pertanyaan atau dukungan, silakan buat issue di repository ini.

---

**Admin PWA** - Modern mobile admin interface built with Laravel & Tailwind CSS