CREATE DATABASE IF NOT EXISTS resume;
use resume;

-- Create tables
CREATE TABLE about (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    title VARCHAR(200),
    bio TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    institution VARCHAR(100),
    degree VARCHAR(200),
    field VARCHAR(200),
    start_date DATE,
    end_date DATE,
    description TEXT
);

CREATE TABLE experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    position VARCHAR(100),
    company VARCHAR(200),
    start_date DATE,
    end_date DATE,
    responsibilities TEXT,
    technologies VARCHAR(500)
);

CREATE TABLE contact_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(50),
    value TEXT,
    icon VARCHAR(100),
    link VARCHAR(255),
    is_active BOOLEAN DEFAULT TRUE
);