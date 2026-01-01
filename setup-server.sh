#!/bin/bash

# 🚀 Laravel DCR System Server Setup Script
# Run this script on your production server to set up the deployment environment

set -e

echo "🔧 Laravel DCR System Server Setup"
echo "=================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    print_error "Please run as root (use sudo)"
    exit 1
fi

# Update system packages
print_status "Updating system packages..."
apt update && apt upgrade -y

# Install required packages
print_status "Installing required packages..."
apt install -y git curl wget unzip zip nginx php php-fpm php-cli php-mbstring php-xml php-zip php-mysql php-curl php-bcmath php-json php-tokenizer composer

# Install Node.js (for frontend assets)
print_status "Installing Node.js..."
curl -fsSL https://deb.nodesource.com/setup_18.x | bash -
apt-get install -y nodejs

# Create web directory
print_status "Creating web directory..."
mkdir -p /var/www/html
cd /var/www/html

# Ask for repository URL
echo ""
print_warning "Please provide your GitHub repository URL:"
read -p "Repository URL (e.g., https://github.com/user/repo.git): " REPO_URL

if [ -z "$REPO_URL" ]; then
    print_error "Repository URL is required"
    exit 1
fi

# Clone repository
print_status "Cloning repository..."
git clone "$REPO_URL" dcr-system
cd dcr-system

# Set permissions
print_status "Setting up permissions..."
chown -R www-data:www-data /var/www/html/dcr-system
chmod -R 775 /var/www/html/dcr-system/storage
chmod -R 775 /var/www/html/dcr-system/bootstrap/cache

# Install Composer dependencies
print_status "Installing Composer dependencies..."
sudo -u www-data composer install --no-dev --optimize-autoloader

# Setup environment file
print_status "Setting up environment file..."
if [ ! -f .env ]; then
    cp .env.example .env
    print_warning "Please edit .env file with your database and application settings"
fi

# Generate application key
print_status "Generating application key..."
sudo -u www-data php artisan key:generate

# Create storage link
print_status "Creating storage link..."
sudo -u www-data php artisan storage:link

# Setup Nginx configuration
print_status "Setting up Nginx configuration..."
cat > /etc/nginx/sites-available/dcr-system << 'EOF'
server {
    listen 80;
    server_name your-domain.com;  # Change this to your domain
    root /var/www/html/dcr-system/public;
    index index.php index.html index.htm;

    client_max_body_size 100M;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
    }

    location ~ /\.ht {
        deny all;
    }

    # Gzip compression
    gzip on;
    gzip_vary on;
    gzip_proxied any;
    gzip_comp_level 6;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
}
EOF

# Enable site
ln -s /etc/nginx/sites-available/dcr-system /etc/nginx/sites-enabled/
rm -f /etc/nginx/sites-enabled/default

# Test Nginx configuration
nginx -t

# Restart services
print_status "Restarting services..."
systemctl restart nginx
systemctl restart php8.2-fpm

# Enable services on boot
systemctl enable nginx
systemctl enable php8.2-fpm

# Setup firewall
print_status "Configuring firewall..."
ufw allow 22/tcp
ufw allow 80/tcp
ufw allow 443/tcp
ufw --force enable

# Display final information
echo ""
print_status "🎉 Server setup completed!"
echo ""
echo "📋 Next Steps:"
echo "1. Edit /etc/nginx/sites-available/dcr-system and change 'your-domain.com' to your actual domain"
echo "2. Edit /var/www/html/dcr-system/.env with your database credentials"
echo "3. Run database migrations: sudo -u www-data php artisan migrate --force"
echo "4. Clear caches: sudo -u www-data php artisan optimize:clear"
echo "5. Set up SSL certificate (recommended): sudo certbot --nginx"
echo ""
echo "🔧 Useful Commands:"
echo "- View logs: tail -f /var/log/nginx/error.log"
echo "- Restart Nginx: systemctl restart nginx"
echo "- Restart PHP-FPM: systemctl restart php8.2-fpm"
echo "- Deploy updates: cd /var/www/html/dcr-system && git pull origin main"
echo ""
echo "📁 Application Path: /var/www/html/dcr-system"
echo "🌐 Web Root: /var/www/html/dcr-system/public"
echo ""
print_warning "Don't forget to configure your GitHub Actions secrets for automated deployment!"
