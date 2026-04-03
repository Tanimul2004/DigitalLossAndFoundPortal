CREATE DATABASE IF NOT EXISTS lost_found_nexus CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE lost_found_nexus;
DROP TABLE IF EXISTS admin_actions;
DROP TABLE IF EXISTS claims;
DROP TABLE IF EXISTS items;
DROP TABLE IF EXISTS users;
CREATE TABLE users (
  id INT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  phone VARCHAR(20) NULL,
  profile_image VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
CREATE TABLE items (
  id INT PRIMARY KEY AUTO_INCREMENT,
  type ENUM('lost','found') NOT NULL,
  title VARCHAR(200) NOT NULL,
  description TEXT NOT NULL,
  category VARCHAR(50) NULL,
  brand VARCHAR(100) NULL,
  color VARCHAR(50) NULL,
  serial_number VARCHAR(120) NULL,
  unique_marks TEXT NULL,
  location VARCHAR(200) NOT NULL,
  latitude DECIMAL(10,7) NULL,
  longitude DECIMAL(10,7) NULL,
  date_lost_found DATE NOT NULL,
  image VARCHAR(255) NULL,
  status ENUM('pending','active','resolved','rejected') NOT NULL DEFAULT 'pending',
  user_id INT NOT NULL,
  approved_at TIMESTAMP NULL,
  resolved_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_items_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
CREATE TABLE claims (
  id INT PRIMARY KEY AUTO_INCREMENT,
  item_id INT NOT NULL,
  user_id INT NOT NULL,
  claim_details TEXT NOT NULL,
  proof_docs TEXT NULL,
  claimed_brand VARCHAR(100) NULL,
  claimed_color VARCHAR(50) NULL,
  claimed_serial VARCHAR(120) NULL,
  claimed_location VARCHAR(200) NULL,
  claimed_date DATE NULL,
  identifying_marks TEXT NULL,
  match_score DECIMAL(5,2) NOT NULL DEFAULT 0,
  match_summary TEXT NULL,
  status ENUM('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  admin_notes TEXT NULL,
  resolved_at TIMESTAMP NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_claims_item FOREIGN KEY (item_id) REFERENCES items(id) ON DELETE CASCADE,
  CONSTRAINT fk_claims_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  UNIQUE KEY uq_claim_item_user (item_id, user_id)
);
CREATE TABLE admin_actions (
  id INT PRIMARY KEY AUTO_INCREMENT,
  admin_id INT NOT NULL,
  action_type VARCHAR(50) NOT NULL,
  target_id INT NOT NULL,
  details TEXT NULL,
  timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_admin_actions_user FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);
INSERT INTO users (name,email,password,role,phone) VALUES
('Admin User','admin@nexus.com','$2y$12$HzCrkxXVAd6w.GpgtcF5G.nfOHJUPMLX//uYqlpJGpLmeSKPXQ1Vi','admin','01700000000'),
('Demo User','user@example.com','$2y$12$Z6KIkBGqY9VnKT.QkMIBpOFTOL2SKOfJCDhtXfdfZxaoAZOPQFI6y','user','01800000000');
INSERT INTO items (type,title,description,category,brand,color,serial_number,unique_marks,location,latitude,longitude,date_lost_found,image,status,user_id,approved_at) VALUES
('lost','Black Wallet','Leather wallet with student ID and some cash.','bags','Wildcraft','Black','WAL-1001','Small scratch inside flap','Main Library',23.7298000,90.3991000,CURDATE() - INTERVAL 2 DAY,NULL,'active',2,NOW()),
('lost','Blue Backpack','Backpack containing notebooks and charger.','bags','Skybag','Blue',NULL,'Sticker on the front pocket','ICT Building',23.7288000,90.3983000,CURDATE() - INTERVAL 3 DAY,NULL,'active',2,NOW()),
('found','Silver Keychain','A keychain with 3 keys and a blue strap.','keys',NULL,'Silver',NULL,'Blue fabric strap','Cafeteria',23.7305000,90.4010000,CURDATE() - INTERVAL 1 DAY,NULL,'active',2,NOW()),
('found','Student ID Card','University ID card found near the gate.','documents',NULL,NULL,'ID-7788','Photo ID with red lanyard','Main Gate',23.7321000,90.4033000,CURDATE() - INTERVAL 1 DAY,NULL,'active',2,NOW());
