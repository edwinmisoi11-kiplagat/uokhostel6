CREATE DATABASE IF NOT EXISTS hostel6_portal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE hostel6_portal;

CREATE TABLE IF NOT EXISTS applications (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    full_name VARCHAR(120) NOT NULL,
    registration_number VARCHAR(50) NOT NULL,
    room_number SMALLINT UNSIGNED NOT NULL,
    year_of_study TINYINT UNSIGNED NOT NULL,
    phone_number VARCHAR(20) NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') NOT NULL DEFAULT 'Pending',
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_registration_number (registration_number),
    KEY idx_status (status),
    CONSTRAINT chk_room_number CHECK (room_number BETWEEN 1 AND 300),
    CONSTRAINT chk_year_of_study CHECK (year_of_study BETWEEN 1 AND 4)
);
