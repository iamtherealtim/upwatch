# Deployment Guide

This document explains how to deploy UpWatch to production.

## 📋 Pre-Deployment Checklist

- [ ] Code tested in development environment
- [ ] Database migrations tested
- [ ] `.env` file configured for production
- [ ] Encryption key generated
- [ ] HTTPS/SSL certificate configured
- [ ] Build assets locally or in CI/CD

## 🚀 Deployment Methods

### Method 1: Simple Server Deployment (Recommended for Small Teams)

**Requirements:**
- PHP 8.1+
- MySQL 8.0+
- Node.js 20+ (for building CSS)
- Composer
- Web server (Apache/Nginx)

**Steps:**

1. **Clone repository on server:**
   ```bash
   cd /var/www
   git clone https://github.com/iamtherealtim/upwatch.git
   cd upwatch
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install --no-dev --optimize-autoloader
   ```

3. **Install Node dependencies and build CSS:**
   ```bash
   npm ci
   npm run build
   ```
   
   **Important:** This creates `public/assets/css/output.css` which is needed for the site to display properly.

4. **Configure environment:**
   ```bash
   cp env .env
   nano .env  # Edit with production settings
   ```
   
   Set these values:
   ```ini
   CI_ENVIRONMENT = production
   
   app.baseURL = 'https://status.yourdomain.com/'
   
   database.default.hostname = localhost
   database.default.database = upwatch
   database.default.username = upwatch_user
   database.default.password = your_secure_password
   ```

5. **Generate encryption key:**
   ```bash
   php spark key:generate
   ```

6. **Run database setup:**
   ```bash
   php spark migrate
   php spark db:seed InitialDataSeeder
   ```
   
   **SAVE THE ADMIN PASSWORD!**

7. **Set file permissions:**
   ```bash
   chown -R www-data:www-data /var/www/upwatch
   chmod -R 755 /var/www/upwatch
   chmod -R 775 /var/www/upwatch/writable
   ```

8. **Configure web server** (see Web Server Configuration below)

9. **Set up cron job:**
   ```bash
   crontab -e
   ```
   
   Add:
   ```
   * * * * * cd /var/www/upwatch && php spark monitor:run >> /dev/null 2>&1
   ```

10. **Update views to use compiled CSS:**
    
    Replace in these files:
    - `app/Views/status/index.php`
    - `app/Views/admin/partials/header.php`
    - `app/Views/admin/auth/login.php`
    - `app/Views/subscribe/unsubscribed.php`
    
    Change:
    ```html
    <script src="https://cdn.tailwindcss.com"></script>
    ```
    
    To:
    ```html
    <link rel="stylesheet" href="/assets/css/output.css">
    ```

### Method 2: CI/CD Deployment (Recommended for Teams)

Use GitHub Actions or GitLab CI to automate builds:

**Example workflow (`.github/workflows/deploy.yml`):**

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    
    steps:
    - uses: actions/checkout@v4
    
    - name: Setup Node.js
      uses: actions/setup-node@v4
      with:
        node-version: '20'
        cache: 'npm'
    
    - name: Install and build
      run: |
        npm ci
        npm run build
    
    - name: Deploy to server
      uses: easingthemes/ssh-deploy@v4
      with:
        SSH_PRIVATE_KEY: ${{ secrets.SSH_PRIVATE_KEY }}
        REMOTE_HOST: ${{ secrets.REMOTE_HOST }}
        REMOTE_USER: ${{ secrets.REMOTE_USER }}
        TARGET: /var/www/upwatch
        EXCLUDE: "/node_modules/, /.git/, /.github/"
```

### Method 3: Docker Deployment

**Dockerfile:**

```dockerfile
FROM php:8.2-apache

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    nodejs \
    npm

# Install PHP extensions
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Install dependencies
RUN composer install --no-dev --optimize-autoloader
RUN npm ci && npm run build

# Set permissions
RUN chown -R www-data:www-data /var/www/html/writable
RUN chmod -R 775 /var/www/html/writable

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Copy virtual host configuration
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

EXPOSE 80

CMD ["apache2-foreground"]
```

## 🌐 Web Server Configuration

### Apache

Create `/etc/apache2/sites-available/upwatch.conf`:

```apache
<VirtualHost *:80>
    ServerName status.yourdomain.com
    DocumentRoot /var/www/upwatch/public
    
    <Directory /var/www/upwatch/public>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/upwatch_error.log
    CustomLog ${APACHE_LOG_DIR}/upwatch_access.log combined
</VirtualHost>
```

Enable site and SSL:
```bash
a2ensite upwatch
a2enmod rewrite
systemctl restart apache2

# For HTTPS (recommended)
certbot --apache -d status.yourdomain.com
```

### Nginx

Create `/etc/nginx/sites-available/upwatch`:

```nginx
server {
    listen 80;
    server_name status.yourdomain.com;
    root /var/www/upwatch/public;
    
    index index.php;
    
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
    
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }
    
    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Enable site and SSL:
```bash
ln -s /etc/nginx/sites-available/upwatch /etc/nginx/sites-enabled/
nginx -t
systemctl restart nginx

# For HTTPS (recommended)
certbot --nginx -d status.yourdomain.com
```

## 🔄 Updates & Maintenance

### Updating UpWatch

```bash
cd /var/www/upwatch

# Backup database first!
mysqldump -u upwatch_user -p upwatch > backup_$(date +%Y%m%d).sql

# Pull latest code
git pull origin main

# Update dependencies
composer install --no-dev --optimize-autoloader
npm ci
npm run build

# Run new migrations
php spark migrate

# Clear cache
rm -rf writable/cache/*
```

### Monitoring the Monitor

Set up basic health checks:

```bash
# Check if cron is running monitors
tail -f writable/logs/log-*.log | grep "monitor:run"

# Check database size
mysql -u upwatch_user -p -e "SELECT table_name, 
    ROUND(((data_length + index_length) / 1024 / 1024), 2) 'Size (MB)' 
    FROM information_schema.TABLES 
    WHERE table_schema = 'upwatch' 
    ORDER BY (data_length + index_length) DESC;"

# Monitor disk usage
df -h /var/www/upwatch
```

## 🔒 Security Hardening

1. **Disable directory listing:**
   - Already done in `.htaccess`

2. **Hide server information:**
   ```apache
   # In Apache config
   ServerTokens Prod
   ServerSignature Off
   ```

3. **Set secure permissions:**
   ```bash
   find /var/www/upwatch -type d -exec chmod 755 {} \;
   find /var/www/upwatch -type f -exec chmod 644 {} \;
   chmod -R 775 /var/www/upwatch/writable
   chmod 600 /var/www/upwatch/.env
   ```

4. **Configure firewall:**
   ```bash
   ufw allow 80/tcp
   ufw allow 443/tcp
   ufw allow 22/tcp
   ufw enable
   ```

5. **Regular backups:**
   ```bash
   # Add to crontab
   0 2 * * * mysqldump -u upwatch_user -p'password' upwatch | gzip > /backups/upwatch_$(date +\%Y\%m\%d).sql.gz
   ```

## 📊 Performance Optimization

1. **Enable OPcache:**
   ```ini
   # In php.ini
   opcache.enable=1
   opcache.memory_consumption=128
   opcache.max_accelerated_files=10000
   ```

2. **Configure caching:**
   ```bash
   # Use Redis for session/cache (optional)
   apt-get install redis-server php-redis
   ```

3. **Database optimization:**
   ```sql
   OPTIMIZE TABLE monitor_results;
   ```

4. **Set up CDN** (optional):
   - CloudFlare for static assets
   - Improves global performance

## 🆘 Troubleshooting

**CSS not loading:**
- Verify `public/assets/css/output.css` exists
- Check file permissions: `chmod 644 public/assets/css/output.css`
- Rebuild: `npm run build`

**500 Internal Server Error:**
- Check Apache/Nginx error logs
- Verify `.env` file exists and is readable
- Check writable directory permissions

**Monitors not running:**
- Verify cron job is configured: `crontab -l`
- Check cron logs: `grep CRON /var/log/syslog`
- Test manually: `php spark monitor:run`

**Database connection failed:**
- Verify MySQL is running: `systemctl status mysql`
- Test connection: `mysql -u upwatch_user -p`
- Check `.env` database credentials

## 📝 Checklist: Before Going Live

- [ ] `.env` set to production
- [ ] HTTPS enabled with valid SSL certificate
- [ ] Database migrations completed
- [ ] Admin password changed from default
- [ ] CSS built and views updated
- [ ] Cron job configured for monitoring
- [ ] File permissions set correctly
- [ ] Backups configured
- [ ] Error logging enabled
- [ ] Firewall configured
- [ ] Server monitoring set up
- [ ] `.env` file secured (chmod 600)
- [ ] Test all monitor types (HTTP, TCP, SSL, Ping)
- [ ] Test incident creation and email notifications
- [ ] Test subscription signup and notifications

## 🎯 Production Environment Variables

```ini
#--------------------------------------------------------------------
# ENVIRONMENT
#--------------------------------------------------------------------

CI_ENVIRONMENT = production

#--------------------------------------------------------------------
# APP
#--------------------------------------------------------------------

app.baseURL = 'https://status.yourdomain.com/'
app.forceGlobalSecureRequests = true

#--------------------------------------------------------------------
# DATABASE
#--------------------------------------------------------------------

database.default.hostname = localhost
database.default.database = upwatch
database.default.username = upwatch_user
database.default.password = STRONG_PASSWORD_HERE
database.default.DBDebug = false
database.default.DBPrefix = 

#--------------------------------------------------------------------
# ENCRYPTION
#--------------------------------------------------------------------

encryption.key = YOUR_GENERATED_KEY_HERE

#--------------------------------------------------------------------
# EMAIL (Configure for notifications)
#--------------------------------------------------------------------

email.fromEmail = 'noreply@yourdomain.com'
email.fromName = 'UpWatch Status'
email.SMTPHost = 'smtp.yourdomain.com'
email.SMTPUser = 'smtp_username'
email.SMTPPass = 'smtp_password'
email.SMTPPort = 587
email.SMTPCrypto = 'tls'

#--------------------------------------------------------------------
# SECURITY
#--------------------------------------------------------------------

cookie.secure = true
session.cookieSecure = true
```

---

**Need help?** Check the [Security Documentation](SECURITY_FIXES_COMPLETE.md) and [README](UPWATCH_README.md)
