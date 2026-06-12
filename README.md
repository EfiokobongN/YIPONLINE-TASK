# YIPShop — E-Commerce Application

A full-stack e-commerce web application built with Laravel 12 as a case study submission for YIPONLINE.

---

## About

YIPShop is a simple but complete online store where customers can browse products, add them to cart, and place orders. Admins have a separate dashboard to manage everything behind the scenes.

I built this from scratch using Laravel 12, keeping the code clean and the structure easy to follow. The bonus requirement was Smarty template integration, which I implemented alongside Laravel's default Blade engine.

---

## What it does

**For customers:**
- Browse and search products by category
- View product details with multiple images
- Add products to cart and adjust quantities
- Checkout with saved shipping details
- View order history

**For admins:**
- Manage products with multiple image uploads
- Manage categories with images
- View and update order statuses
- Monitor revenue and order stats on the dashboard

---

## Tech Stack

- **Framework** — Laravel 12
- **Language** — PHP 8.2
- **Database** — MySQL
- **Templating** — Blade (+ Smarty integration as bonus)
- **Auth** — Custom session-based authentication
- **Storage** — Laravel filesystem (local/public disk)
- **Server** — Apache with mod_rewrite

---

## Getting Started

### Requirements
- PHP 8.2+
- Composer
- MySQL
- Apache or Nginx

### Installation

```bash
# Clone the repo
git clone https://github.com/YOUR_USERNAME/yiponline-ecommerce.git
cd yiponline-ecommerce

# Install dependencies
composer install

# Set up environment
cp .env.example .env
php artisan key:generate

# Configure your database in .env
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password

# Run migrations and seed demo data
php artisan migrate --seed

# Link storage
php artisan storage:link

# Start the server
php artisan serve
```

Visit `http://localhost:8000`

---

## Demo Accounts

| Role | Email | Password |
|---|---|---|
| Admin | admin@yiponline.com | password |

---




---

## Smarty Integration

As a bonus, I integrated the Smarty template engine alongside Blade. The `SmartyService` class in `app/Services/SmartyService.php` bridges Laravel with Smarty, allowing both template engines to work within the same application.

```php
// Example usage in a controller
$smarty = new SmartyService();
return $smarty->response('products/listing', [
    'products' => $products,
]);
```

---

## Deployment

The application is deployed on Go54 shared hosting and accessible at:

🌐 **https://yip.williams04group.com**

CI/CD is set up via GitHub Actions using FTP deployment — every push to the `main` branch automatically deploys to the server.

---

## Notes

- Payment is currently in mock mode — no real charges are made
- In production, this would integrate with Paystack or Flutterwave
- VAT is calculated at 7.5% (Nigerian standard)
- Free shipping on orders over ₦50,000

---

## Author

Built by **EFIOKOBONG NELSON** for the YIPONLINE job application case study.

> *"This project demonstrates my ability to build a complete, production-ready Laravel application from scratch — covering authentication, e-commerce logic, admin management, and deployment."*
