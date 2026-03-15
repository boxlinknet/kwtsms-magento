# kwtSMS for Magento 2

SMS notifications and OTP verification for Adobe Commerce (Magento 2) via the [kwtSMS](https://www.kwtsms.com) gateway.

## Features

- **Order SMS notifications:** New order, status change, shipment, invoice, refund, cancellation
- **Customer SMS:** Welcome message on registration
- **Admin alerts:** New order, new customer, low stock
- **Bilingual templates:** English and Arabic with placeholder support
- **Full SMS logging:** Searchable admin grid with export
- **Dashboard:** SMS analytics, balance, connection status
- **Test mode:** Send to API queue without delivering (credits recoverable)
- **Phone validation:** 90+ country rules with Arabic numeral conversion
- **Daily sync:** Auto-refresh balance, sender IDs, and coverage via cron

## Requirements

- Magento 2.4.7+
- PHP 8.2 or 8.3
- Active [kwtSMS](https://www.kwtsms.com) account with API access

## Installation

### Via Composer (Marketplace)

```bash
composer require kwtsms/module-sms-integration
bin/magento module:enable KwtSms_SmsIntegration
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### Manual Installation

1. Copy the module to `app/code/KwtSms/SmsIntegration/`
2. Run:
```bash
bin/magento module:enable KwtSms_SmsIntegration
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

## Configuration

1. Go to **Stores > Configuration > kwtSMS > Gateway Settings**
2. Enter your API username and password
3. Click **Test Connection**
4. Select your Sender ID
5. Enable the module

## kwtSMS Account

1. Register at [kwtsms.com](https://www.kwtsms.com)
2. Request API access from your account menu
3. Find your API username and password in your account API page

**Note:** API username is NOT your phone number.

## Development

### Docker Setup

```bash
docker compose up -d
docker compose exec php bash /var/www/html/docker/install-magento.sh
```

- Storefront: http://localhost:8080
- Admin: http://localhost:8080/admin (admin / Admin123!)
- phpMyAdmin: http://localhost:8082

## Security

Report security vulnerabilities to: support@kwtsms.com

See [SECURITY.md](SECURITY.md) for details.

## License

OSL-3.0
