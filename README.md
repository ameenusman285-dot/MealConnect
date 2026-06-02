# MealConnect — Online Food Ordering System (PHP + MySQL)

MealConnect is a responsive online food ordering website built with **PHP (MySQLi)**, **MySQL**, **HTML**, **CSS**, **Bootstrap**, and a **Gemini-powered AI chatbot**.

The project includes:
- **Customer frontend**: browse menu, add items to cart, checkout, place orders, view order history, contact the restaurant, and reserve a table.
- **Admin portal**: manage foods, categories, orders, reservations, contact messages, and special offers; also update admin credentials.

---

## Live Pages (Entry Points)

From your project folder (in XAMPP `htdocs`), open these URLs:

### Customer / Frontend
- **Home**: `http://localhost/MealConnect/index.php`
- **Menu**: `http://localhost/MealConnect/menu.php`
  - Supports filters:
    - Category: `menu.php?cat=<category_id>`
    - Search: `menu.php?q=<keyword>`
-- **Cart**: `http://localhost/MealConnect/cart.php`
-- **Checkout**: `http://localhost/MealConnect/checkout.php`
-- **Login**: `http://localhost/MealConnect/login.php`
-- **Register**: `http://localhost/MealConnect/register.php`
-- **Dashboard (Order History)**: `http://localhost/MealConnect/dashboard.php`
-- **Contact Us**: `http://localhost/MealConnect/contact.php`
-- **Reservation**: `http://localhost/MealConnect/reservation.php`

### Admin
- **Admin Login**: `http://localhost/MealConnect/admin/login.php`
- **Admin Dashboard**: `http://localhost/MealConnect/admin/index.php`
- **Manage Foods**: `http://localhost/MealConnect/admin/foods.php`
- **Manage Categories**: `http://localhost/MealConnect/admin/categories.php`
- **Manage Orders**: `http://localhost/MealConnect/admin/orders.php`
- **Manage Reservations**: `http://localhost/MealConnect/admin/reservations.php`
- **Manage Messages**: `http://localhost/MealConnect/admin/messages.php`
- **Manage Special Offers**: `http://localhost/MealConnect/admin/offers.php`
- **Admin Settings**: `http://localhost/MealConnect/admin/settings.php`

---

## Features (What the site does)

### Frontend (Customers)
- **Category browsing & menu search** (Menu page)
- **Featured foods** (Home page)
- **Special offers section** (Home page)
- **Cart system**
  - Add items to cart
  - Update quantity / remove items
  - Automatically calculates **subtotal**, **delivery fee**, and **total**
- **Checkout → Orders**
  - Collects address/phone/notes
  - Creates a row in `orders` and related `order_items`
  - Clears cart after successful order
- **Dashboard**
  - Shows order history and status badges
- **User authentication**
  - Register / Login using password hashing
- **AI Chatbot**
  - Floating chatbot UI on every page
  - Uses `chatbot_api.php` to call Gemini API
- **Contact form**
  - Saves messages into `contact_messages`
- **Table reservation**
  - Saves reservations into `reservations`

### Admin Portal
- **Authentication** with session protection
- **Admin Dashboard metrics**
  - Total orders, delivered revenue, customers count, pending orders
- **Foods CRUD**
  - Add/edit/delete foods
  - Supports image upload OR image URL
  - Flags: `is_featured` and `is_available`
- **Categories CRUD**
  - Add/edit/delete categories
  - Supports image upload OR image URL
- **Orders management**
  - Filter orders by status (Ordered, On Delivery, Delivered, Cancelled)
  - Update order status
- **Reservations management**
  - Update reservation status: Pending / Confirmed / Cancelled
- **Messages management**
  - Delete messages
  - Mark read/unread
- **Special offers management**
  - Add/edit/delete offers
  - Supports discount %, expires date, image upload OR URL, active/inactive
- **Admin settings**
  - Update admin username and optional password

---

## Technologies Used

- **Frontend**: HTML5, CSS (single `css/style.css`), Bootstrap 5, Font Awesome, Google Fonts
- **Backend**: PHP (MySQLi)
- **Database**: MySQL
- **AI**: Gemini API via `chatbot_api.php` using cURL

---

## Project Structure (Key Files)

```text
MealConnect/
├── admin/
│   ├── login.php
│   ├── index.php
│   ├── foods.php
│   ├── categories.php
│   ├── orders.php
│   ├── reservations.php
│   ├── messages.php
│   ├── offers.php
│   └── settings.php
│   └── includes/
│       ├── admin_auth.php
│       ├── admin_header.php
│       └── admin_footer.php
│
├── includes/
│   ├── config.php               # DB config + Gemini API key + site constants
│   ├── db_connect.php          # MySQLi connection + helpers (sanitize, redirect, session checks)
│   ├── header.php              # Frontend navbar + session-aware links + chatbot UI footer
│   └── footer.php
│
├── css/
│   └── style.css
│
├── images/
│   └── uploads/               # Uploaded images for foods/categories/offers
│
├── sql/
│   └── database.sql           # Database schema + seed data
│
├── index.php
├── menu.php
├── cart.php
├── checkout.php
├── dashboard.php
├── login.php
├── register.php
├── contact.php
├── reservation.php
└── chatbot_api.php
```

---

## Database Setup (Required)

1. Start **Apache** and **MySQL** in XAMPP.
2. Open phpMyAdmin: `http://localhost/phpmyadmin/`.
3. Create database `mealconnect` (or let the SQL script create it).
4. Import schema:
   - File: `sql/database.sql`

### What gets created
The script creates these tables:
- `admin`
- `users`
- `categories`
- `foods`
- `orders`
- `order_items`
- `cart`
- `contact_messages`
- `special_offers`
- `reservations`

---

## Configuration (API key + DB)

Edit:
- `includes/config.php`

Important values:
- `DB_HOST`, `DB_USER`, `DB_PASS`, `DB_NAME`
- `GEMINI_API_KEY` (replace placeholder with your key)

---

## Default Admin Credentials

The database seed inserts an admin user:
- **Username**: `admin`
- **Password**: (hashed seed corresponds to the plaintext **`password`**)

Access:
- `http://localhost/MealConnect/admin/login.php`

*(After first login, update credentials on `admin/settings.php`.)*

---

## How Each Page Works (Detailed)

### `index.php` (Home)
- Pulls:
  - Featured foods (`foods.is_featured=1`)
  - Categories (limited)
  - Special offers (`special_offers.is_active=1` and not expired)
- Renders hero, category pills, featured cards, offers, why-choose-us, testimonials, reservation CTA.

### `menu.php` (Menu + Filters)
- Reads:
  - `cat` from `$_GET` (category filter)
  - `q` from `$_GET` (search by name/description)
- Queries foods joined with categories.
- Shows “Add to Cart” button:
  - If logged in → submits to `cart.php`
  - If not logged in → redirects to `login.php`

### `cart.php` (Cart)
- Requires login.
- POST actions:
  - `add`: inserts into `cart` or increments quantity
  - `update`: updates quantity or deletes when qty < 1
  - `remove`: deletes the item row
  - `clear`: removes all cart items
- Computes:
  - subtotal from `price * quantity`
  - delivery fee is `99` when cart has items, otherwise `0`

### `checkout.php` (Place Order)
- Requires login.
- Loads cart items + user details.
- POST creates:
  - `orders` row with address/phone/notes, total & delivery fee
  - `order_items` rows for each cart item
- Clears cart and redirects to `dashboard.php?order_success=1`.

### `dashboard.php` (Customer Order History)
- Requires login.
- Shows:
  - total orders
  - total spent
  - list of orders with status badges
  - each order expands to show item list

### `login.php` / `register.php`
- `register.php`: creates user, stores password using `password_hash()`.
- `login.php`: verifies with `password_verify()`.
- Both redirect to `dashboard.php` after success.

### `contact.php`
- On submit: saves into `contact_messages`.

### `reservation.php`
- On submit: saves into `reservations`.
- Includes reservation policy text and booking form (name/email/phone/date/time/guests/notes).

### `chatbot_api.php` (Gemini backend)
- Receives JSON: `{ "message": "..." }`
- Calls Gemini endpoint using `GEMINI_API_KEY`.
- Returns JSON: `{ "reply": "..." }`

### Admin pages
All admin pages call `admin/includes/admin_auth.php` and require an active admin session.
- **Admin dashboard**: shows KPIs from `orders`, `users`, `contact_messages`, `reservations`.
- **Foods**: full CRUD with image upload support.
- **Categories**: CRUD with image upload support.
- **Orders**: filter by status + update status.
- **Reservations**: update reservation status.
- **Messages**: delete + mark read/unread.
- **Offers**: CRUD with discount %, expiry, and active/inactive.
- **Settings**: update admin username and optional password.

---

## Notes / Setup Tips

- Ensure PHP `mysqli` extension is enabled.
- Ensure folder `images/uploads/` is writable by the web server (for image uploads).
- For local testing, set `SITE_URL` in `includes/config.php` to match your localhost path.


#
