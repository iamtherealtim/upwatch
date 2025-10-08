# UpWatch 🔍


<img width="962" height="429" alt="upwatch-logo" src="https://github.com/user-attachments/assets/af821cdc-1158-4de4-828d-077377a28566" />

**Open-source uptime monitoring & status pages made simple**

UpWatch is a self-hosted status page and uptime monitoring solution built with CodeIgniter 4, HTMX, Alpine.js, and Tailwind CSS. Perfect for teams who want full control over their status page infrastructure.

[![License: MIT](https://img.shields.io/badge/License-MIT-blue.svg)](LICENSE)
[![PHP Version](https://img.shields.io/badge/PHP-8.3%2B-777BB4?logo=php&logoColor=white)](https://php.net)
[![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.5-EF4223?logo=codeigniter&logoColor=white)](https://codeigniter.com)
[![Node.js](https://img.shields.io/badge/Node.js-22%2B-339933?logo=node.js&logoColor=white)](https://nodejs.org)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![GitHub Actions](https://img.shields.io/github/actions/workflow/status/iamtherealtim/upwatch/build.yml?branch=main&logo=github&label=build)](https://github.com/iamtherealtim/upwatch/actions)
[![GitHub Issues](https://img.shields.io/github/issues/iamtherealtim/upwatch?logo=github)](https://github.com/iamtherealtim/upwatch/issues)
[![GitHub Stars](https://img.shields.io/github/stars/iamtherealtim/upwatch?style=social)](https://github.com/iamtherealtim/upwatch/stargazers)

## 📋 Table of Contents

- [✨ Features](#-features)
- [🚀 Quick Start](#-quick-start)
- [🚀 Getting Started (Quick)](#-getting-started-quick)
- [📊 Project Status](#-project-status)
- [📖 Documentation](#-documentation)
- [🔧 Troubleshooting](#-troubleshooting)
- [🎨 Customization](#-customization)
- [🔐 Security](#-security)
- [🤝 Contributing](#-contributing)
- [🌟 Roadmap](#-roadmap)
- [🎯 Why UpWatch?](#-why-upwatch)
- [📄 License](#-license)

## ✨ Features

- 🎯 **Beautiful Public Status Pages** - Clean, fast-loading status pages for your users
- 📊 **Real-time Monitoring** - HTTP/HTTPS, TCP, SSL certificate, and ping checks
- 🚨 **Incident Management** - Create and manage incidents with timeline updates
- 📅 **Scheduled Maintenance** - Announce planned maintenance windows
- 📈 **Uptime Analytics** - Historical uptime data and response time graphs
- 📧 **Email Notifications** - Subscriber management with email alerts
- 🎨 **Custom Branding** - Logo, colors, and domain customization
- 🔄 **HTMX Powered** - Dynamic updates without full page reloads
- 🌐 **Multi-tenant Support** - Host multiple status pages
- 🔐 **Secure** - Built-in authentication and authorization

## 🚀 Quick Start

### Requirements

- **PHP 8.3+** (8.1+ supported, 8.3+ recommended)
- **MySQL 8.0+** or **MariaDB 10.6+**
- **Composer 2.6+**
- **Node.js 20+ & npm** (for asset building)
- **Git** (for version control)

### Installation

#### Quick Install (Recommended)

UpWatch includes an interactive installation wizard that handles database setup, migrations, and initial configuration:

1. **Clone the repository**
   ```bash
   git clone https://github.com/iamtherealtim/upwatch.git
   cd upwatch
   ```

2. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment**
   ```bash
   cp env .env
   ```
   
   Edit `.env` and configure your database:
   ```ini
   database.default.hostname = localhost
   database.default.database = upwatch
   database.default.username = upwatch_user
   database.default.password = your_password
   ```
   
   **Note:** The database must already exist. See [Database Setup](#database-setup) below for instructions.

4. **Run the installer**
   ```bash
   php spark upwatch:install
   ```
   
   The installer will:
   - ✓ Check database connection
   - ✓ Verify/generate encryption key
   - ✓ Run all database migrations
   - ✓ Create admin user with secure random password
   - ✓ Seed sample data (status page, components, monitors)
   - ✓ Offer to install npm dependencies and build CSS
   
   **🔑 IMPORTANT:** Save the admin password shown in the output!

5. **Start the server**
   ```bash
   php spark serve
   ```

6. **Access UpWatch**
   - Public Status Page: http://localhost:8080
   - Admin Dashboard: http://localhost:8080/admin
   - Login with credentials from installer output
   - **Change your password immediately** after first login

#### Manual Installation

If you prefer manual setup or need more control:

1. **Clone and install dependencies** (same as steps 1-3 above)

2. **Generate encryption key**
   ```bash
   php spark key:generate
   ```

3. **Run migrations**
   ```bash
   php spark migrate
   ```

4. **Seed database**
   ```bash
   php spark db:seed InitialDataSeeder
   ```
   
   **🔑 IMPORTANT:** The seeder will generate a secure random password for the admin account. You'll see output like:
   ```
   ==================================================
   🔑 ADMIN CREDENTIALS - SAVE THESE IMMEDIATELY!
   ==================================================
   Email:    admin@upwatch.local
   Password: X7k$9mP2#qR5nL@8
   ==================================================
   ⚠️  This password will NOT be shown again!
   ==================================================
   ```
   
   **⚠️ SAVE THIS PASSWORD!** It's randomly generated for security and won't be displayed again.

5. **Build assets (optional - for Tailwind CSS)**
   ```bash
   npm run build
   ```

6. **Start the development server**
   ```bash
   php spark serve
   ```

7. **Access UpWatch**
   - Public Status Page: http://localhost:8080
   - Admin Dashboard: http://localhost:8080/admin
   - Login with the credentials shown during seeding
   - **Change your password immediately** after first login

### Set up Cron Job

For automated monitoring, add this to your crontab:

```bash
* * * * * cd /path/to/upwatch && php spark monitor:run >> /dev/null 2>&1
```

### Frontend Assets & Tailwind CSS

UpWatch uses Tailwind CSS for styling with a **build process** (not CDN in production).

#### Development Setup

1. **Install Node.js dependencies:**
   ```bash
   npm install
   ```
   
   This installs Tailwind CSS and other frontend dependencies.

2. **Build CSS for development:**
   ```bash
   npm run dev
   ```
   
   This watches for changes and auto-rebuilds `public/assets/css/output.css`.

3. **Build CSS for production:**
   ```bash
   npm run build
   ```
   
   Creates an optimized, minified CSS file.

#### How It Works

- **Source:** `public/assets/css/input.css` (committed to git)
- **Config:** `tailwind.config.js` + `postcss.config.js` (committed to git)
- **Output:** `public/assets/css/output.css` (NOT committed - built during deployment)

**Why output.css is not in git:**
- ✅ Build artifacts shouldn't be version controlled
- ✅ Prevents merge conflicts on every CSS change
- ✅ Keeps repository clean and history readable
- ✅ Forces proper build process in deployment
- ✅ Standard practice for modern web development (like `node_modules/` or `vendor/`)

#### Current State: Views Use CDN (Temporary)

The view files currently use Tailwind via CDN for quick development:

```html
<script src="https://cdn.tailwindcss.com"></script>
```

**⚠️ This is temporary and should be replaced before production!**

**Files that need updating:**
- `app/Views/status/index.php`
- `app/Views/admin/partials/header.php`
- `app/Views/admin/auth/login.php`
- `app/Views/subscribe/unsubscribed.php`

**Replace CDN with built CSS:**

Change:
```html
<script src="https://cdn.tailwindcss.com"></script>
```

To:
```html
<link rel="stylesheet" href="/assets/css/output.css">
```

#### Deployment Workflow

**For production deployment:**

1. **On your server or in CI/CD:**
   ```bash
   npm ci                # Install exact versions from package-lock.json
   npm run build         # Build optimized CSS
   ```

> **Note**: The `package-lock.json` file is now included in the repository to ensure consistent builds across environments and fix GitHub Actions deployment.

2. **Update view files** to use compiled CSS (as shown above)

3. **Deploy** the built `output.css` along with your code

**Automated with GitHub Actions:**
The project includes `.github/workflows/build.yml` which automatically builds CSS on every push and provides build artifacts.

#### Tailwind Configuration

**Custom theme colors:**
- Primary: `upwatch-500` (#3b82f6)
- Status colors: operational (green), degraded (yellow), major (red), maintenance (blue)

**Custom CSS components:**
```css
.btn, .btn-primary, .btn-secondary, .btn-danger
.card
.status-badge
.status-operational, .status-degraded, .status-partial, .status-major, .status-maintenance
```

All defined in `public/assets/css/input.css`.

#### Package Scripts

```bash
npm run dev    # Watch mode - rebuilds on file changes
npm run build  # Production build - minified and optimized
```

**Dependencies:**
- `tailwindcss` ^3.4.0 - Core CSS framework
- `@tailwindcss/forms` ^0.5.7 - Form styling plugin
- `autoprefixer` ^10.4.16 - Browser compatibility
- `postcss` ^8.4.32 - CSS processing
- `alpinejs` ^3.13.3 - Lightweight JavaScript framework  
- `htmx.org` ^1.9.10 - Dynamic HTML interactions
- `chart.js` ^4.4.1 - Charts and graphs

#### Why Not CDN in Production?

Using the CDN `<script src="https://cdn.tailwindcss.com"></script>` is great for prototyping but problematic for production:

❌ **Performance issues:**
- 300KB+ download (vs ~10KB built and minified)
- No browser caching
- Blocks page rendering
- Network dependency

❌ **Functionality issues:**
- Custom config (`tailwind.config.js`) not applied
- Custom components not available
- Purging doesn't work (includes unused classes)

❌ **Production concerns:**
- External dependency (CDN could go down)
- Privacy concerns (third-party requests)
- Content Security Policy complications

✅ **Built CSS advantages:**
- 10-30x smaller file size (only used classes)
- Cached by browser
- Custom configuration applied
- No external dependencies
- Much faster page loads

For detailed deployment instructions, see [DEPLOYMENT.md](DEPLOYMENT.md).

## 📖 Documentation

### Project Structure

```
upwatch/
├── app/
│   ├── Controllers/
│   │   ├── Admin/          # Admin panel controllers
│   │   ├── StatusController.php
│   │   └── AuthController.php
│   ├── Models/             # Database models
│   ├── Libraries/          # Core libraries (MonitoringEngine)
│   ├── Commands/           # CLI commands
│   ├── Database/
│   │   ├── Migrations/     # Database migrations
│   │   └── Seeds/          # Database seeders
│   └── Views/              # View templates
├── public/                 # Web root
└── writable/              # Logs & cache
```

### Core Components

#### MonitoringEngine
Handles all monitoring checks (HTTP, TCP, SSL, Ping). Runs via cron job.

#### Models
- `StatusPageModel` - Multi-tenant status pages
- `ComponentModel` - Services/components to monitor
- `IncidentModel` - Incidents and outages
- `MonitorModel` - Monitor configurations
- `MonitorResultModel` - Historical monitoring data
- `SubscriberModel` - Email subscribers
- `UserModel` - Admin users

### Database Setup

#### Database Requirements
- **MySQL 8.0+** or **MariaDB 10.3+**
- InnoDB storage engine (default)
- UTF8MB4 character set for emoji support

#### Creating the Database

1. **Log into MySQL/MariaDB:**
   ```bash
   mysql -u root -p
   ```

2. **Create the database:**
   ```sql
   CREATE DATABASE upwatch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
   ```

3. **Create a dedicated database user (recommended):**
   ```sql
   CREATE USER 'upwatch_user'@'localhost' IDENTIFIED BY 'your_secure_password';
   GRANT ALL PRIVILEGES ON upwatch.* TO 'upwatch_user'@'localhost';
   FLUSH PRIVILEGES;
   EXIT;
   ```

4. **Configure `.env` with your database credentials:**
   ```ini
   database.default.hostname = localhost
   database.default.database = upwatch
   database.default.username = upwatch_user
   database.default.password = your_secure_password
   database.default.DBDriver = MySQLi
   database.default.DBPrefix = 
   database.default.port = 3306
   ```

#### Database Schema

UpWatch uses migrations to create the following tables:

| Table | Purpose |
|-------|---------|
| `status_pages` | Multi-tenant status page configurations |
| `components` | Services/components being monitored |
| `component_groups` | Grouping for components on status pages |
| `incidents` | Incident records and outages |
| `incident_updates` | Timeline updates for incidents |
| `monitors` | Monitor configurations (HTTP, TCP, SSL, Ping) |
| `monitor_results` | Historical monitoring check results |
| `subscribers` | Email subscribers for notifications |
| `users` | Admin user accounts |
| `migrations` | Migration version tracking |

#### Running Migrations

Migrations automatically create all required tables:

```bash
# Run all pending migrations
php spark migrate

# Check migration status
php spark migrate:status

# Rollback last migration batch
php spark migrate:rollback

# Rollback all migrations (WARNING: destroys data)
php spark migrate:rollback --all
```

#### Seeding Initial Data

The `InitialDataSeeder` creates:
- Default admin user with secure random password
- Example status page
- Sample components (Website, API, Database)
- Demo monitoring checks

```bash
# Seed database with initial data
php spark db:seed InitialDataSeeder

# Seed specific seeder
php spark db:seed YourSeederName
```

#### Database Backup

**Recommended backup strategy:**

```bash
# Full database backup
mysqldump -u upwatch_user -p upwatch > upwatch_backup_$(date +%Y%m%d).sql

# Backup with compression
mysqldump -u upwatch_user -p upwatch | gzip > upwatch_backup_$(date +%Y%m%d).sql.gz

# Restore from backup
mysql -u upwatch_user -p upwatch < upwatch_backup_20250103.sql
```

**Automated daily backups (cron):**
```bash
0 2 * * * mysqldump -u upwatch_user -p'your_password' upwatch | gzip > /backups/upwatch_$(date +\%Y\%m\%d).sql.gz
```

#### Data Retention

Monitor results are cleaned up automatically:

```bash
# Cleanup results older than 90 days (default)
php spark monitor:cleanup

# Custom retention period (30 days)
php spark monitor:cleanup 30
```

Add to cron for automatic cleanup:
```bash
0 3 * * 0 cd /path/to/upwatch && php spark monitor:cleanup >> /dev/null 2>&1
```

#### Database Performance Tips

1. **Add indexes for frequently queried columns:**
   ```sql
   -- Already included in migrations
   CREATE INDEX idx_monitor_results_monitor_id ON monitor_results(monitor_id);
   CREATE INDEX idx_monitor_results_created_at ON monitor_results(created_at);
   ```

2. **Monitor table sizes:**
   ```sql
   SELECT 
       table_name AS 'Table',
       ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
   FROM information_schema.TABLES
   WHERE table_schema = 'upwatch'
   ORDER BY (data_length + index_length) DESC;
   ```

3. **Optimize tables periodically:**
   ```sql
   OPTIMIZE TABLE monitor_results;
   ```

### Commands

#### Installation
```bash
# Interactive installation wizard (recommended for first-time setup)
php spark upwatch:install

# Generate encryption key
php spark key:generate

# Database migrations
php spark migrate
php spark migrate:status
php spark migrate:rollback

# Seed database with initial data
php spark db:seed InitialDataSeeder
```

#### Monitoring
```bash
# Run monitors manually
php spark monitor:run

# Cleanup old data (keeps 90 days by default)
php spark monitor:cleanup

# Custom retention period
php spark monitor:cleanup 30
```

#### Utilities
```bash
# Clear rate limiting cache
php spark ratelimit:clear

# List all available commands
php spark list
```

## 🔧 Troubleshooting

### Common Issues

**"Encryption key not set" error**
- Run `php spark key:generate` and ensure it's saved to `.env`
- Check that `.env` file exists and contains `encryption.key = ...`

**"Database connection failed" error during installation**
- Verify MySQL/MariaDB service is running: `systemctl status mysql` (Linux) or check Services (Windows)
- Check database credentials in `.env` file
- **Database must exist before running installer** - Create it first:
  ```sql
  CREATE DATABASE upwatch CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
  ```
- Verify user has proper permissions: `SHOW GRANTS FOR 'upwatch_user'@'localhost';`
- Check port configuration (default: 3306)
- Run `php spark upwatch:install` - it will guide you through any issues

**"Database connection failed" error**
- Verify MySQL/MariaDB service is running: `systemctl status mysql` (Linux) or check Services (Windows)
- Check database credentials in `.env` file
- Ensure database exists: `SHOW DATABASES;` in MySQL
- Verify user has proper permissions: `SHOW GRANTS FOR 'upwatch_user'@'localhost';`
- Check port configuration (default: 3306)

**"Can't create monitor - validation error"**
- Check that the URL format is correct (must start with http:// or https://)
- Ensure the URL is properly formatted
- Check the monitor type matches the target (HTTP/HTTPS for web endpoints)

**"Too many subscription attempts"**
- Rate limiting is active (5 subscriptions per hour per IP)
- This is a security feature to prevent spam
- Wait 1 hour or clear cache: `writable/cache/*`

**"Invalid password" after seeding**
- Admin password is randomly generated during seeding
- Check the seeder output for the actual password
- If lost, re-run: `php spark db:seed InitialDataSeeder` (creates new user)

**CSP (Content Security Policy) errors in console**
- Browser blocking external resources
- Check `app/Filters/SecurityHeadersFilter.php` CSP configuration
- Source map warnings are cosmetic and don't affect functionality

**Login fails repeatedly**
- Rate limiting active after 5 failed attempts
- 30-minute lockout period
- Check `writable/logs/` for failed login IP addresses

**Styles not loading / Page looks unstyled**
- If using built CSS: Check that `public/assets/css/output.css` exists
- Run: `npm install && npm run build`
- If using CDN: Check internet connection and CSP settings
- Verify the correct CSS is linked in view files
- Clear browser cache: Ctrl+Shift+R (or Cmd+Shift+R on Mac)

**"npm: command not found"**
- Node.js not installed
- Download from: https://nodejs.org/
- Verify installation: `node --version` and `npm --version`
- After installing Node.js, run: `npm install && npm run build`

**CSS build fails / Tailwind errors**
- Delete `node_modules` and reinstall: `rm -rf node_modules && npm install`
- Check that `tailwind.config.js` and `postcss.config.js` exist
- Verify `public/assets/css/input.css` exists
- Try running with verbose output: `npx tailwindcss -i ./public/assets/css/input.css -o ./public/assets/css/output.css --minify --verbose`

## 🎨 Customization

### Branding
Customize your status page from the admin panel:
- Set primary brand color
- Configure support email
- Set custom domain

## 🔐 Security

UpWatch implements industry-standard security practices:

### Authentication & Passwords
- **Strong password requirements:** Minimum 12 characters with uppercase, lowercase, numbers, and special characters
- **Secure password generation:** Admin password randomly generated during initial setup (16 characters)
- **Password hashing:** bcrypt with secure salts
- **Session security:** Double regeneration on login, IP validation, CSRF protection

### Input Validation
- **SQL injection prevention:** Parameterized queries and query builder
- **XSS protection:** All output escaped with `esc()` function
- **Mass assignment protection:** `$protectFields` enabled on all models
- **URL validation:** Monitors validate URL format and schemes (HTTP/HTTPS)

### Network Security
- **SSL/TLS verification:** Monitor checks validate SSL certificates by default
- **Secure cookies:** HTTPS-only in production environments
- **Content Security Policy:** Restricts resource loading to trusted sources
- **Security headers:** X-Frame-Options, X-Content-Type-Options, etc.

### Rate Limiting
- **Login attempts:** 5 attempts per 15 minutes, 30-minute lockout
- **Subscriptions:** 5 subscriptions per hour per IP address
- **API endpoints:** Rate limiting on all public endpoints

### Production Deployment
Before deploying to production:
1. Set `CI_ENVIRONMENT = production` in `.env`
2. Enable HTTPS (required for secure cookies)
3. Generate encryption key: `php spark key:generate`
4. Change default admin password immediately
5. Review `SECURITY_AUDIT_REPORT.md` and `SECURITY_FIXES_COMPLETE.md`
6. Never commit `.env` file to version control

## 🚀 Getting Started (Quick)

### One-Command Installation

```bash
# Clone, install, and setup in one go
git clone https://github.com/iamtherealtim/upwatch.git && cd upwatch && composer install && npm install && cp env .env && php spark upwatch:install
```

Then edit your `.env` file with database credentials and visit `http://localhost:8080`!

## 📊 Project Status

- ✅ **Production Ready** - Fully functional status page and monitoring
- 🔒 **Security Audited** - See `SECURITY_AUDIT_REPORT.md`
- 🚀 **CI/CD Pipeline** - Automated builds and testing
- 📚 **Well Documented** - Comprehensive documentation
- 🐳 **Docker Ready** - Docker support coming soon

## 🤝 Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

### Quick Contribution Setup

1. **Fork the repository** on GitHub
2. **Clone your fork:**
   ```bash
   git clone https://github.com/YOUR_USERNAME/upwatch.git
   ```
3. **Create a feature branch:**
   ```bash
   git checkout -b feature/amazing-feature
   ```
4. **Make your changes** and test thoroughly
5. **Commit with conventional commits:**
   ```bash
   git commit -m "feat: add amazing feature"
   ```
6. **Push and create a Pull Request**

### Development Guidelines

- Follow PSR-12 coding standards
- Write tests for new features
- Update documentation for changes
- Use conventional commit messages
- Ensure CI/CD pipeline passes

### Reporting Issues

Found a bug? Have a feature request?

1. Check [existing issues](https://github.com/iamtherealtim/upwatch/issues)
2. Create a [new issue](https://github.com/iamtherealtim/upwatch/issues/new) with:
   - Clear description
   - Steps to reproduce
   - Expected vs actual behavior
   - Environment details (PHP/MySQL versions, OS)

## 🌟 Roadmap

### Upcoming Features
- 🐳 **Docker Support** - One-click deployment
- 📱 **Mobile App** - Native iOS/Android apps  
- 🔔 **Slack/Discord Integration** - Real-time notifications
- 📊 **Advanced Analytics** - Historical trends and insights
- 🌐 **Multi-language Support** - i18n implementation
- 🔌 **Webhook Support** - Custom integrations
- ☁️ **Cloud Hosting Option** - Managed SaaS version

See our [Project Board](https://github.com/iamtherealtim/upwatch/projects) for detailed progress.

## 🏆 Support & Community

- 💬 **Discussions**: [GitHub Discussions](https://github.com/iamtherealtim/upwatch/discussions)
- � **Issues**: [Bug Reports & Feature Requests](https://github.com/iamtherealtim/upwatch/issues)
- 📖 **Wiki**: [Documentation Wiki](https://github.com/iamtherealtim/upwatch/wiki)
- ⭐ **Star us** on GitHub if you find UpWatch useful!

## �📄 License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.

### License Notice
```
Copyright (c) 2025 iamtherealtim
Copyright (c) 2019-present CodeIgniter Foundation
Copyright (c) 2014-2019 British Columbia Institute of Technology

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software...
```

## 🙏 Acknowledgments

- Built with [CodeIgniter 4](https://codeigniter.com/)
- Powered by [HTMX](https://htmx.org/)
- Styled with [Tailwind CSS](https://tailwindcss.com/)
- Enhanced with [Alpine.js](https://alpinejs.dev/)

## 🎯 Why UpWatch?

### Perfect For

- 🏢 **Enterprises** - Full control and customization
- 🚀 **Startups** - Cost-effective monitoring solution  
- 👩‍💻 **Developers** - Open source, hackable, extensible
- 🏛️ **Government** - Data sovereignty requirements
- 🔒 **Privacy-conscious** - No third-party data sharing

---

## ⭐ Star History

[![Star History Chart](https://api.star-history.com/svg?repos=iamtherealtim/upwatch&type=Date)](https://star-history.com/#iamtherealtim/upwatch&Date)

---

**Made with ❤️ for the open-source community**

*If UpWatch helps your business, consider [sponsoring](https://github.com/sponsors/iamtherealtim) the project!*
