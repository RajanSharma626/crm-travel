# SMS Integration Guide

This document explains how to integrate and use the SMS functionality for sending messages to leads.

## Overview

The SMS integration allows you to send templated SMS messages to leads directly from the CRM. The system uses a PHP-based SMS API with DLT (Distributed Ledger Technology) compliance for Indian SMS regulations.

## Features

- ✅ Send SMS to multiple phone numbers (Primary, Secondary, Emergency)
- ✅ Pre-configured message templates
- ✅ Dynamic variable replacement (customer name, destination, travel date, etc.)
- ✅ Template management via configuration
- ✅ Comprehensive logging and error handling
- ✅ Support for both transactional and promotional routes

## Setup Instructions

### 1. Configure Environment Variables

Add the following variables to your `.env` file:

```env
# SMS API Configuration
SMS_API_URL=http://hostname.in/api/send/sms
SMS_AUTH_KEY=your_actual_auth_key_here
SMS_SENDER=ABCXYZ
SMS_ROUTE=TR

# SMS Template IDs (DLT approved)
SMS_TEMPLATE_WELCOME=1234567890123456789
SMS_TEMPLATE_FOLLOWUP=1234567890123456789
SMS_TEMPLATE_QUOTE=1234567890123456789
SMS_TEMPLATE_BOOKING=1234567890123456789
SMS_TEMPLATE_PAYMENT=1234567890123456789
SMS_TEMPLATE_TRAVEL=1234567890123456789
SMS_TEMPLATE_THANKYOU=1234567890123456789
```

**Important:** Replace the placeholder values with your actual SMS provider credentials.

### 2. Get Your SMS Provider Credentials

Contact your SMS provider to obtain:

- **API URL**: The endpoint for sending SMS
- **Auth Key**: Your authentication key
- **Sender ID**: Your DLT approved sender ID
- **Template IDs**: 19-digit DLT approved template IDs for each message type

### 3. Update Template Messages (Optional)

Edit the templates in `config/services.php` under the `sms.templates` section to match your DLT approved message templates.

## Available Templates

The system comes with 7 pre-configured templates:

1. **Welcome Message**: Sent to new leads
2. **Follow Up**: For checking in with existing leads
3. **Quote Ready**: When a quote is prepared
4. **Booking Confirmation**: After booking is confirmed
5. **Payment Reminder**: For pending payments
6. **Travel Reminder**: Before travel date
7. **Thank You**: Post-trip appreciation

## How to Use

### From the Leads Page

1. Navigate to the Leads page
2. Find the lead you want to send SMS to
3. Click the SMS icon (message square) next to the phone number
4. Select the phone numbers to send to (Primary, Secondary, or Emergency)
5. Choose a message template
6. Click "Send SMS"

### Dynamic Variables

The following variables are automatically replaced in templates:

- `{{customer_name}}`: Lead's full name
- `{{first_name}}`: Lead's first name
- `{{last_name}}`: Lead's last name
- `{{destination}}`: Travel destination
- `{{travel_date}}`: Travel date (formatted as "d M Y")
- `{{service}}`: Service type

## API Endpoints

### Send SMS

```
POST /leads/sms/send
```

**Request Body:**

```json
{
    "lead_id": 123,
    "phone_numbers": ["9876543210", "9876543211"],
    "template_key": "welcome"
}
```

**Response:**

```json
{
  "success": true,
  "message": "SMS sent successfully",
  "http_code": 200,
  "response": {...}
}
```

### Get Templates

```
GET /leads/sms/templates
```

**Response:**

```json
{
    "success": true,
    "templates": [
        {
            "key": "welcome",
            "name": "Welcome Message",
            "message": "Hi {{customer_name}}, thank you for contacting us!...",
            "description": "Welcome message for new leads"
        }
    ]
}
```

### Send Custom SMS

```
POST /leads/sms/send-custom
```

**Request Body:**

```json
{
    "phone_numbers": ["9876543210"],
    "message": "Your custom message here",
    "template_id": "1234567890123456789",
    "campaign_name": "Custom Campaign"
}
```

## File Structure

```
app/
├── Http/Controllers/
│   └── LeadsSmsController.php    # SMS controller
├── Services/
│   └── SmsService.php             # SMS service class
config/
└── services.php                   # SMS configuration
routes/
└── web.php                        # SMS routes
resources/views/leads/
└── index.blade.php                # Frontend integration
```

## Troubleshooting

### SMS Not Sending

1. **Check Credentials**: Verify your SMS_AUTH_KEY and SMS_SENDER in `.env`
2. **Check Template IDs**: Ensure template IDs are DLT approved and correct
3. **Check Logs**: Review `storage/logs/laravel.log` for error messages
4. **Network Issues**: Verify your server can reach the SMS API URL

### Template Not Found Error

- Ensure the template key exists in `config/services.php`
- Run `php artisan config:clear` to clear config cache

### Invalid Phone Number

- Phone numbers should be in the format: 10 digits (e.g., 9876543210)
- For international numbers, include country code

## Logging

All SMS activities are logged in `storage/logs/laravel.log`:

- SMS requests with receiver details
- API responses
- Errors and exceptions

Example log entry:

```
[2026-01-26 23:00:00] local.INFO: SMS sent to lead {"lead_id":123,"phone_numbers":["9876543210"],"template":"welcome","result":{...}}
```

## Security Considerations

1. **Never commit** your `.env` file with actual credentials
2. **Restrict access** to SMS sending functionality based on user roles
3. **Monitor usage** to prevent abuse
4. **Validate phone numbers** before sending
5. **Rate limiting**: Consider implementing rate limits to prevent spam

## Customization

### Adding New Templates

1. Add template configuration in `config/services.php`:

```php
'new_template' => [
    'name' => 'New Template',
    'message' => 'Your message with {{variables}}',
    'template_id' => env('SMS_TEMPLATE_NEW', '1234567890123456789'),
    'campaign_name' => 'New Campaign',
    'coding' => 1,
    'description' => 'Description of the template',
],
```

2. Add environment variable in `.env`:

```env
SMS_TEMPLATE_NEW=your_dlt_approved_template_id
```

3. The template will automatically appear in the SMS modal

### Modifying SMS Service

Edit `app/Services/SmsService.php` to customize:

- API request format
- Response handling
- Error handling
- Timeout settings

## Support

For issues or questions:

1. Check the logs in `storage/logs/laravel.log`
2. Review this documentation
3. Contact your SMS provider for API-specific issues

## License

This SMS integration is part of the CRM Travel application.
