
-- MealConnect Database Schema
-- Run this in phpMyAdmin after creating the database: mealconnect

CREATE DATABASE IF NOT EXISTS mealconnect;

USE mealconnect;
-- Admin table
CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');
-- Default password: password (change after first login!)

-- Users table
CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  phone VARCHAR(20),
  address TEXT,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Categories table
CREATE TABLE categories (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  image VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO categories (name, image) VALUES
('Burgers', 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=100&h=100&fit=crop'),
('Pizza', 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=100&h=100&fit=crop'),
('Fried Chicken', 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=100&h=100&fit=crop'),
('Wraps & Rolls', 'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=100&h=100&fit=crop'),
('Fries & Sides', 'https://images.unsplash.com/photo-1630384060421-cb20d0e0649d?w=100&h=100&fit=crop'),
('Drinks', 'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=100&h=100&fit=crop');

-- Foods table
CREATE TABLE foods (
  id INT AUTO_INCREMENT PRIMARY KEY,
  category_id INT NOT NULL,
  name VARCHAR(150) NOT NULL,
  description TEXT,
  price DECIMAL(10,2) NOT NULL,
  image VARCHAR(255),
  is_featured TINYINT(1) DEFAULT 0,
  is_available TINYINT(1) DEFAULT 1,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
);

INSERT INTO foods (category_id, name, description, price, image, is_featured) VALUES
(1,'Classic Smash Burger','Double smashed beef patty, cheddar, pickles, special sauce on brioche bun',650,'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=400&h=300&fit=crop',1),
(1,'BBQ Bacon Stacker','Triple patty, crispy bacon, BBQ sauce, caramelized onions',850,'https://images.unsplash.com/photo-1553979459-d2229ba7433b?w=400&h=300&fit=crop',1),
(1,'Spicy Jalapeño Burger','Beef patty, jalapeños, pepper jack cheese, chipotle mayo',700,'https://images.unsplash.com/photo-1594212699903-ec8a3eca50f5?w=400&h=300&fit=crop',0),
(1,'Mushroom Swiss Burger','Sautéed mushrooms, Swiss cheese, garlic aioli, arugula',720,'https://images.unsplash.com/photo-1571091718767-18b5b1457add?w=400&h=300&fit=crop',0),
(2,'Margherita Supreme','San Marzano tomato, fresh mozzarella, basil, olive oil drizzle',900,'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=400&h=300&fit=crop',1),
(2,'Pepperoni Feast','Double pepperoni, mozzarella, oregano on hand-stretched dough',1100,'https://images.unsplash.com/photo-1628840042765-356cda07504e?w=400&h=300&fit=crop',1),
(2,'BBQ Chicken Pizza','Grilled chicken, BBQ sauce, red onion, bell peppers, cheddar',1050,'https://images.unsplash.com/photo-1513104890138-7c749659a591?w=400&h=300&fit=crop',0),
(3,'Crispy Fried Chicken','8-piece golden fried chicken with original seasoning blend',1200,'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=400&h=300&fit=crop',1),
(3,'Nashville Hot Tenders','Extra spicy fried tenders with honey drizzle and pickles',650,'https://images.unsplash.com/photo-1562967914-608f82629710?w=400&h=300&fit=crop',0),
(3,'Chicken Popcorn Box','Bite-sized crispy chicken pieces, 250g with dipping sauce',480,'https://images.unsplash.com/photo-1585325701956-60dd9c8399b6?w=400&h=300&fit=crop',0),
(4,'Zinger Wrap','Crispy zinger fillet, coleslaw, jalapeños, chipotle sauce in flour tortilla',520,'https://images.unsplash.com/photo-1552332386-f8dd00dc2f85?w=400&h=300&fit=crop',0),
(4,'Grilled Chicken Shawarma','Marinated grilled chicken, garlic sauce, pickles, fries in lavash',480,'https://images.unsplash.com/photo-1561651188-d207bbec4ec3?w=400&h=300&fit=crop',0),
(5,'Loaded Cheese Fries','Golden fries, nacho cheese sauce, jalapeños, sour cream',380,'https://images.unsplash.com/photo-1630384060421-cb20d0e0649d?w=400&h=300&fit=crop',0),
(5,'Onion Rings Basket','Beer-battered onion rings, crispy, served with ranch dip',280,'https://images.unsplash.com/photo-1639024471283-03518883512d?w=400&h=300&fit=crop',0),
(6,'Classic Milkshake','Thick vanilla, chocolate, or strawberry milkshake, 500ml',320,'https://images.unsplash.com/photo-1544145945-f90425340c7e?w=400&h=300&fit=crop',0),
(6,'Fresh Lemonade','Hand-squeezed lemonade with mint, served chilled 400ml',180,'https://images.unsplash.com/photo-1523677011781-c91d1bbe2f9?w=400&h=300&fit=crop',0);

-- Orders table
CREATE TABLE orders (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  total_amount DECIMAL(10,2) NOT NULL,
  delivery_fee DECIMAL(10,2) DEFAULT 99.00,
  address TEXT NOT NULL,
  phone VARCHAR(20) NOT NULL,
  notes TEXT,
  status ENUM('Ordered','On Delivery','Delivered','Cancelled') DEFAULT 'Ordered',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Order items table
CREATE TABLE order_items (
  id INT AUTO_INCREMENT PRIMARY KEY,
  order_id INT NOT NULL,
  food_id INT NOT NULL,
  quantity INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
  FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE
);

-- Cart table
CREATE TABLE cart (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  food_id INT NOT NULL,
  quantity INT NOT NULL DEFAULT 1,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (food_id) REFERENCES foods(id) ON DELETE CASCADE,
  UNIQUE KEY unique_cart_item (user_id, food_id)
);

-- Contact messages table
CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL,
  subject VARCHAR(200),
  message TEXT NOT NULL,
  is_read TINYINT(1) DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Special offers table (extra section)
CREATE TABLE special_offers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(150) NOT NULL,
  description TEXT,
  discount_percent INT,
  image VARCHAR(255),
  is_active TINYINT(1) DEFAULT 1,
  expires_at DATE,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO special_offers (title, description, discount_percent, image, expires_at) VALUES
('Weekend Feast Deal', 'Get any 2 burgers + 2 fries + 2 drinks at a special price every weekend', 20, 'https://images.unsplash.com/photo-1568901346375-23c9450c58cd?w=600&h=300&fit=crop', DATE_ADD(NOW(), INTERVAL 30 DAY)),
('Pizza Family Bundle', 'Order any large pizza and get free garlic bread and a 1.5L drink', 15, 'https://images.unsplash.com/photo-1565299624946-b28f40a0ae38?w=600&h=300&fit=crop', DATE_ADD(NOW(), INTERVAL 15 DAY)),
('First Order Discount', 'New customers get 10% off their very first order with code WELCOME10', 10, 'https://images.unsplash.com/photo-1626645738196-c2a7c87a8f58?w=600&h=300&fit=crop', DATE_ADD(NOW(), INTERVAL 60 DAY));

-- Reservations table (extra section)
CREATE TABLE reservations (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100),
  phone VARCHAR(20) NOT NULL,
  date DATE NOT NULL,
  time TIME NOT NULL,
  guests INT NOT NULL,
  notes TEXT,
  status ENUM('Pending','Confirmed','Cancelled') DEFAULT 'Pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
