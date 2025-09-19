-- Create database
CREATE DATABASE IF NOT EXISTS seedbank_db;
USE seedbank_db;

-- Create seeds table
CREATE TABLE IF NOT EXISTS seeds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    variety VARCHAR(100) NOT NULL,
    category VARCHAR(50) NOT NULL,
    donor_name VARCHAR(100) NOT NULL,
    donor_email VARCHAR(100) NOT NULL,
    quantity_in_stock INT DEFAULT 0,
    planting_season VARCHAR(50),
    description TEXT,
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create seed_transactions table
CREATE TABLE IF NOT EXISTS seed_transactions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    seed_id INT NOT NULL,
    transaction_type ENUM('IN', 'OUT') NOT NULL,
    quantity INT NOT NULL,
    member_name VARCHAR(100) NOT NULL,
    notes TEXT,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (seed_id) REFERENCES seeds(id) ON DELETE CASCADE
);

-- Insert sample data
INSERT INTO seeds (name, variety, category, donor_name, donor_email, quantity_in_stock, planting_season, description) VALUES
('Tomato', 'Cherokee Purple', 'Vegetables', 'John Smith', 'john@email.com', 50, 'Spring', 'Heritage variety with excellent flavor'),
('Sunflower', 'Giant Russian', 'Flowers', 'Mary Johnson', 'mary@email.com', 25, 'Summer', 'Large decorative sunflowers'),
('Lettuce', 'Black Seeded Simpson', 'Vegetables', 'Robert Brown', 'robert@email.com', 100, 'Spring/Fall', 'Fast growing leaf lettuce'),
('Marigold', 'French Dwarf', 'Flowers', 'Sarah Wilson', 'sarah@email.com', 75, 'Spring', 'Compact flowering plant');

-- Insert sample transactions
INSERT INTO seed_transactions (seed_id, transaction_type, quantity, member_name, notes) VALUES
(1, 'OUT', 10, 'Alice Green', 'For home garden'),
(2, 'OUT', 5, 'Bob Miller', 'School garden project'),
(3, 'IN', 20, 'Carol Davis', 'Fresh harvest donation'),
(4, 'OUT', 15, 'David Lee', 'Community flower bed');