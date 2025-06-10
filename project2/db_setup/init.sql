-- Use the database created via environment variables in docker-compose
USE project2_db;

-- Create the users table for authentication
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT TRUE
);

-- Create the comments table for the guestbook
CREATE TABLE comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    author VARCHAR(100) NOT NULL,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Add the default admin user
-- NOTE: In a real app, passwords should be hashed!
INSERT INTO users (username, password, is_admin) VALUES ('admin', 'password', TRUE);