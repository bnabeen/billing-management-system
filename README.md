# Kirana Store - Business Management System (BMS)

A comprehensive Point of Sale (POS) and inventory management system designed for small retail stores (Kirana stores).

## Features

- **Dashboard**: Overview of daily sales, low stock alerts, and key metrics
- **Quick POS Billing**: Fast checkout with real-time bill preview
- **Product Management**: Add, edit, delete products with stock tracking
- **Sales Management**: Complete sales history and reporting
- **Credit Management (Udharo)**: Track customer credit and payments
- **User Management**: Staff account management with role-based access
- **Responsive Design**: Works on desktop and mobile devices

## Tech Stack

- **Frontend**: HTML5, CSS3, Vanilla JavaScript
- **Backend**: PHP 7.4+
- **Database**: MySQL/MariaDB
- **Server**: Apache (XAMPP)

## Installation

1. **Install XAMPP** (if not already installed)
   - Download from [https://www.apachefriends.org/](https://www.apachefriends.org/)

2. **Clone/Copy Project**
   ```bash
   # Place the project in XAMPP's htdocs folder
   C:\xampp\htdocs\bms\
   ```

3. **Database Setup**
   - Open phpMyAdmin: `http://localhost/phpmyadmin`
   - Import the database schema: `database/bms.sql`
   - Or run the SQL file manually

4. **Configure Database**
   - Edit `config/db.php` with your database credentials
   - Default credentials:
     - Host: localhost
     - Username: root
     - Password: (empty)
     - Database: kirana_bms

5. **Start Apache & MySQL**
   - Open XAMPP Control Panel
   - Start Apache and MySQL services

6. **Access Application**
   - Navigate to: `http://localhost/bms/public/index.php`
   - Default login:
     - Username: `admin`
     - Password: `admin123`

## Project Structure

```
/bms/
│
├── /public/                 # Public-facing files
│   ├── index.php           # Login page
│   ├── dashboard.php       # Main dashboard
│   ├── products.php        # Product management
│   ├── sales.php           # Sales/POS
│   ├── udharo.php          # Credit management
│   └── users.php           # User management
│
├── /assets/                # Static files
│   ├── /css/              # Stylesheets
│   ├── /js/               # JavaScript files
│   └── /img/              # Images
│
├── /includes/              # Reusable components
│   ├── header.php
│   ├── navbar.php
│   ├── footer.php
│   └── auth.php
│
├── /config/               # Configuration
│   └── db.php
│
├── /controllers/          # Business logic
│   ├── productController.php
│   ├── salesController.php
│   ├── udharoController.php
│   └── authController.php
│
├── /models/               # Database models
│   ├── Product.php
│   ├── Sales.php
│   ├── Udharo.php
│   └── User.php
│
└── /database/             # Database schema
    └── bms.sql
```

## Usage

### Dashboard
- View today's sales, low stock alerts, and total inventory
- Quick access to common actions
- Quick POS billing section for fast checkout

### Product Management
- Add new products with name, price, stock, and category
- Edit existing products
- Delete products
- Search and filter products

### Sales/POS
- Create new bills
- Add multiple items
- Calculate totals automatically
- Print receipts

### Credit Management (Udharo)
- Track customer credit
- Record payments
- View outstanding balances

### User Management
- Add staff accounts
- Manage roles (admin/staff)
- Delete users

## Security Notes

- Change default admin password after first login
- Use strong passwords for all accounts
- Keep database credentials secure
- Regular backups recommended

## License

This project is open-source and available for personal and commercial use.

## Support

For issues or questions, please create an issue in the repository.
