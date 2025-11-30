#!/bin/bash

echo "=== PAMS Deployment Script ==="
echo ""

# Colors
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Check if .env exists
if [ ! -f .env ]; then
    echo -e "${RED}Error: .env file not found!${NC}"
    exit 1
fi

echo -e "${YELLOW}Step 1: Updating environment configuration...${NC}"
# Ensure production settings
sed -i 's/APP_ENV=.*/APP_ENV=production/' .env
sed -i 's/APP_DEBUG=.*/APP_DEBUG=false/' .env
sed -i 's|APP_URL=.*|APP_URL=https://pams.produkmastah.com|' .env
sed -i 's/SESSION_SECURE_COOKIE=.*/SESSION_SECURE_COOKIE=true/' .env
echo -e "${GREEN}✓ Environment configured${NC}"

echo ""
echo -e "${YELLOW}Step 2: Installing dependencies...${NC}"
composer install --no-dev --optimize-autoloader
echo -e "${GREEN}✓ Dependencies installed${NC}"

echo ""
echo -e "${YELLOW}Step 3: Clearing all caches...${NC}"
php artisan optimize:clear
echo -e "${GREEN}✓ Caches cleared${NC}"

echo ""
echo -e "${YELLOW}Step 4: Caching configuration...${NC}"
php artisan config:cache
php artisan route:cache
php artisan view:cache
echo -e "${GREEN}✓ Configuration cached${NC}"

echo ""
echo -e "${YELLOW}Step 5: Running migrations...${NC}"
php artisan migrate --force
echo -e "${GREEN}✓ Migrations completed${NC}"

echo ""
echo -e "${YELLOW}Step 6: Regenerating permissions...${NC}"
php artisan shield:generate --all --no-interaction
echo -e "${GREEN}✓ Permissions regenerated${NC}"

echo ""
echo -e "${YELLOW}Step 7: Setting up super admin...${NC}"
php artisan shield:super-admin --user=2 --no-interaction
echo -e "${GREEN}✓ Super admin configured${NC}"

echo ""
echo -e "${YELLOW}Step 8: Publishing Livewire assets...${NC}"
php artisan livewire:publish --assets --force
echo -e "${GREEN}✓ Livewire assets published${NC}"

echo ""
echo -e "${YELLOW}Step 9: Optimizing database queries...${NC}"
php artisan optimize
echo -e "${GREEN}✓ Application optimized${NC}"

echo ""
echo -e "${YELLOW}Step 10: Setting permissions...${NC}"
chmod -R 755 storage bootstrap/cache
chmod -R 775 storage/logs
echo -e "${GREEN}✓ Permissions set${NC}"

echo ""
echo -e "${GREEN}=== Deployment Complete! ===${NC}"
echo ""
echo "Next steps:"
echo "1. Clear browser cookies for pams.produkmastah.com"
echo "2. Test login at https://pams.produkmastah.com/admin"
echo "3. Check logs: tail -f storage/logs/laravel.log"
echo ""