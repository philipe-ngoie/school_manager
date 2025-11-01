# School Manager

A comprehensive school management system built with Laravel 10, featuring payment processing, multi-currency support, and Flutter mobile applications.

[![Backend CI](https://github.com/philipe-ngoie/school_manager/actions/workflows/backend.yml/badge.svg)](https://github.com/philipe-ngoie/school_manager/actions/workflows/backend.yml)
[![Flutter CI](https://github.com/philipe-ngoie/school_manager/actions/workflows/flutter.yml/badge.svg)](https://github.com/philipe-ngoie/school_manager/actions/workflows/flutter.yml)

## Features

- **Student Management**: Complete student information management with enrollment tracking
- **Teacher & Class Management**: Manage teachers, classes, and subject assignments
- **Financial Management**: Invoice creation, payment processing, expense tracking
- **Payment Integration**: CinetPay integration with support for multiple payment providers
- **Multi-Currency**: Support for USD, XOF, EUR with currency conversion
- **PDF Invoices**: Generate and download professional invoice PDFs
- **Mobile Apps**: Flutter apps for administrators and parents
- **API-First**: RESTful API with Laravel Sanctum authentication
- **Multi-Platform**: Web admin panel + Android/iOS mobile apps

## Tech Stack

### Backend
- **Framework**: Laravel 10.49.1
- **PHP**: 8.1+
- **Database**: MySQL 8.0
- **Cache**: Redis
- **Authentication**: Laravel Sanctum + Fortify
- **UI**: Jetstream with Inertia.js + Vue.js
- **PDF**: barryvdh/laravel-dompdf

### Frontend
- **Admin Panel**: Inertia.js + Vue.js 3
- **CSS Framework**: Tailwind CSS
- **Build Tool**: Vite

### Mobile
- **Framework**: Flutter 3.x
- **Push Notifications**: Firebase Cloud Messaging (FCM)
- **Payment WebView**: CinetPay integration

### Infrastructure
- **Containerization**: Docker & Docker Compose
- **Web Server**: Nginx
- **CI/CD**: GitHub Actions

## Quick Start

### Prerequisites

- Docker & Docker Compose
- Git
- (Optional) PHP 8.1+, Composer, Node.js 18+ for local development

### Installation with Docker

1. **Clone the repository**
```bash
git clone https://github.com/philipe-ngoie/school_manager.git
cd school_manager
```

2. **Copy environment file**
```bash
cp .env.example .env
```

3. **Update environment variables**
Edit `.env` and configure:
- Database credentials
- CinetPay credentials (for payment processing)
- Mail settings
- FCM credentials (for push notifications)

4. **Start Docker containers**
```bash
make up
# or
docker-compose up -d
```

5. **Install dependencies and setup**
```bash
make composer-install
make npm-install
make migrate-fresh
```

6. **Access the application**
- **Web**: http://localhost
- **Admin Login**: admin@schoolmanager.com / password

### Manual Installation (Without Docker)

1. **Install PHP dependencies**
```bash
composer install
```

2. **Install Node dependencies**
```bash
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Configure database** in `.env`
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. **Run migrations and seeders**
```bash
php artisan migrate --seed
```

6. **Build frontend assets**
```bash
npm run build
```

7. **Start development server**
```bash
php artisan serve
npm run dev  # In another terminal
```

## Configuration

### Payment Gateway Setup

#### CinetPay Configuration

1. Sign up at [CinetPay](https://cinetpay.com)
2. Get your credentials from the dashboard
3. Update `.env`:

```env
CINETPAY_SITE_ID=your_site_id
CINETPAY_API_KEY=your_api_key
CINETPAY_SECRET_KEY=your_secret_key
CINETPAY_MODE=sandbox
CINETPAY_NOTIFY_URL=https://your-domain.com/api/payments/webhook/cinetpay
CINETPAY_RETURN_URL=https://your-domain.com/payments/callback
```

For detailed payment integration guide, see [docs/payments.md](docs/payments.md)

### Email Configuration

Update `.env` with your SMTP settings:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@schoolmanager.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Firebase Cloud Messaging (FCM)

For push notifications in mobile apps:

```env
FCM_SERVER_KEY=your_fcm_server_key
FCM_SENDER_ID=your_fcm_sender_id
```

## Usage

### Makefile Commands

The project includes a Makefile for common tasks:

```bash
make help              # Show available commands
make build             # Build Docker containers
make up                # Start all services
make down              # Stop all services
make restart           # Restart all services
make logs              # View logs
make shell             # Access app container shell
make migrate           # Run migrations
make migrate-fresh     # Fresh migration with seed
make seed              # Run seeders
make test              # Run tests
make npm-install       # Install npm dependencies
make npm-dev           # Run npm dev
make npm-build         # Build frontend assets
make fresh             # Complete fresh install
```

### API Endpoints

#### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Login and get API token

#### Students
- `GET /api/students` - List students
- `POST /api/students` - Create student
- `GET /api/students/{id}` - Get student details
- `PUT /api/students/{id}` - Update student
- `DELETE /api/students/{id}` - Delete student

#### Invoices
- `GET /api/invoices` - List invoices
- `POST /api/invoices` - Create invoice
- `GET /api/invoices/{id}` - Get invoice details
- `GET /api/invoices/{id}/pdf` - Download invoice PDF
- `POST /api/invoices/{id}/send` - Email invoice
- `POST /api/invoices/{id}/refund` - Request refund

#### Payments
- `POST /api/payments/initiate` - Initiate payment
- `POST /api/payments/verify` - Verify payment
- `POST /api/payments/webhook/cinetpay` - CinetPay webhook (public)

#### Reports
- `GET /api/reports/financials` - Financial reports
- `GET /api/reports/students` - Student reports
- `GET /api/reports/attendance` - Attendance reports

For complete API documentation, visit `/api/documentation` after starting the server.

## Testing

### Backend Tests

Run PHPUnit tests:
```bash
php artisan test
# or
make test
```

Run specific test:
```bash
php artisan test --filter=PaymentTest
```

### Frontend Tests

```bash
npm test
```

### Flutter Tests

```bash
cd apps/parent_app
flutter test
```

## Mobile Apps

### Parent App

Navigate to the parent app:
```bash
cd apps/parent_app
flutter pub get
flutter run
```

See [apps/parent_app/README.md](apps/parent_app/README.md) for detailed setup.

### Multiplatform App

Navigate to the multiplatform app:
```bash
cd apps/multiplatform_app
flutter pub get
flutter run
```

See [apps/multiplatform_app/README.md](apps/multiplatform_app/README.md) for detailed setup.

## Database Schema

### Core Entities
- **schools**: School information
- **teachers**: Teacher profiles
- **school_classes**: Class/grade information
- **students**: Student profiles
- **subjects**: Subject definitions
- **enrollments**: Student-subject enrollment
- **grades**: Academic grades
- **attendances**: Attendance records

### Financial Entities
- **fee_types**: Types of fees (tuition, lab, etc.)
- **invoices**: Student invoices
- **invoice_lines**: Invoice line items
- **payments**: Payment records
- **transactions**: Financial transactions
- **expenses**: School expenses
- **refunds**: Payment refunds
- **accounts**: Account balances
- **currency_rates**: Currency exchange rates

## Deployment

### Production Checklist

1. ✅ Set `APP_ENV=production` in `.env`
2. ✅ Set `APP_DEBUG=false`
3. ✅ Generate secure `APP_KEY`
4. ✅ Configure production database
5. ✅ Set up SSL/TLS certificates
6. ✅ Configure production payment credentials
7. ✅ Set up email service (SendGrid, Mailgun, etc.)
8. ✅ Configure proper backup strategy
9. ✅ Set up monitoring and logging
10. ✅ Run `php artisan optimize`

### Docker Production

Build production images:
```bash
docker-compose -f docker-compose.prod.yml build
docker-compose -f docker-compose.prod.yml up -d
```

## Documentation

- [Payment Integration Guide](docs/payments.md)
- [API Documentation](docs/api.md) (to be added)
- [Deployment Guide](docs/deployment.md) (to be added)

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## Security

If you discover any security vulnerabilities, please email security@schoolmanager.com instead of using the issue tracker.

## License

This project is licensed under the MIT License - see the LICENSE file for details.

## Support

- **Documentation**: Check the [docs](docs/) folder
- **Issues**: [GitHub Issues](https://github.com/philipe-ngoie/school_manager/issues)
- **Email**: support@schoolmanager.com

## Credits

Built with:
- [Laravel](https://laravel.com)
- [Flutter](https://flutter.dev)
- [Tailwind CSS](https://tailwindcss.com)
- [CinetPay](https://cinetpay.com)
