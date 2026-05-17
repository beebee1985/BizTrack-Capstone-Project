# BizTrack — Small Business Operations & Sales Management System

## Project Overview

BizTrack is a web-based business management system for small and medium-sized businesses. It centralizes daily operations such as recording sales, managing customers, tracking expenses, and generating basic reports to improve efficiency, accuracy, and visibility into business performance.

## Motivation

Many small businesses still rely on notebooks or spreadsheets to track sales and expenses. This leads to errors, poor record keeping, and a lack of actionable insight. BizTrack addresses these issues by digitizing core business processes into a single, easy-to-use platform.

## Screenshots
<img width="1919" height="949" alt="image" src="https://github.com/user-attachments/assets/7aa00efe-e698-4a84-adab-252ff65201af" />
<img width="1919" height="917" alt="image" src="https://github.com/user-attachments/assets/5215924d-5b94-40c0-9249-1b2e643c0fa7" />
<img width="1919" height="944" alt="image" src="https://github.com/user-attachments/assets/d6dc82dd-4a0d-420d-86e5-8576113c5d90" />
<img width="1919" height="931" alt="image" src="https://github.com/user-attachments/assets/afe634be-abab-48b6-a07b-590ffac1497a" />
<img width="1919" height="931" alt="image" src="https://github.com/user-attachments/assets/10ea1424-e701-4b47-911c-8308050868ff" />
<img width="1919" height="950" alt="image" src="https://github.com/user-attachments/assets/b86a7468-2b75-400c-b05a-d3497eff6f8d" />
<img width="1914" height="931" alt="image" src="https://github.com/user-attachments/assets/7c1f3e01-8370-4824-95e0-60a53fb342e7" />

## Key Features

**Business Owner Features:**

- User registration and secure login
- Dashboard with key metrics (sales, expenses, profit)
- Record and manage sales transactions
- Manage product/service listings
- Manage customer records
- Track business expenses
- View profit and loss summaries
- Generate basic business reports

**Admin / System Features:**

- Role-based access (business owner, admin)
- CRUD operations for products, customers, sales, and expenses
- Data validation and error handling
- Secure authentication and authorization
- Responsive web interface

**Technical Features:**

- Backend API built using PHP
- MySQL database for persistent data storage
- Frontend developed using React (RESTful communication with backend)
- Version control with Git & GitHub

## Technology Stack

- Backend: PHP
- Database: MySQL
- Frontend: React
- API: RESTful endpoints
- Version control: Git & GitHub
- Development tools: VS Code, Postman

## Architecture (high-level)

- Client (React) communicates with Backend (PHP) via RESTful API
- Backend performs business logic and persists data to MySQL
- Authentication and authorization handled by the backend with role checks

## Getting Started

See [SETUP.md](SETUP.md) for detailed installation and configuration instructions.

### Quick Start

**Backend:**

```bash
cd backend
composer install
cp .env.example .env
# Edit .env with your database credentials
mysql -u root -p < database/schema.sql
php -S localhost:8000
```

**Frontend:**

```bash
cd frontend
npm install
npm start
```

Visit `http://localhost:3000` and login with:

- Email: admin@biztrack.com
- Password: password

**Important:** Change the default admin password after first login!

## Project Structure

```
BizTrack-Capstone-Project/
├── backend/
│   ├── api/              # RESTful API endpoints
│   ├── config/           # Database and CORS configuration
│   ├── database/         # SQL schema
│   ├── models/           # Data models (User, Product, Customer, Sale, Expense)
│   ├── utils/            # Auth, Response, Validator utilities
│   ├── .env.example      # Environment configuration template
│   ├── .htaccess         # Apache configuration
│   ├── composer.json     # PHP dependencies
│   └── index.php         # Main router
├── frontend/
│   ├── public/           # Static files
│   ├── src/
│   │   ├── components/   # React components (Layout, PrivateRoute)
│   │   ├── pages/        # Page components (Dashboard, Products, etc.)
│   │   ├── services/     # API service layer
│   │   ├── App.js        # Main app with routing
│   │   ├── index.js      # Entry point
│   │   └── index.css     # Global styles
│   └── package.json      # NPM dependencies
├── README.md
└── SETUP.md              # Detailed setup instructions
```

## API Endpoints

All endpoints except `/api/auth/login` and `/api/auth/register` require JWT authentication.

**Authentication:**

- POST `/api/auth/login` - User login
- POST `/api/auth/register` - User registration
- GET `/api/auth/me` - Get current user

**Products:**

- GET/POST `/api/products` - List/Create products
- GET/PUT/DELETE `/api/products/{id}` - Get/Update/Delete product

**Customers:**

- GET/POST `/api/customers` - List/Create customers
- GET/PUT/DELETE `/api/customers/{id}` - Get/Update/Delete customer

**Sales:**

- GET/POST `/api/sales` - List/Create sales
- GET/PUT/DELETE `/api/sales/{id}` - Get/Update/Delete sale

**Expenses:**

- GET/POST `/api/expenses` - List/Create expenses
- GET/PUT/DELETE `/api/expenses/{id}` - Get/Update/Delete expense

**Dashboard:**

- GET `/api/dashboard` - Get business statistics and analytics

## Contributing

- Fork the repository and open a pull request with clear description of changes.
- Add tests and documentation for new features where appropriate.

## Features Implemented

✅ **Authentication System**

- JWT-based authentication with secure token storage
- User registration and login
- Password hashing with bcrypt
- Protected routes and API endpoints

✅ **Backend API**

- Complete RESTful API with CRUD operations
- Input validation and error handling
- CORS support for frontend integration
- Database models for all entities

✅ **Frontend Application**

- React-based single-page application
- Dashboard with sales analytics and charts
- Product management with inventory tracking
- Customer relationship management
- Sales transaction recording with line items
- Expense tracking by category
- Responsive design with intuitive UI

✅ **Database**

- Normalized schema with proper relationships
- Foreign key constraints
- Indexes for performance
- Default admin account

## Future Enhancements

- Advanced reporting and export features (PDF, Excel)
- Multi-user role-based access control
- Inventory low-stock alerts
- Invoice generation and printing
- Email notifications
- Multi-currency support
- Mobile app version

## Contact

For questions or collaboration, open an issue or contact the project owner via the repository on GitHub.

---

_Capstone Project Proposal: BizTrack — Small Business Operations & Sales Management System_
