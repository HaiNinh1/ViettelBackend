#!/bin/bash

# Self-Signed SSL Certificate Generator for AWS
# This creates a certificate that works with IP addresses (no domain needed)

set -e

echo "========================================="
echo "Generating Self-Signed SSL Certificate"
echo "========================================="

# Create directories
mkdir -p ssl

# Generate private key
echo "Generating private key..."
openssl genrsa -out ssl/privkey.pem 2048

# Generate certificate signing request (CSR) with IP address
echo "Generating certificate..."
openssl req -new -x509 -key ssl/privkey.pem -out ssl/fullchain.pem -days 365 \
    -subj "/C=VN/ST=Vietnam/L=Hanoi/O=ViettelQLNS/CN=54.206.62.10" \
    -addext "subjectAltName=IP:54.206.62.10"

echo "========================================="
echo "SSL Certificate created successfully!"
echo "========================================="
echo ""
echo "Certificate files:"
echo "  - ssl/fullchain.pem (certificate)"
echo "  - ssl/privkey.pem (private key)"
echo ""
echo "⚠️  WARNING: This is a self-signed certificate"
echo "Browsers will show security warnings that users must accept."
echo ""
echo "Next steps:"
echo "1. Update docker-compose.yml to use these certificates"
echo "2. Run: docker-compose up -d"
echo "3. Access: https://54.206.62.10"
echo "4. Accept browser security warning"
