# Exchange Platform with Escrow Functionality

A fully functional exchange platform where users can trade any type of asset, including money, crypto, and physical items with secure escrow functionality.

## Core Features

### User System
- Registration and login
- KYC verification
- User profile management

### Asset Management
- List assets for exchange
- Admin approval workflow
- Asset categorization

### Exchange System
- Matching system for trades
- Condition-based deal completion
- Secure escrow mechanism

### Transaction Management
- Secure fund/asset holding
- Verification process
- Release mechanism

### Fee System
- 10% fee on monetary transactions
- Configurable fee for non-monetary exchanges

## Admin Panel

- User Management
- Asset Control
- Transaction Control
- Delivery & Address Management
- Fee Settings

## Tech Stack

- Backend: PHP (Laravel)
- Frontend: React.js
- Database: MySQL
- Payment Integration: Stripe
- Authentication: Laravel Sanctum
- Permissions: Spatie Laravel-Permission

## Installation

```bash
# Clone the repository
git clone [repository-url]

# Install PHP dependencies
composer install

# Install NPM dependencies
npm install

# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate

# Run database migrations
php artisan migrate

# Seed the database
php artisan db:seed

# Compile assets
npm run dev
```

## Security

This platform implements high-level security measures for:
- User authentication
- Crypto transactions
- Escrow mechanism
- Payment processing

## License

MIT