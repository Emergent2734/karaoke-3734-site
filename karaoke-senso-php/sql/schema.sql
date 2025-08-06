-- Karaoke Sensō Database Schema for MySQL 5.7
-- Create database
CREATE DATABASE IF NOT EXISTS karaoke_senso CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE karaoke_senso;

-- Users table (admin authentication)
CREATE TABLE users (
    id VARCHAR(36) PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    is_admin BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_email (email)
);

-- Events table
CREATE TABLE events (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    municipality VARCHAR(255) NOT NULL,
    venue TEXT NOT NULL,
    date DATETIME NOT NULL,
    max_participants INT DEFAULT 50,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_date (date),
    INDEX idx_municipality (municipality)
);

-- Registrations table
CREATE TABLE registrations (
    id VARCHAR(36) PRIMARY KEY,
    full_name VARCHAR(255) NOT NULL,
    age INT NOT NULL,
    municipality VARCHAR(255) NOT NULL,
    sector VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    email VARCHAR(255) NOT NULL,
    event_id VARCHAR(36) NOT NULL,
    payment_status ENUM('pendiente', 'pagado') DEFAULT 'pendiente',
    video_url TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE,
    INDEX idx_event (event_id),
    INDEX idx_email (email),
    INDEX idx_municipality (municipality),
    INDEX idx_sector (sector),
    INDEX idx_payment_status (payment_status)
);

-- Brands table (sponsors)
CREATE TABLE brands (
    id VARCHAR(36) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    logo_url TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user
-- Password: Senso2025* (hashed with PHP password_hash)
INSERT INTO users (id, email, password_hash, is_admin) VALUES (
    UUID(),
    'admin@karaokesenso.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- This will be replaced with proper hash
    TRUE
);

-- Insert sample brands (sponsors)
INSERT INTO brands (id, name, logo_url) VALUES
(UUID(), 'PVA', 'https://via.placeholder.com/150x60/D4AF37/000000?text=PVA'),
(UUID(), 'Impactos Digitales', 'https://via.placeholder.com/150x60/D4AF37/000000?text=IMPACTOS'),
(UUID(), 'Club de Leones Querétaro', 'https://via.placeholder.com/150x60/D4AF37/000000?text=LEONES'),
(UUID(), 'Radio UAQ 89.5 FM', 'https://via.placeholder.com/150x60/D4AF37/000000?text=RADIO+UAQ'),
(UUID(), 'CIJ', 'https://via.placeholder.com/150x60/D4AF37/000000?text=CIJ');