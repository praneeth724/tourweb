-- CeylonEcoTrails Booking Database Setup
-- Run this script to create the database and bookings table

-- Create database (uncomment if needed)
-- CREATE DATABASE IF NOT EXISTS ceylonecotrails;
-- USE ceylonecotrails;

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,

    -- Tour Information
    tour_name VARCHAR(100) NOT NULL,
    tour_price DECIMAL(10, 2) NOT NULL,

    -- Customer Information
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    country VARCHAR(50) NOT NULL,

    -- Booking Details
    number_of_people INT NOT NULL,
    preferred_date DATE NOT NULL,
    special_requirements TEXT,

    -- Payment Information
    payment_method VARCHAR(20) NOT NULL,
    currency VARCHAR(10) NOT NULL DEFAULT 'USD',
    total_amount DECIMAL(10, 2) NOT NULL,

    -- Booking Reference
    booking_reference VARCHAR(20) UNIQUE NOT NULL,

    -- Status and Timestamps
    booking_status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Indexes for better query performance
    INDEX idx_email (email),
    INDEX idx_booking_reference (booking_reference),
    INDEX idx_preferred_date (preferred_date),
    INDEX idx_created_at (created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Optional: Create a table for tracking booking statistics
CREATE TABLE IF NOT EXISTS booking_stats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    total_bookings INT DEFAULT 0,
    total_revenue DECIMAL(15, 2) DEFAULT 0.00,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert initial stats record
INSERT INTO booking_stats (total_bookings, total_revenue)
VALUES (0, 0.00)
ON DUPLICATE KEY UPDATE last_updated = CURRENT_TIMESTAMP;
