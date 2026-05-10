

CREATE DATABASE IF NOT EXISTS vuln_web_app;
USE vuln_web_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, password) VALUES
('admin', 'admin123'),
('user1', 'password1'),
('user2', 'password2');

INSERT INTO comments (comment) VALUES
('This is a test comment'),
('Another sample comment'),
('Welcome to the application');


CREATE DATABASE IF NOT EXISTS secure_web_app;
USE secure_web_app;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS login_attempts (
    username VARCHAR(50) PRIMARY KEY,
    attempts INT NOT NULL DEFAULT 0,
    last_attempt INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comment TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO users (username, password) VALUES
('admin', '$2y$10$kAmXpa52NpIwccOPUggE1ufVTh7BeD2yjnK/EFd4020bww5Hj0Vim'),
('user1', '$2y$10$YuVI1lyiVWvsYJfFPn.R5OX3ufbym4Ra3rYqUc.KZEqJJ5Jb.qr4u'),
('user2', '$2y$10$defVtg03vEXfSGeLqsVLG..1BAXBaKAmXkh1INQYiHZL2j.l03GtK');

INSERT INTO comments (comment) VALUES
('This is a test comment'),
('Another sample comment'),
('Welcome to the application');

