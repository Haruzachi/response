CREATE DATABASE IF NOT EXISTS emergency_system;
USE emergency_system;

-- ------------------------
-- Users table
-- ------------------------
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) NOT NULL,
    profile_image VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ------------------------
-- Emergency calls table
-- ------------------------
CREATE TABLE IF NOT EXISTS emergency_calls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    caller_name VARCHAR(100) NOT NULL,
    incident_type VARCHAR(50) NOT NULL,
    location VARCHAR(255) NOT NULL,
    latitude DECIMAL(10,8) DEFAULT NULL,
    longitude DECIMAL(11,8) DEFAULT NULL,
    severity ENUM('Low','Medium','High','Critical') DEFAULT 'Low',
    status ENUM('Pending','Ongoing','Completed') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    response_time INT(11) DEFAULT NULL,
    expected_time INT(11) DEFAULT NULL
);


-- ------------------------
-- Dispatch assignments table
-- ------------------------
CREATE TABLE IF NOT EXISTS dispatch_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    incident_id INT NOT NULL,
    responder_name VARCHAR(100) NOT NULL,
    dispatch_status ENUM('Assigned','In Progress','Completed') DEFAULT 'Assigned',
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (incident_id) REFERENCES emergency_calls(id) ON DELETE CASCADE
);

-- ------------------------
-- Resources table
-- ------------------------
CREATE TABLE IF NOT EXISTS resources (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_name VARCHAR(100) NOT NULL,
    resource_type ENUM('EMS','Fire','Police') NOT NULL,
    status ENUM('Available','Dispatched') DEFAULT 'Available',
    current_location VARCHAR(100)
);

-- Insert existing resources
INSERT INTO resources (id, unit_name, resource_type, status, current_location) VALUES
(1, 'Unit 1', 'EMS', 'Available', 'Station A'),
(2, 'Unit 2', 'EMS', 'Available', 'Station B'),
(3, 'Unit 3', 'EMS', 'Available', 'Station C'),
(4, 'Unit 4', 'EMS', 'Available', 'Station D'),
(5, 'Unit 5', 'EMS', 'Available', 'Station E'),
(6, 'Unit 1', 'Fire', 'Available', 'Station A'),
(7, 'Unit 2', 'Fire', 'Available', 'Station B'),
(8, 'Unit 3', 'Fire', 'Available', 'Station C'),
(9, 'Unit 4', 'Fire', 'Available', 'Station D'),
(10, 'Unit 5', 'Fire', 'Available', 'Station E'),
(11, 'Unit 1', 'Police', 'Available', 'Station A'),
(12, 'Unit 2', 'Police', 'Available', 'Station B'),
(13, 'Unit 3', 'Police', 'Available', 'Station C'),
(14, 'Unit 4', 'Police', 'Available', 'Station D'),
(15, 'Unit 5', 'Police', 'Available', 'Station E');

-- ------------------------
-- Resource allocations table
-- ------------------------
CREATE TABLE IF NOT EXISTS resource_allocations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    emergency_call_id INT NOT NULL,
    resource_id INT NOT NULL,
    assigned_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (emergency_call_id) REFERENCES emergency_calls(id) ON DELETE CASCADE,
    FOREIGN KEY (resource_id) REFERENCES resources(id) ON DELETE CASCADE
);

-- ------------------------
-- Messages table
-- ------------------------
CREATE TABLE IF NOT EXISTS messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    message TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    department VARCHAR(50) NOT NULL DEFAULT 'General',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- ------------------------
-- Feedback table
-- ------------------------
CREATE TABLE IF NOT EXISTS feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    feedback TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);




ALTER TABLE users 
ADD COLUMN otp_code VARCHAR(6) NULL,
ADD COLUMN otp_expiration DATETIME NULL;
