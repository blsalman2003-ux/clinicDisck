-- ============================================================
--  ClinicDesk — Database Schema
--  Database: clinicdesk_db
--  Charset:  utf8mb4
--  Run this file once to set up the full schema.
-- ============================================================

CREATE DATABASE IF NOT EXISTS clinicdesk_db
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE clinicdesk_db;

-- ─── 1. users ────────────────────────────────────────────────
-- Parent table: must be created before doctors, appointments.
CREATE TABLE IF NOT EXISTS users (
    id         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(120)  NOT NULL,
    email      VARCHAR(180)  NOT NULL UNIQUE,
    password   VARCHAR(255)  NOT NULL,
    role       ENUM('admin','doctor','patient') NOT NULL DEFAULT 'patient',
    phone      VARCHAR(20)   DEFAULT NULL,
    avatar     VARCHAR(255)  DEFAULT NULL,
    is_active  TINYINT(1)    NOT NULL DEFAULT 1,
    first_login TINYINT(1)   NOT NULL DEFAULT 1,   -- challenge feature
    created_at TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Seed: admin account
-- ⚠️ بعد تشغيل هذا الملف، افتح setup.php في المتصفح لتعيين كلمة السر
-- Password: Admin@1234 (يتم ضبطها عبر setup.php)
INSERT INTO users (name, email, password, role, is_active, first_login)
VALUES (
    'Admin',
    'admin@clinic.local',
    '$2y$10$placeholder_run_setup_php_to_fix',
    'admin',
    1,
    0
) ON DUPLICATE KEY UPDATE name = 'Admin';

-- ─── 2. specializations ──────────────────────────────────────
CREATE TABLE IF NOT EXISTS specializations (
    id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO specializations (name) VALUES
    ('General Practice'),
    ('Cardiology'),
    ('Dermatology'),
    ('Pediatrics'),
    ('Orthopedics'),
    ('Neurology'),
    ('Ophthalmology'),
    ('ENT'),
    ('Psychiatry');

-- ─── 3. doctors ──────────────────────────────────────────────
-- Links a user (role=doctor) to doctor-specific data.
-- available_days: comma-separated e.g. "Sun,Mon,Tue,Wed,Thu"
CREATE TABLE IF NOT EXISTS doctors (
    id                INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id           INT UNSIGNED NOT NULL UNIQUE,
    specialization_id INT UNSIGNED NOT NULL,
    bio               TEXT         DEFAULT NULL,
    consultation_fee  DECIMAL(8,2) NOT NULL DEFAULT 0.00,
    available_days    VARCHAR(50)  NOT NULL DEFAULT 'Sun,Mon,Tue,Wed,Thu',
    photo             VARCHAR(255) DEFAULT NULL,
    FOREIGN KEY (user_id)           REFERENCES users(id)           ON DELETE CASCADE,
    FOREIGN KEY (specialization_id) REFERENCES specializations(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 4. appointments ─────────────────────────────────────────
-- UNIQUE KEY prevents double-booking at the database level.
CREATE TABLE IF NOT EXISTS appointments (
    id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    patient_id   INT UNSIGNED NOT NULL,
    doctor_id    INT UNSIGNED NOT NULL,
    appt_date    DATE         NOT NULL,
    appt_time    TIME         NOT NULL,
    status       ENUM('pending','confirmed','completed','cancelled')
                              NOT NULL DEFAULT 'pending',
    reason       VARCHAR(255) DEFAULT NULL,
    doctor_notes TEXT         DEFAULT NULL,
    -- Challenge feature columns:
    cancelled_by          INT UNSIGNED DEFAULT NULL,
    cancellation_reason   TEXT         DEFAULT NULL,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,

    UNIQUE KEY no_double_booking (doctor_id, appt_date, appt_time),
    FOREIGN KEY (patient_id) REFERENCES users(id)    ON DELETE CASCADE,
    FOREIGN KEY (doctor_id)  REFERENCES doctors(id)  ON DELETE CASCADE,
    FOREIGN KEY (cancelled_by) REFERENCES users(id)  ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 5. prescriptions ────────────────────────────────────────
-- One prescription per appointment (UNIQUE on appointment_id).
CREATE TABLE IF NOT EXISTS prescriptions (
    id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id INT UNSIGNED NOT NULL UNIQUE,
    diagnosis      TEXT         NOT NULL,
    medications    TEXT         NOT NULL,
    notes          TEXT         DEFAULT NULL,
    file_path      VARCHAR(255) DEFAULT NULL,   -- optional PDF upload
    created_at     TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id) REFERENCES appointments(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── 6. appointment_logs (challenge feature) ─────────────────
-- Audit trail: every status change writes a row here.
CREATE TABLE IF NOT EXISTS appointment_logs (
    id                  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    appointment_id      INT UNSIGNED NOT NULL,
    changed_by_user_id  INT UNSIGNED DEFAULT NULL,
    old_status          VARCHAR(20)  DEFAULT NULL,
    new_status          VARCHAR(20)  NOT NULL,
    note                TEXT         DEFAULT NULL,
    changed_at          TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (appointment_id)     REFERENCES appointments(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by_user_id) REFERENCES users(id)        ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─── Summary of relationships ────────────────────────────────
-- users        → doctors        : 1-to-1   (via user_id UNIQUE)
-- specializations → doctors     : 1-to-many
-- users(patient)  → appointments: 1-to-many (via patient_id)
-- doctors         → appointments: 1-to-many (via doctor_id)
-- appointments    → prescriptions: 1-to-1  (via appointment_id UNIQUE)
-- appointments    → appointment_logs: 1-to-many
