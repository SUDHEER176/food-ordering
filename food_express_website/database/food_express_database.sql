
CREATE DATABASE IF NOT EXISTS food_express;
USE food_express;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- Food items table
CREATE TABLE IF NOT EXISTS food_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255) NOT NULL,
    price DECIMAL(6,2) NOT NULL,
    category VARCHAR(50)
);

-- Cart table
CREATE TABLE IF NOT EXISTS cart (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    food_id INT,
    quantity INT DEFAULT 1,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (food_id) REFERENCES food_items(id)
);

-- Sample food items
INSERT INTO food_items (name, image, price, category) VALUES
('Margherita Pizza', 'https://source.unsplash.com/200x140/?pizza', 299.00, 'Pizza'),
('Cheeseburger', 'https://source.unsplash.com/200x140/?burger', 199.00, 'Burger'),
('Butter Chicken', 'https://source.unsplash.com/200x140/?indian-food', 349.00, 'Indian'),
('Chocolate Cake', 'https://source.unsplash.com/200x140/?dessert', 149.00, 'Dessert');
--admin page login
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
