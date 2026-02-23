# Watered Project Seeding Commands

Use the commands below to seed the database with the new features (Teachings, Announcements, Landing Page Features).

### 1. Seed All at Once
Run this if you want to populate everything including the new features:
```bash
/opt/alt/php84/usr/bin/php artisan db:seed
```

### 2. Seed Only Specific New Features
If you want to run them individually:

**Landing Page Features:**
```bash
/opt/alt/php84/usr/bin/php artisan db:seed --class=LandingPageFeaturesSeeder
```

**Teachings (Blog):**
```bash
/opt/alt/php84/usr/bin/php artisan db:seed --class=TeachingsSeeder
```

**Announcements:**
```bash
/opt/alt/php84/usr/bin/php artisan db:seed --class=AnnouncementsSeeder
```

---
*Note: Make sure your migrations are up to date before seeding.*
```bash
/opt/alt/php84/usr/bin/php artisan migrate
```
