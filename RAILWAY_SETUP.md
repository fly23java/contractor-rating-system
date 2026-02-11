# Railway Deployment Guide

## Required Environment Variables

Add these variables in Railway's **Variables** tab:

### Application Settings
```
APP_NAME="Contractor Rating System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://lena.up.railway.app
APP_KEY=base64:YOUR_APP_KEY_HERE
```

### Database Settings (Auto-filled by Railway MySQL)
```
DB_CONNECTION=mysql
DB_HOST=${{MySQL.MYSQLHOST}}
DB_PORT=${{MySQL.MYSQLPORT}}
DB_DATABASE=${{MySQL.MYSQLDATABASE}}
DB_USERNAME=${{MySQL.MYSQLUSER}}
DB_PASSWORD=${{MySQL.MYSQLPASSWORD}}
```

### Session & Cache
```
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

### Logging
```
LOG_CHANNEL=errorlog
LOG_LEVEL=error
```

### Security (Important for HTTPS)
```
ASSET_URL=https://lena.up.railway.app
```

## Deployment Steps

1. **Create New Project** on Railway
2. **Deploy from GitHub**: Select `fly23java/contractor-rating-system`
3. **Add MySQL Database**: Click "+ New" → "Database" → "MySQL"
4. **Set Environment Variables**: Copy the variables above to Railway's Variables tab
5. **Deploy**: Railway will automatically build and deploy

## What Happens During Deployment

The `nixpacks.toml` file automates:
- ✅ Installing PHP dependencies with Composer
- ✅ Setting proper permissions for storage folders
- ✅ Creating storage symlink
- ✅ Caching config, routes, and views
- ✅ Running database migrations
- ✅ Seeding initial data (admin users)
- ✅ Starting the application server

## Default Users (Created by Seeder)

After deployment, you can login with:

**Contractor:**
- Email: `contractor@example.com`
- Password: `password`

**Supervisor:**
- Email: `supervisor@example.com`
- Password: `password`

**Owner:**
- Email: `owner@example.com`
- Password: `password`

## Troubleshooting

### Mixed Content Errors (HTTP/HTTPS)
The app is configured to force HTTPS in production via `AppServiceProvider.php`.
Make sure `APP_ENV=production` is set in Railway.

### Database Connection Issues
Verify that the MySQL service is linked and the `${{MySQL.*}}` variables are correctly set.

### Storage Permission Issues
The build process sets `chmod 775` on storage folders. If issues persist, check Railway logs.
