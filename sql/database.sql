-- =====================================================
-- ASISTEN AKADEMIK HARIAN - Database Schema
-- =====================================================
-- Created: 2026-01-28
-- Description: Complete database schema for daily academic assistant app
-- =====================================================

-- Create database
CREATE DATABASE IF NOT EXISTS akademik_harian
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE akademik_harian;

-- =====================================================
-- TABLE: users
-- Description: Store user information
-- =====================================================
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    class VARCHAR(50) NOT NULL,
    current_gpa DECIMAL(3,2) DEFAULT 0.00,
    target_gpa DECIMAL(3,2) DEFAULT 4.00,
    streak_days INT DEFAULT 0,
    total_study_minutes INT DEFAULT 0,
    last_activity DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: subjects (Mata Kuliah)
-- Description: Store subject/course information
-- =====================================================
CREATE TABLE IF NOT EXISTS subjects (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    credits INT DEFAULT 3,
    grade VARCHAR(2) DEFAULT NULL,
    grade_value DECIMAL(3,2) DEFAULT NULL,
    numeric_score INT DEFAULT NULL,
    semester INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_subjects (user_id)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: tasks (Tugas)
-- Description: Store tasks with deadlines
-- =====================================================
CREATE TABLE IF NOT EXISTS tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_id INT DEFAULT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT DEFAULT NULL,
    deadline DATETIME NOT NULL,
    priority ENUM('low', 'medium', 'high') DEFAULT 'medium',
    is_completed TINYINT(1) DEFAULT 0,
    completed_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    INDEX idx_user_tasks (user_id),
    INDEX idx_deadline (deadline),
    INDEX idx_completed (is_completed)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: notes (Catatan Cepat)
-- Description: Store quick notes with colors
-- =====================================================
CREATE TABLE IF NOT EXISTS notes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT DEFAULT NULL,
    color VARCHAR(20) DEFAULT 'yellow',
    is_pinned TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_notes (user_id),
    INDEX idx_pinned (is_pinned)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: checklist_items (Daily Checklist)
-- Description: Store daily routines and study goals
-- =====================================================
CREATE TABLE IF NOT EXISTS checklist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    type ENUM('daily', 'goal') DEFAULT 'daily',
    is_completed TINYINT(1) DEFAULT 0,
    check_date DATE DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_checklist (user_id),
    INDEX idx_check_date (check_date),
    INDEX idx_type (type)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: study_sessions (Sesi Belajar)
-- Description: Track study timer sessions
-- =====================================================
CREATE TABLE IF NOT EXISTS study_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    subject_id INT DEFAULT NULL,
    duration_minutes INT NOT NULL,
    session_type ENUM('pomodoro', 'short_break', 'long_break', 'custom') DEFAULT 'pomodoro',
    started_at DATETIME NOT NULL,
    ended_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE SET NULL,
    INDEX idx_user_sessions (user_id),
    INDEX idx_started_at (started_at)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: achievements (Pencapaian)
-- Description: Track user achievements and badges
-- =====================================================
CREATE TABLE IF NOT EXISTS achievements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    category ENUM('streak', 'study_time', 'tasks', 'gpa') NOT NULL,
    name VARCHAR(100) NOT NULL,
    description VARCHAR(255) DEFAULT NULL,
    icon VARCHAR(50) DEFAULT '🏆',
    progress INT DEFAULT 0,
    target INT NOT NULL,
    is_unlocked TINYINT(1) DEFAULT 0,
    unlocked_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_achievements (user_id),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- =====================================================
-- TABLE: motivational_quotes
-- Description: Store motivational quotes
-- =====================================================
CREATE TABLE IF NOT EXISTS motivational_quotes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    quote TEXT NOT NULL,
    category VARCHAR(50) DEFAULT 'general'
) ENGINE=InnoDB;

-- =====================================================
-- INSERT: Default Motivational Quotes
-- =====================================================
INSERT INTO motivational_quotes (quote, category) VALUES
('Jangan menyerah! Perjuanganmu akan membuahkan hasil! 🚀', 'general'),
('Halo, pejuang IPK! Hari ini adalah kesempatan baru! 💪', 'greeting'),
('Yuk dicicil satu per satu! Kamu pasti bisa! ✨', 'task'),
('Terus pertahankan semangatmu! 🔥', 'streak'),
('Setiap langkah kecil membawamu lebih dekat ke tujuan! 🎯', 'general'),
('Konsistensi adalah kunci kesuksesan! 🗝️', 'general'),
('Istirahat sejenak, lalu lanjutkan perjuanganmu! ☕', 'break'),
('Kamu sudah melakukan yang terbaik hari ini! 🌟', 'complete'),
('Satu tugas selesai, selangkah lebih dekat ke impian! 🏆', 'task'),
('Fokus pada prosesnya, hasilnya akan mengikuti! 📚', 'study'),
('Hari ini belajar, besok sukses! 📖', 'study'),
('Tetap semangat! Kamu adalah bintang yang sedang bersinar! ⭐', 'general'),
('Waktu yang kamu investasikan untuk belajar tidak akan sia-sia! ⏰', 'study'),
('Hebat! Kamu sudah lebih baik dari kemarin! 📈', 'progress'),
('Jangan bandingkan dirimu dengan orang lain, bandingkan dengan dirimu kemarin! 💫', 'general');

-- =====================================================
-- INSERT: Default Achievement Templates
-- =====================================================
-- These will be created per user when they register
