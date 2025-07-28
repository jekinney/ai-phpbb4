# Installation Guide

## Requirements

- PHP 8.1 or higher
- Composer
- Node.js and npm
- MySQL or SQLite database

## Installation Steps

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/ai-phpbb4.git
   cd ai-phpbb4
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node.js dependencies**
   ```bash
   npm install
   ```

4. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Database Setup**
   - Configure your database settings in `.env`
   - Run migrations:
   ```bash
   php artisan migrate
   ```

6. **Storage Setup**
   ```bash
   php artisan storage:link
   ```

7. **Build Assets**
   ```bash
   npm run build
   ```

8. **Start the Application**
   ```bash
   php artisan serve
   ```

## Post-Installation

- Visit the admin dashboard to configure your forum
- Create your first forum categories
- Set up user permissions
- Configure file upload settings

## Troubleshooting

### Common Issues

- **Permission errors**: Make sure storage and bootstrap/cache directories are writable
- **Asset issues**: Run `npm run build` to compile assets
- **Database connection**: Check your database credentials in `.env`
