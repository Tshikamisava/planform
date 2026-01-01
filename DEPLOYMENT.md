# 🚀 Laravel DCR System Deployment Guide

## 📋 Prerequisites

1. **GitHub Repository** with your Laravel DCR system
2. **Hostking Hosting** or any SSH-accessible server
3. **SSH Access** to your production server
4. **Git** installed on production server

## 🔧 GitHub Actions Setup

### Step 1: Repository Secrets

Go to your GitHub repository → **Settings** → **Secrets and variables** → **Actions** → **Repository secrets**

Add these secrets:

| Secret Name | Description | Example |
|-------------|-------------|---------|
| `SSH_HOST` | Your server IP or domain | `192.168.1.100` or `yourdomain.com` |
| `SSH_USER` | SSH username | `root` or `ubuntu` |
| `SSH_KEY` | Private SSH key | `-----BEGIN RSA PRIVATE KEY-----...` |
| `APP_PATH` | Path to your Laravel app on server | `/var/www/html/dcr-system` |

### Step 2: SSH Key Setup

1. **Generate SSH Key** (if you don't have one):
```bash
ssh-keygen -t rsa -b 4096 -C "github-actions-deploy"
```

2. **Add Public Key to Server**:
```bash
# Copy the public key content
cat ~/.ssh/id_rsa.pub

# Add to server's authorized_keys
ssh user@your-server "echo 'your-public-key-content' >> ~/.ssh/authorized_keys"
```

3. **Add Private Key to GitHub Secrets**:
```bash
# Copy the private key content (including -----BEGIN/END lines)
cat ~/.ssh/id_rsa
```

### Step 3: Server Setup

Connect to your server and run:

```bash
# 1. Clone your repository
git clone https://github.com/yourusername/your-repo.git /var/www/html/dcr-system
cd /var/www/html/dcr-system

# 2. Set up environment
cp .env.example .env
php artisan key:generate

# 3. Install dependencies
composer install --no-dev --optimize-autoloader

# 4. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 5. Create storage link
php artisan storage:link

# 6. Run migrations
php artisan migrate --force

# 7. Clear caches
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🔄 Deployment Process

### Automatic Deployment (On Push to Main)

1. **Push changes to main branch**:
```bash
git add .
git commit -m "Your commit message"
git push origin main
```

2. **GitHub Actions will automatically**:
   - Connect to your server via SSH
   - Pull latest code
   - Install/update dependencies
   - Set proper permissions
   - Clear and rebuild caches
   - Run database migrations

### Manual Deployment (Optional)

If you need to deploy manually:

```bash
ssh user@your-server
cd /var/www/html/dcr-system
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
```

## 🔍 Troubleshooting

### Common Issues

1. **SSH Connection Failed**:
   - Check SSH host, user, and key in GitHub secrets
   - Ensure server allows SSH key authentication
   - Verify firewall settings

2. **Permission Issues**:
   - Ensure web server user (www-data) has proper permissions
   - Check file ownership on storage and bootstrap/cache directories

3. **Composer Issues**:
   - Ensure Composer is installed on server
   - Check memory limits in php.ini
   - Verify composer.json is valid

4. **Migration Issues**:
   - Ensure database credentials are correct in .env
   - Check database connectivity
   - Verify migration files are compatible

### Debug Mode

To debug deployment issues, modify the workflow temporarily:

```yaml
- name: Deploy via SSH
  uses: appleboy/ssh-action@v1.0.3
  with:
    host: ${{ secrets.SSH_HOST }}
    username: ${{ secrets.SSH_USER }}
    key: ${{ secrets.SSH_KEY }}
    debug: true  # Add this for debugging
    script: |
      set -e  # Keep all existing commands
```

## 📁 File Structure After Deployment

```
/var/www/html/dcr-system/
├── .github/workflows/deploy.yml
├── app/
├── bootstrap/
├── config/
├── database/
├── public/
│   └── index.php
├── resources/
├── routes/
├── storage/
│   ├── app/
│   ├── framework/
│   └── logs/
├── vendor/
├── .env
├── .gitignore
├── artisan
├── composer.json
└── composer.lock
```

## 🔐 Security Considerations

1. **Environment Variables**: Never commit .env to git
2. **SSH Keys**: Use dedicated deploy keys, not personal ones
3. **Database**: Use strong passwords and limit access
4. **File Permissions**: Restrict access to sensitive files
5. **HTTPS**: Configure SSL certificate in production

## 📊 Monitoring

After deployment, monitor:

1. **Application Logs**: `storage/logs/laravel.log`
2. **Web Server Logs**: `/var/log/nginx/` or `/var/log/apache2/`
3. **System Resources**: Disk space, memory, CPU usage
4. **Database Performance**: Query optimization, indexing

## 🚀 Production Optimizations

1. **OPcache**: Enable and configure PHP OPcache
2. **Database**: Use connection pooling and read replicas
3. **CDN**: Serve static assets via CDN
4. **Load Balancer**: Distribute traffic across multiple servers
5. **Caching**: Implement Redis or Memcached

## 📞 Support

For deployment issues:
1. Check GitHub Actions logs
2. Review server logs
3. Verify configuration files
4. Test SSH connection manually

---

**🎉 Your Laravel DCR System is now ready for automated deployment!**
