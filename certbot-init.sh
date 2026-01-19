#!/bin/bash

# HTTPS Setup Script for Laravel on AWS
# This script obtains SSL certificate from Let's Encrypt using Certbot

set -e  # Exit on error

# Check if domain is provided
if [ -z "$1" ]; then
    echo "Usage: ./certbot-init.sh your-domain.com"
    echo "Example: ./certbot-init.sh api.example.com"
    exit 1
fi

DOMAIN=$1
EMAIL="your-email@example.com"  # Change this to your email

echo "========================================="
echo "Setting up HTTPS for: $DOMAIN"
echo "========================================="

# Step 1: Stop Nginx to free port 80
echo "Stopping Nginx container..."
docker-compose stop nginx

# Step 2: Run Certbot in standalone mode
echo "Obtaining SSL certificate from Let's Encrypt..."
docker run -it --rm \
    -v $(pwd)/certbot/conf:/etc/letsencrypt \
    -v $(pwd)/certbot/www:/var/www/certbot \
    -p 80:80 \
    certbot/certbot certonly \
    --standalone \
    --email $EMAIL \
    --agree-tos \
    --no-eff-email \
    -d $DOMAIN

# Step 3: Update nginx-ssl.conf with your domain
echo "Updating Nginx configuration..."
sed -i "s/your-domain.com/$DOMAIN/g" nginx-ssl.conf

# Step 4: Restart containers with SSL
echo "Starting containers with HTTPS enabled..."
docker-compose up -d

echo "========================================="
echo "HTTPS setup complete!"
echo "Your API is now available at: https://$DOMAIN"
echo "========================================="
echo ""
echo "Next steps:"
echo "1. Update your frontend .env.production with: VITE_API_URL=https://$DOMAIN/api"
echo "2. Rebuild and deploy your frontend"
echo "3. Test your API: curl https://$DOMAIN/api"
echo ""
echo "Certificate will auto-renew. To manually renew:"
echo "docker-compose run --rm certbot renew"
