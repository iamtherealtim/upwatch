# Security Policy

## Supported Versions

We actively support the following versions of UpWatch with security updates:

| Version | Supported          |
| ------- | ------------------ |
| 1.0.x   | ✅ Yes             |
| < 1.0   | ❌ No              |

## Reporting a Vulnerability

We take security seriously. If you discover a security vulnerability, please follow these steps:

### 🔒 Private Disclosure (Preferred)

For security vulnerabilities, please **do not** create a public GitHub issue. Instead:

1. **Email us directly**: security@upwatch.local
2. **Include the following information**:
   - Description of the vulnerability
   - Steps to reproduce the issue
   - Potential impact assessment
   - Suggested fix (if you have one)
   - Your contact information

### 📋 What to Include

Please provide as much information as possible:

- **Type of vulnerability** (e.g., SQL injection, XSS, CSRF, etc.)
- **Location** of the vulnerable code (file path, line number)
- **Proof of concept** or exploit code (if safe to share)
- **Impact assessment** (what can an attacker do?)
- **Environment details** (PHP version, database, OS)

### ⏰ Response Timeline

- **Initial response**: Within 48 hours
- **Assessment**: Within 1 week
- **Fix development**: 1-4 weeks (depending on complexity)
- **Public disclosure**: After fix is released and users have time to update

### 🏆 Recognition

We believe in recognizing security researchers who help make UpWatch safer:

- **Security Hall of Fame**: Listed in our security acknowledgments
- **Public credit**: In release notes and security advisories (if desired)
- **Direct communication**: With our development team during the fix process

### 🚨 Scope

**In Scope:**
- Authentication and authorization flaws
- SQL injection vulnerabilities  
- Cross-site scripting (XSS)
- Cross-site request forgery (CSRF)
- Server-side request forgery (SSRF)
- Remote code execution
- Path traversal and file inclusion
- Insecure direct object references
- Sensitive data exposure
- Security configuration issues

**Out of Scope:**
- Social engineering attacks
- Physical attacks
- DoS/DDoS attacks (unless leading to RCE)
- Issues requiring physical access
- Recently disclosed vulnerabilities in third-party libraries (report to the library maintainers first)
- Theoretical vulnerabilities without proof of exploitation

### 🛡️ Security Best Practices

When running UpWatch in production:

1. **Keep updated**: Always use the latest stable version
2. **Secure configuration**: Follow the security guidelines in our documentation
3. **HTTPS only**: Never run in production without SSL/TLS
4. **Database security**: Use strong passwords and restricted user permissions
5. **Regular backups**: Maintain secure, tested backups
6. **Monitor logs**: Watch for suspicious activity in `writable/logs/`

---

**Thank you for helping keep UpWatch secure! 🛡️**