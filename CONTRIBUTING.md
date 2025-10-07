# Contributing to UpWatch

Thank you for considering contributing to UpWatch! This document outlines the development workflow and best practices.

## 🚀 Getting Started

### Prerequisites

- PHP 8.1 or higher
- MySQL 8.0 or higher
- Node.js 20 or higher
- Composer
- Git

### Development Setup

1. **Fork and clone the repository:**
   ```bash
   git clone https://github.com/YOUR_USERNAME/upwatch.git
   cd upwatch
   ```

2. **Install dependencies:**
   ```bash
   composer install
   npm install
   ```

3. **Configure environment:**
   ```bash
   cp env .env
   # Edit .env with your database credentials
   ```

4. **Run the installer:**
   ```bash
   php spark upwatch:install
   ```
   
   This sets up the database, creates admin user, and builds CSS.

5. **Start development server:**
   ```bash
   php spark serve
   ```

6. **Start CSS watch mode (in another terminal):**
   ```bash
   npm run dev
   ```
   
   This auto-rebuilds CSS when you edit `public/assets/css/input.css` or any view files.

## 📁 Project Structure

```
upwatch/
├── app/
│   ├── Commands/         # CLI commands (spark commands)
│   ├── Controllers/      # HTTP controllers
│   │   └── Admin/        # Admin panel controllers
│   ├── Models/           # Database models
│   ├── Libraries/        # Core libraries (MonitoringEngine, etc.)
│   ├── Validation/       # Custom validation rules
│   ├── Filters/          # HTTP filters (auth, security headers)
│   ├── Views/            # View templates
│   └── Database/
│       ├── Migrations/   # Database migrations
│       └── Seeds/        # Database seeders
├── public/
│   └── assets/
│       └── css/
│           ├── input.css   # Tailwind source (EDIT THIS)
│           └── output.css  # Compiled CSS (DO NOT EDIT - auto-generated)
├── system/               # CodeIgniter 4 framework
├── writable/             # Logs, cache, uploads
├── .github/
│   └── workflows/        # GitHub Actions CI/CD
├── tailwind.config.js    # Tailwind configuration
├── postcss.config.js     # PostCSS configuration
└── package.json          # Node.js dependencies and scripts
```

## 🎨 Frontend Development

### Working with Tailwind CSS

**DO:**
- ✅ Edit `public/assets/css/input.css` for custom styles
- ✅ Use Tailwind utility classes in view files
- ✅ Run `npm run dev` during development (watches for changes)
- ✅ Run `npm run build` before committing (for testing)
- ✅ Update `tailwind.config.js` for theme customization

**DON'T:**
- ❌ Edit `public/assets/css/output.css` directly (it's auto-generated)
- ❌ Commit `output.css` to git (it's in .gitignore)
- ❌ Use CDN in production (use compiled CSS)
- ❌ Add inline styles (use Tailwind utilities instead)

### CSS Build Process

```bash
# Development (auto-rebuild on changes)
npm run dev

# Production (minified)
npm run build
```

**What happens:**
1. Tailwind scans `app/Views/**/*.php` for class names
2. Generates CSS with only used classes
3. Runs through PostCSS (autoprefixer)
4. Outputs to `public/assets/css/output.css`

### Replacing CDN with Built CSS

The project currently uses CDN for quick prototyping. Before production:

**Find in view files:**
```html
<script src="https://cdn.tailwindcss.com"></script>
```

**Replace with:**
```html
<link rel="stylesheet" href="/assets/css/output.css">
```

**Files to update:**
- `app/Views/status/index.php`
- `app/Views/admin/partials/header.php`
- `app/Views/admin/auth/login.php`
- `app/Views/subscribe/unsubscribed.php`

## 🗄️ Database Changes

### Creating a Migration

```bash
php spark make:migration CreateTableName
```

Edit the generated file in `app/Database/Migrations/`:

```php
public function up()
{
    $this->forge->addField([
        'id' => [
            'type' => 'INT',
            'constraint' => 11,
            'unsigned' => true,
            'auto_increment' => true,
        ],
        'name' => [
            'type' => 'VARCHAR',
            'constraint' => '255',
        ],
    ]);
    $this->forge->addKey('id', true);
    $this->forge->createTable('table_name');
}

public function down()
{
    $this->forge->dropTable('table_name');
}
```

### Running Migrations

```bash
php spark migrate              # Run pending migrations
php spark migrate:rollback     # Rollback last batch
php spark migrate:status       # Check migration status
```

## 🧪 Testing

### Manual Testing Checklist

Before submitting a PR:

- [ ] Code follows PSR-12 coding standards
- [ ] No debug code or console.log() statements
- [ ] CSS builds without errors (`npm run build`)
- [ ] Database migrations run cleanly
- [ ] Changes work in both light/dark mode (if applicable)
- [ ] Tested on Chrome, Firefox, Safari (if UI changes)
- [ ] No JavaScript console errors
- [ ] Security considerations reviewed

### Running Monitors

```bash
# Test monitoring engine
php spark monitor:run

# Check monitor results
mysql -u upwatch_user -p upwatch -e "SELECT * FROM monitor_results ORDER BY id DESC LIMIT 10;"
```

## 📝 Code Style

### PHP (PSR-12)

```php
<?php

namespace App\Controllers;

class ExampleController extends BaseController
{
    public function index(): string
    {
        $data = [
            'title' => 'Example',
        ];
        
        return view('example/index', $data);
    }
}
```

### Tailwind CSS Utilities

**Prefer utility classes over custom CSS:**

✅ **Good:**
```html
<button class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
    Click Me
</button>
```

❌ **Avoid:**
```html
<button class="my-custom-button">Click Me</button>
<style>.my-custom-button { ... }</style>
```

**When to create custom components:**
Only when a pattern is used 3+ times across different files.

## 🔀 Git Workflow

### Branching Strategy

- `main` - Production-ready code
- `develop` - Integration branch for features
- `feature/feature-name` - New features
- `fix/bug-name` - Bug fixes
- `hotfix/critical-fix` - Critical production fixes

### Commit Messages

Follow conventional commits:

```
feat: add TCP monitor support
fix: resolve session timeout issue
docs: update installation instructions
style: format code according to PSR-12
refactor: improve MonitoringEngine performance
test: add unit tests for UserModel
chore: update dependencies
```

### Making a Pull Request

1. **Create a feature branch:**
   ```bash
   git checkout -b feature/my-awesome-feature
   ```

2. **Make your changes:**
   - Write clean, commented code
   - Follow existing patterns
   - Test thoroughly

3. **Build CSS before committing:**
   ```bash
   npm run build
   ```
   
   Don't worry - `output.css` won't be committed (it's in .gitignore).

4. **Commit your changes:**
   ```bash
   git add .
   git commit -m "feat: add awesome feature"
   ```

5. **Push to your fork:**
   ```bash
   git push origin feature/my-awesome-feature
   ```

6. **Open a Pull Request:**
   - Go to GitHub
   - Click "New Pull Request"
   - Select your branch
   - Fill out the PR template
   - Wait for CI/CD checks to pass

### Pull Request Checklist

- [ ] Code builds successfully (`npm run build`)
- [ ] No linting errors
- [ ] Migrations tested
- [ ] Documentation updated (if needed)
- [ ] CHANGELOG.md updated (if applicable)
- [ ] Screenshots included (for UI changes)

## 🔒 Security

### Reporting Security Issues

**DO NOT** open a public issue for security vulnerabilities.

Instead, email: security@upwatch.example (or create a private security advisory on GitHub)

### Security Guidelines

- Always use parameterized queries (never raw SQL)
- Escape all user input with `esc()` in views
- Use CSRF protection for all forms
- Validate and sanitize all input
- Never commit `.env` files
- Use strong password hashing (bcrypt)
- Follow OWASP best practices

## 📦 Dependencies

### Adding New Dependencies

**PHP (Composer):**
```bash
composer require vendor/package
git add composer.json composer.lock
```

**JavaScript (npm):**
```bash
npm install package-name
git add package.json package-lock.json
```

**Important:** 
- ✅ DO commit `composer.lock` and `package-lock.json`
- ❌ DON'T commit `vendor/` or `node_modules/`

These lock files ensure everyone uses the same dependency versions.

## 🚀 Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed deployment instructions.

**Key points:**
- Build CSS on server: `npm run build`
- Use `composer install --no-dev --optimize-autoloader` in production
- Set `CI_ENVIRONMENT = production` in `.env`
- Enable HTTPS
- Set up cron job for monitoring

## 📚 Resources

- [CodeIgniter 4 Documentation](https://codeigniter.com/user_guide/)
- [Tailwind CSS Documentation](https://tailwindcss.com/docs)
- [HTMX Documentation](https://htmx.org/docs/)
- [Alpine.js Documentation](https://alpinejs.dev/)
- [Chart.js Documentation](https://www.chartjs.org/docs/)

## 💬 Questions?

- Open a [GitHub Discussion](https://github.com/iamtherealtim/upwatch/discussions)
- Check existing [Issues](https://github.com/iamtherealtim/upwatch/issues)
- Read the [README](README.md) and [DEPLOYMENT](DEPLOYMENT.md) guides

## 📄 License

By contributing to UpWatch, you agree that your contributions will be licensed under the MIT License.

---

**Thank you for contributing to UpWatch! 🎉**
