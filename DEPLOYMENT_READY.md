# Yoola.ug Deployment Package
**Date:** February 12, 2026

## 📦 Files to Upload

Upload the entire `D:\YOOLA FEB\updated1` folder to `/home/zuuldqak/public_html/`

### Key Modified Files:
```
app/
├── Http/Controllers/Admin/
│   ├── AIOperationsController.php
│   ├── AnalyticsDashboardController.php
│   └── PowerCalculatorSettingsController.php
├── Http/Controllers/Web/
│   ├── PresenceController.php
│   ├── ProductDetailsController.php (random "More from store")
│   └── PowerCalculatorController.php
├── Models/
│   ├── ActiveSession.php (bot detection)
│   ├── Cart.php (added user relationship)
│   ├── PowerCalculatorTariff.php
│   ├── PowerCalculatorCategory.php
│   └── PowerCalculatorAppliance.php
├── Services/
│   └── SmartNotificationService.php
└── Console/Commands/
    └── ProcessSmartNotifications.php

config/
└── gemini.php (AI enabled)

database/migrations/
├── 2026_02_11_230000_create_active_sessions_table.php
├── 2026_02_11_231000_add_bot_columns_to_active_sessions.php
├── 2026_02_12_010000_create_power_calculator_settings_tables.php
├── 2026_02_12_020001_add_tou_columns_to_tariffs.php
└── 2026_02_12_020002_create_missing_power_calculator_tables.php

public/assets/js/
└── presence-tracker.js

resources/views/admin-views/
├── ai-operations/
│   ├── dashboard.blade.php
│   ├── conversations.blade.php
│   ├── notifications.blade.php
│   ├── leads.blade.php
│   └── settings.blade.php
├── analytics/
│   └── dashboard.blade.php (bot vs human stats)
└── power-calculator/
    └── settings.blade.php

resources/themes/theme_aster/theme-views/
├── layouts/main-script.blade.php (presence tracker included)
├── power-calculator.blade.php (urgency banner removed)
├── home.blade.php (H1 fix)
└── product/details.blade.php (H1 fix)

routes/
├── admin/routes.php (AI Operations, Analytics, Power Calculator routes)
└── web/routes.php (Presence tracking routes)

modules_statuses.json (AI: true)
Modules/AI/Addon/info.php (is_published: 1)
.env (GEMINI_API_KEY updated)
```

## 🗄️ Database Migrations

Run these commands on the server after uploading:

```bash
cd /home/zuuldqak/public_html

# Run migrations
php artisan migrate --force

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📊 New Database Tables

These tables will be created:
- `active_sessions` - Real-time visitor tracking with bot detection
- `power_calculator_tariffs` - UEDCL electricity rates
- `power_calculator_categories` - Appliance categories
- `power_calculator_appliances` - Appliances with wattage
- `power_calculator_settings` - Calculator settings

## ⚙️ Environment Variables

Make sure `.env` has:
```
GEMINI_API_KEY=AIzaSyDduzKDdCw4kWTy8kE2equ16hzXDNb-XQk
GEMINI_SEARCH_ENABLED=true
```

## 🔄 Cron Jobs (Add to cPanel)

Add this cron job for smart notifications:
```
*/15 * * * * cd /home/zuuldqak/public_html && php artisan schedule:run >> /dev/null 2>&1
```

## ✅ Features Included

1. **AI Operations Dashboard** - Admin → AI Operations
2. **Live Analytics** - Bot vs Human tracking
3. **Power Calculator** - Admin settings for tariffs/appliances
4. **Smart Notifications** - Cart abandonment, price drops
5. **Presence Tracking** - Real-time visitor counts
6. **SEO Fixes** - Single title tags, proper H1s
7. **Random "More from Store"** - Products shuffle on refresh

## 🧪 Post-Deployment Testing

1. Visit https://yoola.ug - Check homepage loads
2. Visit /power-calculator - Check calculator works
3. Admin → AI Operations - Should show dashboard
4. Admin → Live Analytics - Should show bot/human stats
5. Admin → Power Calculator - Click "Seed Defaults"
6. View any product page - "More from store" should be random

## ⚠️ Important Notes

- Valentine's banner has been REMOVED (as requested)
- AI features are now ENABLED with Gemini
- Bot detection tracks 50+ bot patterns
- Smart notifications require users to have FCM tokens
