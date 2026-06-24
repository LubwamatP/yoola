# Upload Checklist for Patrick

## Step 1: Upload Files via cPanel File Manager

Upload **entire contents** of `D:\YOOLA FEB\updated1\` to `/home/zuuldqak/public_html/`

**Or upload these specific folders:**
- [x] `app/` (all subfolders)
- [x] `config/`
- [x] `database/migrations/`
- [x] `public/assets/js/presence-tracker.js`
- [x] `resources/views/admin-views/`
- [x] `resources/themes/theme_aster/theme-views/`
- [x] `routes/`
- [x] `Modules/AI/`
- [x] `.env` (update GEMINI_API_KEY)
- [x] `modules_statuses.json`

---

## Step 2: Run Database Setup

### Option A: Via phpMyAdmin (Easiest)
1. Go to cPanel → phpMyAdmin
2. Select `yoola` database
3. Click "Import"
4. Upload `database_setup.sql`
5. Click "Go"

### Option B: Via SSH
```bash
mysql -u zuuldqak -p yoola < database_setup.sql
```

---

## Step 3: Clear Caches (via SSH or cPanel Terminal)

```bash
cd /home/zuuldqak/public_html
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

---

## Step 4: Add Cron Job

In cPanel → Cron Jobs, add:
```
*/15 * * * * cd /home/zuuldqak/public_html && php artisan schedule:run >> /dev/null 2>&1
```

---

## Step 5: Verify

- [ ] Homepage loads: https://yoola.ug
- [ ] Power Calculator: https://yoola.ug/power-calculator
- [ ] Admin Login: https://yoola.ug/admin
- [ ] AI Operations menu visible in admin
- [ ] Live Analytics menu visible in admin
- [ ] Power Calculator settings in admin

---

## Files Created for Deployment

| File | Purpose |
|------|---------|
| `DEPLOYMENT_READY.md` | Full deployment guide |
| `database_setup.sql` | SQL to run in phpMyAdmin |
| `deploy.sh` | Shell script for SSH deployment |
| `UPLOAD_CHECKLIST.md` | This checklist |
