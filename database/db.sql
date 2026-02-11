-- Lost & Found Nexus (Capstone) - Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS lost_found_nexus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lost_found_nexus;

-- Users table
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') DEFAULT 'user',
  phone VARCHAR(20) NULL,
  profile_image VARCHAR(255) NULL,
  reset_token VARCHAR(64) NULL,
  reset_expires DATETIME NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Items table
CREATE TABLE items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type ENUM('lost','found') NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(50) NULL,
  location VARCHAR(200) NOT NULL,
  date_lost_found DATE NOT NULL,
  image VARCHAR(255) NULL,
  status ENUM('pending','active','resolved') DEFAULT 'pending',
  user_id INT NOT NULL,
  approved_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Claims table
CREATE TABLE claims (
  id INT PRIMARY KEY AUTO_INCREMENT,
  item_id INT NOT NULL,
  user_id INT NOT NULL,
  claim_details TEXT NOT NULL,
  proof_docs TEXT NULL,
  status ENUM('pending','approved','rejected') DEFAULT 'pending',
  admin_notes TEXT NULL,
  resolved_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uniq_claim (item_id, user_id)
) ENGINE=InnoDB;

-- Admin actions audit
CREATE TABLE admin_actions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  admin_id INT NOT NULL,
  action_type VARCHAR(50) NOT NULL,
  target_id INT NOT NULL,
  details TEXT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Indexes
CREATE INDEX idx_items_status ON items(status);
CREATE INDEX idx_items_type ON items(type);
CREATE INDEX idx_items_user ON items(user_id);
CREATE INDEX idx_claims_status ON claims(status);

-- Default admin (password: Admin123!)
-- IMPORTANT: This hash is for Admin123! using PHP password_hash(PASSWORD_DEFAULT)
INSERT INTO users (name, email, password, role)
VALUES ('Admin User', 'admin@nexus.com', '$2y$10$wQZ3lC55NUEP5sJ8r7AQVe4bPrz1GdDMT6oLa8G1rFVa7xTWhjS8m', 'admin');


-- If upgrading an existing database, run:
-- ALTER TABLE users ADD COLUMN profile_image VARCHAR(255) NULL AFTER phone;
