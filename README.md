# LMS Platform - Learning Management System

A comprehensive Learning Management System built with Laravel 12, featuring advanced security measures, payment integration, and section-based course access.

## 🚀 Features

### Core LMS Features
- **User Management**: Student, Instructor, and Admin roles
- **Course Management**: Create, edit, and organize courses with sections and lessons
- **Content Delivery**: Video lessons with progress tracking
- **Enrollment System**: Course enrollment with payment processing
- **Progress Tracking**: Monitor student progress through lessons and courses
- **Reviews & Ratings**: Student feedback system for courses

### Advanced Security Features
- **Developer Tools Prevention**: Blocks F12, Ctrl+Shift+I, and other dev tools
- **Content Protection**: Prevents text selection, right-click, copy/paste
- **Video Security**: Enhanced video player with watermarking and tampering detection
- **Screen Recording Prevention**: Blocks screen sharing and recording attempts
- **Console Protection**: Monitors and blocks suspicious console usage
- **Iframe Protection**: Prevents page embedding in external frames

### Payment Integration
- **Multiple Gateways**: Stripe, PayPal, and PayMob support
- **Section-Based Purchases**: Buy individual course sections
- **Secure Processing**: Webhook handling and fraud detection
- **Multi-Currency**: Support for USD, EGP, and other currencies
- **Tax Calculation**: Automatic tax calculation and application

### Section-Based Access
- **Granular Monetization**: Purchase individual course sections
- **Flexible Pricing**: Set different prices for sections and courses
- **Access Control**: Middleware-based access enforcement
- **Progress Tracking**: Track progress within individual sections

### Error Handling
- **Comprehensive Error Management**: No error pages shown to users
- **User-Friendly Messages**: Arabic error messages with graceful fallbacks
- **Detailed Logging**: Complete error tracking for debugging
- **Frontend Error Handling**: JavaScript error interception and display

## 🛠️ Technology Stack

### Backend
- **Laravel 12.x** - PHP framework
- **PHP 8.2+** - Server-side language
- **SQLite** - Database (configurable for MySQL/PostgreSQL)
- **Laravel Cashier** - Payment processing
- **Stripe PHP SDK** - Payment gateway integration

### Frontend
- **Tailwind CSS** - Utility-first CSS framework
- **Alpine.js** - Lightweight JavaScript framework
- **Bootstrap 5** - UI components
- **Vite** - Build tool and development server

### Development Tools
- **Pest PHP** - Testing framework
- **Laravel Pint** - Code styling
- **Laravel Sail** - Docker development environment
- **Faker** - Data generation for testing

## 📋 Prerequisites

Before you begin, ensure you have the following installed:
- **PHP 8.2 or higher**
- **Composer** - PHP package manager
- **Node.js 18+** - JavaScript runtime
- **npm** - Node.js package manager
- **Git** - Version control

## 🚀 Installation

### 1. Clone the Repository
```bash
git clone <repository-url>
cd lms-platform-OpenAI
```

### 2. Install PHP Dependencies
```bash
composer install
```

### 3. Install Node.js Dependencies
```bash
npm install
```

### 4. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 5. Configure Database
Edit your `.env` file and set up your database configuration:
```env
DB_CONNECTION=sqlite
DB_DATABASE=/absolute/path/to/your/project/database/database.sqlite
```

### 6. Run Migrations and Seeders
```bash
php artisan migrate
php artisan db:seed
```

### 7. Build Frontend Assets
```bash
npm run build
```

### 8. Start the Development Server
```bash
php artisan serve
```

## 🔧 Configuration

### Payment Gateway Setup

1. **Stripe Configuration**
   ```env
   STRIPE_ENABLED=true
   STRIPE_PUBLIC_KEY=pk_test_your_stripe_public_key_here
   STRIPE_SECRET_KEY=sk_test_your_stripe_secret_key_here
   STRIPE_WEBHOOK_SECRET=whsec_your_stripe_webhook_secret_here
   ```

2. **PayPal Configuration**
   ```env
   PAYPAL_ENABLED=true
   PAYPAL_CLIENT_ID=your_paypal_client_id_here
   PAYPAL_CLIENT_SECRET=your_paypal_client_secret_here
   PAYPAL_MODE=sandbox
   ```

3. **PayMob Configuration**
   ```env
   PAYMOB_ENABLED=true
   PAYMOB_API_KEY=your_paymob_api_key_here
   PAYMOB_INTEGRATION_ID=your_paymob_integration_id_here
   ```

### Security Configuration
The security features are enabled by default. You can customize them in the respective view files:
- Global security: `resources/views/layouts/app.blade.php`
- Video security: `resources/views/student/courses/learn.blade.php`

## 🧪 Testing

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Authentication tests
php artisan test tests/Feature/Auth/

# Section access tests
php artisan test tests/Feature/SectionAccessTest.php

# Profile tests
php artisan test tests/Feature/ProfileTest.php
```

### Manual Security Testing
1. Try opening developer tools (F12, Ctrl+Shift+I)
2. Attempt to right-click on protected content
3. Try to copy/paste text from the application
4. Test video player security features

## 📁 Project Structure

```
lms-platform-OpenAI/
├── app/
│   ├── Events/           # Payment events
│   ├── Exceptions/       # Error handling
│   ├── Http/
│   │   ├── Controllers/  # Application controllers
│   │   ├── Middleware/   # Custom middleware
│   │   └── Requests/     # Form requests
│   ├── Models/           # Eloquent models
│   ├── Providers/        # Service providers
│   └── Services/         # Business logic services
├── config/               # Configuration files
├── database/
│   ├── factories/        # Model factories
│   ├── migrations/       # Database migrations
│   └── seeders/          # Database seeders
├── public/               # Public assets
├── resources/
│   ├── css/              # Stylesheets
│   ├── js/               # JavaScript files
│   └── views/            # Blade templates
├── routes/               # Application routes
├── storage/              # File storage
└── tests/                # Test files
```

## 🔐 Security Features

### Developer Tools Prevention
- Blocks F12, Ctrl+Shift+I, Ctrl+Shift+J, Ctrl+Shift+C
- Prevents view source (Ctrl+U)
- Blocks save page (Ctrl+S) and print (Ctrl+P)
- Real-time detection with immediate response

### Content Protection
- Disables text selection globally
- Blocks right-click context menu
- Prevents copy/paste operations
- Blocks drag and drop file uploads

### Video Security
- Dynamic watermarking with user identification
- Source integrity monitoring
- Screen recording prevention
- Console manipulation detection

## 💳 Payment Features

### Supported Gateways
- **Stripe**: Credit/debit card processing
- **PayPal**: PayPal account payments
- **PayMob**: Middle Eastern payment processing

### Features
- Section-based purchases
- Multi-currency support
- Tax calculation
- Webhook handling
- Fraud detection
- Rate limiting

## 🎯 Section-Based Access

### Features
- Purchase individual course sections
- Flexible pricing per section
- Access control middleware
- Progress tracking within sections
- Admin interface for section management

### Usage
1. Admin sets section pricing in course management
2. Students can purchase individual sections
3. Access is granted immediately after payment
4. Progress is tracked per section

## 🚨 Error Handling

### Features
- No error pages shown to users
- User-friendly Arabic error messages
- Comprehensive error logging
- Graceful fallbacks
- Frontend error interception

### Error Types Handled
- System errors
- Database errors
- Authentication errors
- Validation errors
- Network errors
- JavaScript errors

## 📊 Admin Features

### Course Management
- Create and edit courses
- Manage sections and lessons
- Set pricing for courses and sections
- Monitor enrollment statistics

### User Management
- Manage student accounts
- View user progress
- Handle payment issues
- Access control management

### Content Management
- Upload and manage video content
- Organize course structure
- Set access permissions
- Monitor content usage

## 🎨 User Interface

### Design Features
- Modern, responsive design
- Arabic language support
- Intuitive navigation
- Mobile-friendly interface
- Professional styling with Tailwind CSS

### User Experience
- Smooth learning interface
- Progress indicators
- Interactive elements
- Clear call-to-action buttons
- Consistent design language

## 🔧 Development

### Development Commands
```bash
# Start development server with all services
composer run dev

# Run tests
composer run test

# Code styling
./vendor/bin/pint

# Database operations
php artisan migrate:fresh --seed
```

### Adding New Features
1. Create migrations for database changes
2. Add models and relationships
3. Create controllers and services
4. Add routes and middleware
5. Create views and frontend components
6. Write tests for new functionality

## 📝 Documentation

Additional documentation is available in the project:
- `PAYMENT_SETUP.md` - Detailed payment gateway setup
- `SECURITY_FEATURES.md` - Security implementation details
- `SECTION_ACCESS_FEATURE.md` - Section-based access documentation
- `ERROR_HANDLING_IMPLEMENTATION.md` - Error handling system

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Add tests for new functionality
5. Ensure all tests pass
6. Submit a pull request

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🆘 Support

For support and questions:
- Check the documentation files in the project
- Review the error logs in `storage/logs/`
- Test the security features manually
- Verify payment gateway configurations

## 🔄 Updates

To update the project:
```bash
git pull origin main
composer install
npm install
php artisan migrate
npm run build
```

## 📈 Performance

### Optimization Tips
- Use Laravel's caching features
- Optimize database queries
- Minimize JavaScript bundle size
- Use CDN for static assets
- Enable compression

### Monitoring
- Check Laravel logs regularly
- Monitor payment gateway webhooks
- Track security violation patterns
- Monitor user engagement metrics

---

**Note**: This LMS platform is designed for educational institutions and content creators who need a secure, scalable solution for delivering online courses with advanced payment processing and content protection features.
