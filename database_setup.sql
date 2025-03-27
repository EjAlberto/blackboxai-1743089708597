-- Database creation
CREATE DATABASE IF NOT EXISTS student_management_system;
USE student_management_system;

-- Tables creation
CREATE TABLE IF NOT EXISTS strands (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS sections (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    grade_level INT NOT NULL CHECK (grade_level IN (11, 12))
);

CREATE TABLE IF NOT EXISTS school_years (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year_start YEAR NOT NULL,
    year_end YEAR NOT NULL
);

CREATE TABLE IF NOT EXISTS students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    grade_level INT NOT NULL CHECK (grade_level IN (11, 12)),
    strand_id INT,
    section_id INT,
    school_year_id INT,
    birthdate DATE,
    address TEXT,
    contact_number VARCHAR(20),
    email VARCHAR(100),
    FOREIGN KEY (strand_id) REFERENCES strands(id),
    FOREIGN KEY (section_id) REFERENCES sections(id),
    FOREIGN KEY (school_year_id) REFERENCES school_years(id)
);

CREATE TABLE IF NOT EXISTS documents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    upload_date DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Sample data insertion
INSERT INTO strands (name, description) VALUES 
('STEM', 'Science, Technology, Engineering, and Mathematics'),
('HUMSS', 'Humanities and Social Sciences'),
('ABM', 'Accountancy, Business, and Management'),
('GAS', 'General Academic Strand'),
('TVL', 'Technical-Vocational-Livelihood');

INSERT INTO sections (name, grade_level) VALUES 
('Newton', 11),
('Einstein', 11),
('Aristotle', 12),
('Plato', 12);

INSERT INTO school_years (year_start, year_end) VALUES 
(2023, 2024),
(2024, 2025);