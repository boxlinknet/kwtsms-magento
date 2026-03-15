# Security Policy

## Reporting a Vulnerability

If you discover a security vulnerability in this module, please report it responsibly.

**Email:** support@kwtsms.com

Please include:
- Description of the vulnerability
- Steps to reproduce
- Potential impact
- Suggested fix (if any)

We will acknowledge receipt within 48 hours and provide a timeline for resolution.

## Security Practices

- API credentials are encrypted at rest using Magento's built-in encryption
- All API communication uses HTTPS POST only
- Credentials are never logged or exposed to the frontend
- Phone numbers are stored unmasked in the database but displayed masked in the admin UI
- All admin forms use CSRF protection (form_key)
- All output is escaped to prevent XSS
- No direct SQL queries; all database access uses Magento's repository pattern
- ACL resources enforce granular admin permissions

## Supported Versions

| Version | Supported |
|---------|-----------|
| 1.x     | Yes       |
