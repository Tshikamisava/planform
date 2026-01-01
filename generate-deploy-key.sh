#!/bin/bash

# 🔑 Generate SSH Deploy Keys for GitHub Actions
# This script generates SSH keys for automated deployment

set -e

echo "🔑 SSH Deploy Key Generator for GitHub Actions"
echo "=============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

print_header() {
    echo -e "${BLUE}=== $1 ===${NC}"
}

# Check if ssh-keygen is available
if ! command -v ssh-keygen &> /dev/null; then
    print_error "ssh-keygen is not installed. Please install OpenSSH."
    exit 1
fi

# Get project name
echo ""
print_header "Project Information"
read -p "Enter your project name (e.g., dcr-system): " PROJECT_NAME
if [ -z "$PROJECT_NAME" ]; then
    PROJECT_NAME="dcr-system"
fi

# Generate SSH key pair
print_header "Generating SSH Key Pair"
KEY_NAME="${PROJECT_NAME}-deploy-key"

print_status "Generating SSH key pair: $KEY_NAME"
ssh-keygen -t rsa -b 4096 -C "github-actions-deploy-$PROJECT_NAME" -f "$KEY_NAME" -N ""

# Display keys
print_header "Generated Keys"

echo ""
print_status "🔓 Public Key (add to server's ~/.ssh/authorized_keys):"
echo "-------------------------------------------------------------"
echo ""
cat "${KEY_NAME}.pub"
echo ""

print_warning "Copy the public key above and add it to your server's ~/.ssh/authorized_keys file"
echo ""

read -p "Press Enter to continue to private key..."

echo ""
print_status "🔒 Private Key (add to GitHub Secrets as SSH_KEY):"
echo "-----------------------------------------------------------"
echo ""
cat "$KEY_NAME"
echo ""

print_warning "Copy the private key above and add it to GitHub repository secrets"
echo ""

# Instructions
print_header "Setup Instructions"

echo ""
print_status "📋 Next Steps:"
echo ""
echo "1. 🖥️  Add PUBLIC KEY to your server:"
echo "   ssh user@your-server 'echo \"$(cat ${KEY_NAME}.pub)\" >> ~/.ssh/authorized_keys'"
echo ""
echo "2. 🔐 Add PRIVATE KEY to GitHub:"
echo "   - Go to your GitHub repository"
echo "   - Settings → Secrets and variables → Actions → Repository secrets"
echo "   - Add new secret named: SSH_KEY"
echo "   - Paste the private key content"
echo ""
echo "3. ⚙️  Add other required secrets:"
echo "   - SSH_HOST: your server IP or domain"
echo "   - SSH_USER: your SSH username"
echo "   - APP_PATH: path to Laravel app (e.g., /var/www/html/dcr-system)"
echo ""
echo "4. 🧹 Clean up local key files:"
echo "   rm $KEY_NAME $KEY_NAME.pub"
echo ""

# Test connection option
read -p "Do you want to test SSH connection to your server? (y/n): " TEST_CONNECTION

if [[ $TEST_CONNECTION =~ ^[Yy]$ ]]; then
    echo ""
    read -p "Enter your server host (IP or domain): " SERVER_HOST
    read -p "Enter your SSH username: " SSH_USER
    
    print_status "Testing SSH connection..."
    
    # Add key to ssh-agent for testing
    ssh-add "$KEY_NAME" 2>/dev/null || true
    
    if ssh -o ConnectTimeout=10 -o StrictHostKeyChecking=no "$SSH_USER@$SERVER_HOST" "echo 'SSH connection successful!'"; then
        print_status "✅ SSH connection test successful!"
    else
        print_error "❌ SSH connection test failed!"
        print_warning "Please check your server credentials and ensure the public key is properly added"
    fi
fi

echo ""
print_status "🎉 SSH key generation completed!"
echo ""
print_warning "Remember to:"
echo "- Add public key to your server"
echo "- Add private key to GitHub secrets"
echo "- Clean up local key files when done"
echo "- Test the deployment workflow"
