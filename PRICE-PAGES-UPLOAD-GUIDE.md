# Price Pages Upload Guide
## Programmatic SEO System for Yoola.ug

### Files to Upload

**Frontend:**
```
📁 app/Models/PricePage.php
📁 app/Http/Controllers/Web/PricePageController.php
📁 database/migrations/2026_02_15_020000_create_price_pages_table.php
📁 database/seeders/PricePageSeeder.php
📁 resources/themes/theme_aster/theme-views/price-pages/
   └── index.blade.php
   └── show.blade.php
   └── sitemap.blade.php
📁 routes/web/routes.php (APPEND the price page routes)
```

**Admin Panel:**
```
📁 app/Http/Controllers/Admin/Settings/PricePageController.php
📁 resources/views/admin-views/price-pages/
   └── index.blade.php
   └── create.blade.php
   └── edit.blade.php
📁 routes/admin/routes.php (APPEND the admin routes at the end)
```

### Step 1: Upload Files
Upload all files above to production via FTP/SFTP or your hosting panel.

### Step 2: Add Routes
Add these lines to `routes/web/routes.php` (at the end, before the closing):

```php
// Price Pages - Programmatic SEO
Route::get('/prices', [\App\Http\Controllers\Web\PricePageController::class, 'index'])->name('price-pages.index');
Route::get('/prices/{slug}', [\App\Http\Controllers\Web\PricePageController::class, 'show'])->name('price-pages.show');
Route::get('/sitemap-prices.xml', [\App\Http\Controllers\Web\PricePageController::class, 'sitemap'])->name('price-pages.sitemap');
```

### Step 3: Run Migration
```bash
php artisan migrate
```

### Step 4: Seed Initial Pages
```bash
php artisan db:seed --class=PricePageSeeder
```

### Step 5: Verify
Visit: https://yoola.ug/prices

You should see the hub page with all 10 price pages listed.

### Step 6: Submit Sitemap
Add to Google Search Console:
- https://yoola.ug/sitemap-prices.xml

---

### Initial Pages Created (10)

1. `/prices/hisense-tv-price-in-uganda`
2. `/prices/samsung-tv-price-in-uganda`
3. `/prices/32-inch-tv-price-in-uganda`
4. `/prices/smart-tv-price-in-uganda`
5. `/prices/samsung-fridge-price-in-uganda`
6. `/prices/double-door-fridge-price-in-uganda`
7. `/prices/gas-cooker-price-in-uganda`
8. `/prices/blender-price-in-uganda`
9. `/prices/microwave-price-in-uganda`
10. `/prices/washing-machine-price-in-uganda`

Each page has:
- ✅ Unique title, meta description, H1
- ✅ Unique intro paragraph
- ✅ Unique buying guide
- ✅ FAQ section with schema markup
- ✅ Dynamic product listing
- ✅ WhatsApp CTA (sticky)
- ✅ Trust signals

---

### What This Does

Targets searches like:
- "Hisense TV price in Uganda" (500-2,000 searches/mo)
- "Samsung fridge price in Uganda" (300-800/mo)
- "Gas cooker price in Uganda" (200-500/mo)

**Expected Results:**
- Month 1: 50 pages indexed, ~200 organic visits
- Month 3: Top 10 rankings for 30+ keywords, ~1,000 visits/mo
- Month 6: ~3,000 organic visits/mo, significant WhatsApp inquiries
