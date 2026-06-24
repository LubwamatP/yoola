# Yoola.ug Production Deployment Checklist
**Date:** 2026-02-08

## Pre-Deployment

### 1. Database Migration
Run this SQL on production database:
```bash
mysql -u YOUR_USER -p YOUR_DATABASE < deployment/production_database.sql
```

Or import via phpMyAdmin: Upload `deployment/production_database.sql`

### 2. Environment Variables (.env)
Add these to production `.env`:
```env
# AI Chat Configuration (Optional - for AI inbox responder)
AI_PROVIDER=claude
CLAUDE_API_KEY=your_key_here
# OR for Gemini (free tier):
AI_PROVIDER=gemini
GEMINI_API_KEY=your_key_here
```

## Files to Upload

### New Services (app/Services/)
- `AIChatService.php`
- `AIInboxResponderService.php`
- `SmartNotificationService.php`
- `RecommendationEngine.php`
- `ViewTrackingService.php`
- `EnhancedSearchService.php`

### New Controllers (app/Http/Controllers/)
- `Admin/AIOperationsController.php`
- `Web/ProductDetailsController.php` (if modified)
- `Web/PowerCalculatorController.php`

### Modified Controllers
- `Web/HomeController.php` - Added randomization
- `Web/ProductListController.php` - Enhanced search

### New Console Commands (app/Console/Commands/)
- `ProcessAIInbox.php`

### New Views (resources/themes/theme_aster/theme-views/)
- `admin-views/ai-operations/` (entire folder)
- `partials/_trust-strip.blade.php`
- `power-calculator.blade.php`

### Modified Views
- `home.blade.php` - Flash deals, randomization
- `product/details.blade.php` - WhatsApp button, trust badges
- `layouts/app.blade.php` - Schema.org fix (@@context)
- `partials/_flash-deals.blade.php` - Condition fix
- `partials/_home-categories.blade.php`
- `partials/_more-stores.blade.php`
- `partials/_productSEOMetaContentData.blade.php` - Schema.org fix

### Modified Assets
- `public/assets/js/main.js` - Swiper touch improvements

### Admin Sidebar
- `layouts/partials/_settings-sidebar.blade.php` - AI Operations menu

### Routes
- `routes/admin.php` - AI Operations routes
- `routes/web.php` - Power calculator route

### Config Files
- `.htaccess` (in public/) - 301 redirects

## Post-Deployment

### 1. Clear All Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### 2. Set Up Cron Job (for AI inbox processing)
```bash
* * * * * cd /path/to/yoola && php artisan schedule:run >> /dev/null 2>&1
```

### 3. Verify
- [ ] Homepage loads with Flash Deals
- [ ] Trust strip shows "Cash on Delivery"
- [ ] Product pages have WhatsApp button
- [ ] Slider swipes quickly on mobile
- [ ] Admin can access /admin/ai-operations

## Rollback Plan
If issues occur:
1. Restore previous files from backup
2. Database tables are additive (won't break existing data)
3. Clear caches again

## Notes
- FCM is already configured ✓
- Flash Deal "Holiday welcome" was disabled (had 0 products)
- "Black November Extended" is now active (6 products)
