# 🚀 Laravel DCR System - Deployment Guide

## 📋 Quick Start

### 1. Automated Deployment Setup

```bash
# Generate SSH keys for GitHub Actions
./generate-deploy-key.sh

# Setup your production server
./setup-server.sh
```

### 2. GitHub Configuration

1. **Add Repository Secrets**:
   - `SSH_HOST`: Your server IP/domain
   - `SSH_USER`: SSH username  
   - `SSH_KEY`: Private SSH key (from generate-deploy-key.sh)
   - `APP_PATH`: Laravel app path (e.g., `/var/www/html/dcr-system`)

2. **Push to Main Branch**:
```bash
git add .
git commit -m "Setup deployment configuration"
git push origin main
```

## 🔄 Deployment Process

The GitHub Actions workflow will automatically:

1. ✅ Connect to your server via SSH
2. ✅ Pull latest code from main branch
3. ✅ Install Composer dependencies
4. ✅ Set proper file permissions
5. ✅ Clear and rebuild caches
6. ✅ Run database migrations

## 📁 Files Created

- `.github/workflows/deploy.yml` - GitHub Actions workflow
- `setup-server.sh` - Server initialization script
- `generate-deploy-key.sh` - SSH key generation script
- `DEPLOYMENT.md` - Detailed deployment documentation
- `.gitignore` - Updated with deployment exclusions

## 🔧 Manual Deployment (Optional)

```bash
ssh user@your-server
cd /var/www/html/dcr-system
git pull origin main
composer install --no-dev --optimize-autoloader
php artisan optimize:clear
php artisan migrate --force
```

## 📊 What's Deployed

Your Laravel DCR System includes:

- ✅ Complete DCR management functionality
- ✅ Impact rating and escalation system
- ✅ Comprehensive reporting dashboard
- ✅ User management and authentication
- ✅ Email notifications
- ✅ File attachments support
- ✅ Performance metrics and analytics

## 🔍 Monitoring

After deployment, monitor:
- Application logs: `storage/logs/laravel.log`
- Web server logs: `/var/log/nginx/`
- Database performance
- System resources

## 🆘 Support

For deployment issues:
1. Check GitHub Actions logs
2. Review server configuration
3. Verify SSH connectivity
4. Check environment variables

---

**🎉 Your Laravel DCR System is ready for production deployment!**
