# Agricultural Marketplace Management System

A comprehensive agricultural marketplace platform built with Laravel 11 and Vue.js 3, featuring dynamic role-based access control (RBAC), product management, RFQ system, and enterprise-grade governance tools.

## 🚀 Features

### Core Functionality
- **Dynamic Role-Based Access Control (RBAC)**: Granular permission system with roles, permissions, and user management
- **User Authentication**: Secure registration, login, and profile management with Laravel Sanctum
- **Product Marketplace**: Browse, search, and manage agricultural products with categories
- **RFQ System**: Request for Quotation management for buyers and vendors
- **Subscription Management**: Tiered subscription plans for marketplace access
- **Messaging System**: Communication between buyers and vendors
- **Notification System**: Real-time notifications and broadcast announcements

### Admin Features
- **Super Admin Dashboard**: Comprehensive governance nexus with user statistics and analytics
- **Role Management**: Create, edit, and manage custom roles with granular permissions
- **User Management**: Manage user accounts, roles, and access control
- **Product Moderation**: Approve, reject, and moderate vendor product listings
- **Category Management**: Organize products with hierarchical categories
- **Broadcast Center**: Send system-wide notifications to users

### Vendor Features
- **Inventory Management**: Add, edit, and manage product listings
- **RFQ Responses**: Respond to buyer requests for quotations
- **Sales Dashboard**: Track sales and product performance

### Buyer Features
- **Product Discovery**: Browse and search agricultural products
- **RFQ Creation**: Submit requests for quotations to vendors
- **Order Management**: Track and manage orders

## 🛠️ Technology Stack

### Backend
- **Laravel 11**: PHP web framework
- **PHP 8.2+**: Server-side scripting
- **Laravel Sanctum**: API authentication
- **PostgreSQL/MySQL**: Database (configurable)

### Frontend
- **Vue.js 3**: Progressive JavaScript framework with Composition API
- **Vue Router 4**: Client-side routing with authentication guards
- **Pinia**: State management for Vue.js
- **Tailwind CSS**: Utility-first CSS framework
- **Axios**: HTTP client for API requests

### Development Tools
- **Vite**: Fast build tool and development server
- **Composer**: PHP dependency manager
- **NPM**: JavaScript package manager
- **Laravel Pint**: Code style fixer

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer 2.x
- Node.js 18+ and NPM
- PostgreSQL 14+ or MySQL 8.0+
- Git

## 🚀 Installation & Setup

### 1. Clone the Repository
```bash
git clone <repository-url>
cd management
```

### 2. Install Dependencies
```bash
# Install PHP dependencies
composer install

# Install Node.js dependencies
npm install
```

### 3. Environment Configuration
```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file and set your database credentials:
```env
DB_CONNECTION=pgsql  # or mysql
DB_HOST=127.0.0.1
DB_PORT=5432         # 3306 for MySQL
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 5. Run Migrations
```bash
php artisan migrate
```

### 6. Seed Dynamic Roles & Permissions (CRITICAL)

The application uses a **dynamic role-based permission system**. You MUST run the following seeders in order:

#### Option A: Run All Seeders (Recommended for Development)
```bash
# This runs RBACSeeder, SuperAdminSeeder, and sample data
php artisan db:seed
```

#### Option B: Run Specific Seeders (Production)
```bash
# 1. Create roles and permissions (REQUIRED)
php artisan db:seed --class=RBACSeeder

# 2. Create super admin user (REQUIRED)
php artisan db:seed --class=SuperAdminSeeder

# 3. Optional: Create product categories
php artisan db:seed --class=ProductCategorySeeder

# 4. Optional: Create subscription plans
php artisan db:seed --class=SubscriptionPlanSeeder

# 5. Optional: Create sample products (development only)
php artisan db:seed --class=ProductSeeder
```

#### Seeder Details

**RBACSeeder** (REQUIRED)
- Creates all system roles: `super_admin`, `admin`, `vendor`, `user`, `buyer`, `trader`
- Creates all permissions: `access_super_admin`, `manage_users`, `moderate_products`, etc.
- Links permissions to roles

**SuperAdminSeeder** (REQUIRED)
- Creates the initial super admin account
- Email: `test@example.com`
- Password: `password`
- Assigns all permissions to super admin role
- Links super admin user to super_admin role

**ProductCategorySeeder** (Optional)
- Creates sample product categories

**SubscriptionPlanSeeder** (Optional)
- Creates subscription tiers for marketplace access

**ProductSeeder** (Optional)
- Creates sample products for testing

### 7. Build Frontend Assets
```bash
# Development build with hot reload
npm run dev

# Production build
npm run build
```

### 8. Start Development Servers
```bash
# Terminal 1: Start Laravel server
php artisan serve

# Terminal 2: Start Vite dev server (if using npm run dev)
npm run dev
```

The application will be available at `http://localhost:8000`

## 🔐 Default Credentials

After running the seeders, you can login with:

**Super Admin Account**
- Email: `test@example.com`
- Password: `password`

**Important**: Change these credentials immediately in production!

## 🏗️ Dynamic Roles & Permissions System

### System Roles

The application includes the following pre-configured roles:

| Role | Slug | Description |
|------|------|-------------|
| Super Admin | `super_admin` | Full system access, can manage everything |
| Admin | `admin` | Can moderate products and manage users |
| Vendor | `vendor` | Can manage inventory and respond to RFQs |
| User/Buyer | `user` | Can browse products and create RFQs |

### Key Permissions

| Permission | Description |
|------------|-------------|
| `access_super_admin` | Access to Global Governance Nexus |
| `access_admin` | Access to Regulator Suite |
| `access_vendor` | Access to Sales Suite |
| `manage_users` | Manage system users and roles |
| `moderate_products` | Approve/reject product listings |
| `manage_roles` | Create and edit custom roles |
| `broadcast_notifications` | Send system-wide notifications |
| `manage_categories` | Manage product categories |
| `manage_warehouse` | Manage product inventory |

### Creating Custom Roles

1. Login as Super Admin
2. Navigate to **Super Admin → Role Ecosystem**
3. Click **Initialize Role**
4. Enter role name, description, and select permissions
5. Click **Initialize Role**

Users assigned to custom roles will automatically inherit the selected permissions.

## 📊 Project Structure

```
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # API Controllers
│   │   │   ├── Admin/            # Admin Controllers
│   │   │   └── SuperAdminController.php
│   │   └── Middleware/
│   ├── Models/
│   │   ├── User.php              # User model with HasRoles trait
│   │   ├── Role.php              # Role model
│   │   ├── Permission.php        # Permission model
│   │   └── Product.php
│   ├── Traits/
│   │   └── HasRoles.php          # Dynamic role checking trait
│   └── Mail/                     # Email notifications
├── database/
│   ├── migrations/               # Database migrations
│   └── seeders/
│       ├── RBACSeeder.php        # Roles & permissions
│       ├── SuperAdminSeeder.php  # Super admin user
│       └── DatabaseSeeder.php    # Main seeder
├── resources/
│   ├── js/
│   │   ├── components/           # Vue components
│   │   ├── pages/
│   │   │   ├── super-admin/      # Super admin pages
│   │   │   ├── admin/            # Admin pages
│   │   │   └── vendor/           # Vendor pages
│   │   ├── stores/
│   │   │   └── auth.js           # Auth store with can() method
│   │   └── router/
│   │       └── index.js          # Route definitions
│   └── views/
│       └── emails/               # Email templates
├── routes/
│   ├── api.php                   # API routes
│   └── web.php                   # Web routes
└── public/                       # Public assets
```

## 🔗 Key API Endpoints

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user with roles

### Super Admin
- `GET /api/super-admin/stats` - Dashboard statistics
- `GET /api/super-admin/users` - List all users
- `GET /api/super-admin/roles` - List roles and permissions
- `POST /api/super-admin/roles` - Create new role
- `PUT /api/super-admin/roles/{id}` - Update role
- `DELETE /api/super-admin/roles/{id}` - Delete role

### Admin
- `GET /api/admin/users` - List users
- `GET /api/admin/roles` - List assignable roles
- `POST /api/admin/users` - Create user
- `PUT /api/admin/users/{id}` - Update user
- `GET /api/admin/products` - List products for moderation
- `POST /api/admin/products/{id}/approve` - Approve product
- `POST /api/admin/products/{id}/reject` - Reject product

### Products
- `GET /api/products` - List marketplace products
- `POST /api/products` - Create product (vendors)
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

### RFQs
- `GET /api/rfqs` - List RFQs
- `POST /api/rfqs` - Create RFQ
- `PUT /api/rfqs/{id}` - Update RFQ status

## 🎨 Frontend Architecture

### State Management (Pinia)

**Auth Store** (`stores/auth.js`)
```javascript
// Check if user has a specific permission
authStore.can('manage_users')

// Check if user has a specific role
authStore.user.roles.some(r => r.slug === 'admin')

// Legacy getters (backward compatible)
authStore.isVendor
authStore.isAdmin
authStore.isSuperAdmin
```

### Route Guards

Routes are protected based on permissions:
```javascript
{
  path: '/super-admin',
  component: SuperAdminDashboard,
  meta: { requiresAuth: true, permission: 'access_super_admin' }
}
```

### Dynamic Sidebar

The sidebar automatically shows/hides menu items based on user permissions using the `can()` helper.

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📜 Available Scripts

### Composer Scripts
```bash
composer run setup      # Complete project setup
composer run dev        # Start development servers
composer run test       # Run test suite
```

### NPM Scripts
```bash
npm run dev            # Start Vite dev server with HMR
npm run build          # Build for production
npm run preview        # Preview production build
```

## 🔧 Troubleshooting

### Roles/Permissions Not Working
```bash
# Re-run RBAC seeder
php artisan db:seed --class=RBACSeeder

# Clear cache
php artisan cache:clear
php artisan config:clear
```

### User Can't Access Features
1. Check if user has the correct role assigned in database (`role_user` table)
2. Verify role has the required permissions (`permission_role` table)
3. Check browser console for permission errors

### Frontend Not Loading
```bash
# Clear Vite cache
rm -rf node_modules/.vite
npm run dev
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License.

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js
- Tailwind CSS
- Pinia
- Laravel Sanctum

## 📞 Support

For support, please create an issue in the repository or contact the development team.
