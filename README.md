# Project Management System

A comprehensive project management application built with Laravel 12 and Vue.js 3, featuring Kanban boards, task management, and team collaboration tools.

## 🚀 Features

### Core Functionality
- **User Authentication**: Secure registration, login, and profile management with Laravel Sanctum
- **Project Management**: Create, edit, and manage projects with detailed information
- **Task Management**: Comprehensive task tracking with priorities, statuses, assignments, and due dates
- **Kanban Boards**: Visual board management with drag-and-drop functionality and position tracking
- **Dashboard**: Overview with statistics, charts, and recent project activities

### Advanced Features
- **Role-Based Access Control (RBAC)**: Comprehensive permission system with companies, departments, teams, roles, and permissions
- **Charts & Analytics**: Visual representations of project status, task distribution, and progress tracking
- **Responsive Design**: Mobile-friendly interface built with Tailwind CSS
- **Real-time Notifications**: Toast notifications for all CRUD operations
- **Form Validation**: Client-side validation with error display
- **Breadcrumb Navigation**: Dynamic navigation breadcrumbs for better UX

### Technical Features
- **RESTful API**: Well-structured API endpoints for all resources
- **Vue Router**: Client-side routing with authentication guards
- **Component Architecture**: Modular Vue components for maintainability
- **Database Migrations**: Structured database schema with relationships
- **Seeders**: Pre-populated data for permissions and roles

## 🛠️ Technology Stack

### Backend
- **Laravel 12**: PHP web framework
- **PHP 8.2**: Server-side scripting
- **Laravel Sanctum**: API authentication
- **MySQL/SQLite**: Database (configurable)

### Frontend
- **Vue.js 3**: Progressive JavaScript framework
- **Vue Router 4**: Official router for Vue.js
- **Tailwind CSS 4**: Utility-first CSS framework
- **Chart.js**: Simple yet flexible JavaScript charting library
- **Axios**: HTTP client for API requests

### Development Tools
- **Vite**: Fast build tool and development server
- **Composer**: PHP dependency manager
- **NPM**: JavaScript package manager
- **Laravel Pint**: Code style fixer
- **PHPUnit**: Testing framework

## 📋 Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js 18+ and NPM
- MySQL or SQLite database

## 🚀 Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd management
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   ```bash
   # Configure your database in .env file
   php artisan migrate
   php artisan db:seed
   ```

6. **Build Assets**
   ```bash
   npm run build
   ```

7. **Start the Development Server**
   ```bash
   # Start Laravel server
   php artisan serve

   # In another terminal, start Vite dev server
   npm run dev
   ```

   Or use the convenient setup script:
   ```bash
   composer run setup
   ```

## 🏗️ Project Structure

```
├── app/
│   ├── Http/Controllers/     # API Controllers
│   ├── Models/              # Eloquent Models
│   └── Providers/           # Service Providers
├── database/
│   ├── migrations/          # Database migrations
│   └── seeders/            # Database seeders
├── public/                  # Public assets
├── resources/
│   ├── css/                # Stylesheets
│   ├── js/
│   │   ├── components/     # Vue components
│   │   ├── router/         # Vue Router configuration
│   │   └── views/          # Vue views
│   └── views/              # Blade templates
├── routes/
│   ├── api.php            # API routes
│   └── web.php            # Web routes
├── storage/                # File storage
├── tests/                  # Test files
└── vite.config.js         # Vite configuration
```

## 📊 Database Schema

### Core Tables
- **users**: User accounts and authentication
- **projects**: Project information and metadata
- **tasks**: Task details with assignments and status
- **boards**: Kanban boards with position tracking



## 🔗 API Endpoints

### Authentication
- `POST /api/register` - User registration
- `POST /api/login` - User login
- `POST /api/logout` - User logout
- `GET /api/user` - Get authenticated user
- `PUT /api/user/profile` - Update user profile
- `POST /api/user/change-password` - Change password

### Projects
- `GET /api/projects` - List all projects
- `POST /api/projects` - Create new project
- `GET /api/projects/{id}` - Get specific project
- `PUT /api/projects/{id}` - Update project
- `DELETE /api/projects/{id}` - Delete project

### Tasks
- `GET /api/tasks` - List all tasks
- `POST /api/tasks` - Create new task
- `GET /api/tasks/{id}` - Get specific task
- `PUT /api/tasks/{id}` - Update task
- `DELETE /api/tasks/{id}` - Delete task

### Boards
- `GET /api/boards` - List all boards
- `POST /api/boards` - Create new board
- `GET /api/boards/{id}` - Get specific board
- `PUT /api/boards/{id}` - Update board
- `DELETE /api/boards/{id}` - Delete board
- `PATCH /api/boards/{id}/position` - Update board position

## 🎨 Frontend Components

### Layout Components
- **App.vue**: Main application component
- **Sidebar.vue**: Navigation sidebar
- **Breadcrumbs.vue**: Dynamic breadcrumb navigation
- **Notification.vue**: Toast notifications

### Feature Components
- **Dashboard.vue**: Main dashboard with charts
- **Projects/**: Project CRUD components
- **Tasks/**: Task management components
- **Boards/**: Board management components
- **Auth/**: Authentication components

### Form Components
- **FormField.vue**: Reusable form field wrapper
- **InputText.vue**: Text input component
- **InputTextarea.vue**: Textarea input
- **InputSelect.vue**: Select dropdown
- **InputDate.vue**: Date picker
- **FormActions.vue**: Form action buttons

### Chart Components
- **ProjectStatusChart.vue**: Project status visualization
- **TaskStatusChart.vue**: Task status distribution
- **ProjectProgressChart.vue**: Project progress tracking

## 🧪 Testing

Run the test suite:
```bash
php artisan test
```

## 📜 Scripts

Available Composer scripts:
- `composer run setup` - Complete project setup
- `composer run dev` - Start development servers
- `composer run test` - Run test suite

Available NPM scripts:
- `npm run dev` - Start Vite development server
- `npm run build` - Build for production
- `npm run preview` - Preview production build

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📝 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- Laravel Framework
- Vue.js
- Tailwind CSS
- Chart.js
- Laravel Sanctum

## 📞 Support

For support, please contact the development team or create an issue in the repository.
