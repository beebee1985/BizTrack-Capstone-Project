# BizTrack Setup Guide

## Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Composer
- Node.js 14+ and npm
- Apache/Nginx web server

## Backend Setup

### 1. Install PHP Dependencies

```bash
cd backend
composer install
```

### 2. Configure Environment

Copy the example environment file:

```bash
cp .env.example .env
```

Edit `.env` and configure your database credentials:

```
DB_HOST=localhost
DB_NAME=biztrack
DB_USER=your_db_username
DB_PASS=your_db_password

JWT_SECRET=your_secret_key_here
JWT_ISSUER=http://localhost
JWT_AUDIENCE=http://localhost

API_BASE_URL=http://localhost:8000
```

Generate a secure JWT secret:

```bash
php -r "echo bin2hex(random_bytes(32));"
```

### 3. Create Database

Create a new MySQL database:

```sql
CREATE DATABASE biztrack CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Import the schema:

```bash
mysql -u your_username -p biztrack < database/schema.sql
```

### 4. Start PHP Development Server

```bash
php -S localhost:8000
```

The API will be available at `http://localhost:8000`

### Default Admin Account

- **Email:** admin@biztrack.com
- **Password:** admin123

**Important:** Change this password immediately after first login!

## Frontend Setup

### 1. Install Dependencies

```bash
cd frontend
npm install
```

### 2. Configure API Endpoint

If your backend runs on a different port, update the API base URL in `frontend/src/services/api.js`:

```javascript
const api = axios.create({
  baseURL: "http://localhost:8000", // Change this if needed
});
```

### 3. Start Development Server

```bash
npm start
```

The frontend will be available at `http://localhost:3000`

## Production Deployment

### Backend (Apache)

1. Point your virtual host document root to the `backend` directory
2. Ensure `.htaccess` is enabled and mod_rewrite is active
3. Set proper file permissions:

```bash
chmod -R 755 backend
chmod 644 backend/.env
```

4. Update `.env` with production values

### Frontend

Build the production version:

```bash
cd frontend
npm run build
```

The `build` folder contains the optimized production build. Deploy it to your web server.

## Verification

1. Visit the frontend URL in your browser
2. Register a new account or login with the default admin account
3. Create a test product, customer, and sale
4. Verify the dashboard shows the correct statistics

## Troubleshooting

### CORS Errors

If you encounter CORS errors, ensure:

- The backend `config/cors.php` has the correct frontend URL
- Your `.htaccess` file is properly configured

### Database Connection Errors

- Verify MySQL is running
- Check database credentials in `.env`
- Ensure the database user has proper permissions

### Authentication Issues

- Clear browser localStorage
- Verify JWT_SECRET is set in `.env`
- Check that the system clock is correctly set

## API Documentation

### Authentication Endpoints

- `POST /api/auth/login` - Login
- `POST /api/auth/register` - Register new user
- `GET /api/auth/me` - Get current user info

### Protected Endpoints (Require JWT Token)

#### Products

- `GET /api/products` - List all products
- `GET /api/products/{id}` - Get product by ID
- `POST /api/products` - Create product
- `PUT /api/products/{id}` - Update product
- `DELETE /api/products/{id}` - Delete product

#### Customers

- `GET /api/customers` - List all customers
- `GET /api/customers/{id}` - Get customer by ID
- `POST /api/customers` - Create customer
- `PUT /api/customers/{id}` - Update customer
- `DELETE /api/customers/{id}` - Delete customer

#### Sales

- `GET /api/sales` - List all sales
- `GET /api/sales/{id}` - Get sale by ID with items
- `POST /api/sales` - Create sale
- `PUT /api/sales/{id}` - Update sale status
- `DELETE /api/sales/{id}` - Delete sale

#### Expenses

- `GET /api/expenses` - List all expenses
- `GET /api/expenses/{id}` - Get expense by ID
- `POST /api/expenses` - Create expense
- `PUT /api/expenses/{id}` - Update expense
- `DELETE /api/expenses/{id}` - Delete expense

#### Dashboard

- `GET /api/dashboard` - Get dashboard statistics

All protected endpoints require the `Authorization: Bearer {token}` header.
