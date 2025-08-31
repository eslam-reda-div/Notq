# 🎓 LectureMind Laravel Backend

**Admin Panel & API Gateway for LectureMind AI Processing System**

LectureMind Laravel is a modern web application built with Laravel 12 and Filament 4 that serves as the administrative backend and API gateway for the LectureMind AI-powered lecture processing system. It provides a beautiful admin interface for managing customers, generation requests, and seamless integration with the AI processing service.

## 🚀 Features

### 🔐 Authentication & Authorization

-   **Multi-Guard Authentication**: Separate authentication for Admins and Customers
-   **Laravel Sanctum**: API authentication with token-based access
-   **Two-Factor Authentication**: Enhanced security with 2FA support
-   **Role-Based Access**: Admin and Customer specific interfaces

### 🎨 Admin Panel (Filament 4)

-   **Modern Dashboard**: Beautiful, responsive admin interface
-   **Customer Management**: Complete CRUD operations for customers
-   **Generation Tracking**: Monitor all AI processing requests
-   **File Management**: Audio and PDF file handling
-   **Custom Widgets**: Make Generate widget for easy AI request creation
-   **Real-time Notifications**: Status updates and processing alerts

### 📊 Generate Management System

-   **Status Tracking**: Pending, Processing, Completed, Failed states
-   **File Handling**: Audio upload with validation (mp3, wav, m4a, flac, ogg)
-   **AI Integration**: Seamless communication with Python AI service
-   **PDF Downloads**: Secure file serving and download links
-   **Progress Monitoring**: Real-time status updates

### 🔌 RESTful API

-   **Customer Authentication**: Secure API access with Sanctum tokens
-   **File Upload Endpoints**: Audio file processing requests
-   **Status Webhooks**: AI service result callbacks
-   **Swagger Documentation**: Complete API documentation with L5-Swagger

### 🎯 Key Models

-   **Customer**: User management with avatar support
-   **Generate**: AI processing request tracking
-   **Admin**: Administrative user management

## 🏗️ Architecture

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│  Frontend UI    │    │   Laravel API    │    │  Python AI      │
│  (Customer)     │◄──►│   Gateway        │◄──►│  Service        │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                              │
                              ▼
                       ┌──────────────────┐
                       │   Filament       │
                       │   Admin Panel    │
                       └──────────────────┘
```

## 📋 Prerequisites

-   PHP 8.2+
-   Composer
-   Node.js & NPM
-   MySQL/PostgreSQL
-   Docker (recommended)

## ⚡ Quick Start with Docker

1. **Clone and navigate:**

    ```bash
    git clone https://github.com/eslam-reda-div/lecture-mind.git
    cd lecture-mind/lecture-mind-laravel
    ```

2. **Environment setup:**

    ```bash
    cp .env.example .env
    # Edit .env with your database and AI service configuration
    ```

3. **Build and run:**
    ```bash
    docker build -t lecture-mind-laravel .
    docker run -p 8000:8000 lecture-mind-laravel
    ```

## 🛠️ Local Development Setup

### 1. Install Dependencies

```bash
# PHP dependencies
composer install

# Node.js dependencies
npm install
```

### 2. Environment Configuration

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Configure your .env file
```

**Essential Environment Variables:**

```env
APP_NAME="LectureMind"
APP_URL=http://localhost:8000

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lecture_mind
DB_USERNAME=your_username
DB_PASSWORD=your_password

# AI Service Configuration
AI_SERVICE_URL=http://localhost:5050

# File Storage
FILESYSTEM_DISK=public
```

### 3. Database Setup

```bash
# Run migrations
php artisan migrate

# Seed database (optional)
php artisan db:seed
```

### 4. Storage Setup

```bash
# Create storage link
php artisan storage:link

# Create required directories
mkdir -p storage/app/public/audio_files
mkdir -p storage/app/public/pdf_files
mkdir -p storage/app/public/temp_audio_files
```

### 5. Build Assets

```bash
# Development
npm run dev

# Production
npm run build
```

### 6. Start the Application

```bash
# Development server
php artisan serve

# Or with specific host/port
php artisan serve --host=0.0.0.0 --port=8000
```

## 📚 API Documentation

### 🔐 Authentication

All customer API endpoints require authentication using Laravel Sanctum tokens.

**Login Endpoint:**

```http
POST /api/v1/customer/auth/login
Content-Type: application/json

{
  "email": "customer@example.com",
  "password": "password"
}
```

### 🎵 Generate Endpoints

#### Create Generation Request

```http
POST /api/v1/customer/generate/make
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "audio_file": [audio file]
}
```

**Response:**

```json
{
    "success": true,
    "message": "Generate request created successfully",
    "data": {
        "generate": {
            "id": 1,
            "uuid": "123e4567-e89b-12d3-a456-426614174000",
            "status": "processing",
            "customer_id": 1,
            "audio_file": "audio_files/example.mp3",
            "file": "LectureMind-123456789-abc12.pdf"
        }
    }
}
```

#### AI Service Callback

```http
POST /api/v1/customer/generate/save_update
Content-Type: application/json

{
  "success": true,
  "pdf_filename": "LectureMind-123456789-abc12.pdf",
  "pdf_download_url": "/pdfs/LectureMind-123456789-abc12.pdf",
  "uuid": "123e4567-e89b-12d3-a456-426614174000",
  "transcription": "Lecture content...",
  "summarization": "Summary content...",
  "questions": "Generated questions..."
}
```

#### Get Customer Generations

```http
GET /api/v1/customer/generate/get
Authorization: Bearer {token}
```

### 📖 Complete API Documentation

Access the full Swagger documentation at: `http://localhost:8000/api/documentation`

## 🎨 Admin Panel Features

### 📊 Dashboard

-   Access at: `http://localhost:8000/admin`
-   Overview of system metrics
-   Recent activity monitoring

### 👥 Customer Management

-   **List View**: Searchable, sortable customer table
-   **Create/Edit**: Customer profile management
-   **Avatar Support**: Profile picture handling

### 📝 Generate Management

-   **Comprehensive View**: UUID, status, customer info, files
-   **Status Badges**: Visual status indicators
-   **Download Links**: Direct PDF and audio file access
-   **Markdown Rendering**: Formatted transcription, summary, and questions

### 🛠️ Make Generate Widget

-   **Header Action**: Create new generation requests
-   **File Upload**: Audio file validation and processing
-   **Customer Selection**: Dropdown with search functionality
-   **Real-time Feedback**: Processing status and notifications

## 📁 Project Structure

```
lecture-mind-laravel/
├── app/
│   ├── Enums/
│   │   └── GenerateStatus.php          # Status enumeration
│   ├── Filament/
│   │   └── Admin/
│   │       ├── Resources/              # Admin panel resources
│   │       └── Widgets/                # Custom widgets
│   ├── Http/
│   │   └── Controllers/
│   │       └── V1/Customer/            # API controllers
│   ├── Jobs/                          # Background jobs
│   ├── Models/                        # Eloquent models
│   │   ├── Admin.php
│   │   ├── Customer.php
│   │   └── Generate.php
│   └── Traits/                        # Reusable traits
├── config/                            # Configuration files
├── database/
│   ├── migrations/                    # Database migrations
│   └── seeders/                       # Database seeders
├── resources/
│   ├── css/                          # Stylesheets
│   ├── js/                           # JavaScript assets
│   └── views/                        # Blade templates
├── routes/
│   ├── api/                          # API routes (versioned)
│   └── web.php                       # Web routes
├── storage/
│   └── app/public/                   # File storage
│       ├── audio_files/              # Uploaded audio files
│       ├── pdf_files/                # Generated PDFs
│       └── temp_audio_files/         # Temporary uploads
└── tests/                            # Test files
```

## 🔧 Configuration

### File Storage

The application uses Laravel's public disk for file storage:

```php
// config/filesystems.php
'public' => [
    'driver' => 'local',
    'root' => storage_path('app/public'),
    'url' => env('APP_URL').'/storage',
    'visibility' => 'public',
],
```

### AI Service Integration

Configure the AI service URL in your environment:

```env
AI_SERVICE_URL=http://localhost:5050
```

### Database

Supports MySQL, PostgreSQL, and SQLite. Configure in `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=lecture_mind
```

## 🎯 Key Features Implementation

### Generate Status Management

```php
enum GenerateStatus: string
{
    case PENDING = 'pending';
    case PROCESSING = 'processing';
    case COMPLETED = 'completed';
    case FAILED = 'failed';
}
```

### File Upload Validation

```php
'audio_file' => 'required|file|mimes:mp3,wav,m4a,flac,ogg'
```

### AI Service Communication

```php
$response = Http::attach(
    'audio_file',
    file_get_contents(storage_path('app/public/' . $audioPath)),
    $uniqueAudioName
)->post($aiServiceUrl . '/summarize_request', [
    'pdf_file_name' => $pdfFileName,
    'uuid' => $generate->uuid,
]);
```

## 🚀 Deployment

### Docker Deployment

```bash
# Build image
docker build -t lecture-mind-laravel .

# Run container
docker run -d \
  --name lecture-mind-laravel \
  -p 8000:8000 \
  -v $(pwd)/storage:/var/www/html/storage \
  lecture-mind-laravel
```

### Production Setup

1. **Environment Configuration:**

    ```bash
    APP_ENV=production
    APP_DEBUG=false
    ```

2. **Optimize Application:**

    ```bash
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    composer install --optimize-autoloader --no-dev
    ```

3. **Set Permissions:**
    ```bash
    chmod -R 755 storage bootstrap/cache
    ```

## 🧪 Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature

# Run with coverage
php artisan test --coverage
```

## 📊 Available Commands

```bash
# Generate IDE helpers
php artisan ide-helper:generate
php artisan ide-helper:models

# Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Queue processing
php artisan queue:work

# File permissions
php artisan storage:link
```

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch: `git checkout -b feature-name`
3. Follow PSR-12 coding standards
4. Write tests for new features
5. Run `php artisan pint` for code formatting
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License.

---

**Built with ❤️ using Laravel 12, Filament 4, and modern PHP**
