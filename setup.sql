-- ═══════════════════════════════════════════════════════════════════
-- setup.sql — Manual alternative to running setup/init.php
-- Run as: mysql -u root -p < setup.sql
-- ═══════════════════════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS oracle_ctf
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE oracle_ctf;

-- Create challenge user (adjust password as needed)
-- CREATE USER IF NOT EXISTS 'oracle_user'@'localhost' IDENTIFIED BY 'oracle_pass123';
-- GRANT ALL PRIVILEGES ON oracle_ctf.* TO 'oracle_user'@'localhost';
-- FLUSH PRIVILEGES;

-- ──────────────────────────────────────────────────────────────────
-- Archivists: login targets for Level 1
-- ──────────────────────────────────────────────────────────────────
DROP TABLE IF EXISTS archivists;
CREATE TABLE archivists (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(60)  NOT NULL,
    password VARCHAR(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO archivists (username, password) VALUES
    ('sanjaya',   'kurukshetra_chronicles_741'),
    ('vyasa',     'mahabharata_sage_9182'),
    ('dhritrash', 'blind_king_pass_3355');

-- ──────────────────────────────────────────────────────────────────
-- kshetra_rakshak: warrior records for Levels 2-4
--
-- ⚠  id=7 is the mystery warrior.
--    Name is stored as '???' so a direct SELECT name WHERE id=7
--    does not reveal the answer. The name is verified in level4.php.
-- ──────────────────────────────────────────────────────────────────
DROP TABLE IF EXISTS kshetra_rakshak;
CREATE TABLE kshetra_rakshak (
    id     INT AUTO_INCREMENT PRIMARY KEY,
    name   VARCHAR(100) NOT NULL,
    side   VARCHAR(50)  NOT NULL,
    father VARCHAR(100) DEFAULT NULL,
    mother VARCHAR(100) DEFAULT NULL,
    fate   TEXT         DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT INTO kshetra_rakshak (name, side, father, mother, fate) VALUES
-- 1
('Bhishma',
 'Kaurava',
 'Shantanu',
 'Ganga',
 'Fell on the tenth day, choosing to lie on a bed of arrows until the war\'s end.'),
-- 2
('Drona',
 'Kaurava',
 'Sage Bharadwaja',
 'Unknown',
 'Slain by Dhrishtadyumna on the fifteenth day after laying down his weapons in grief.'),
-- 3
('Karna',
 'Kaurava',
 'Surya (the Sun God)',
 'Kunti',
 'Slain by Arjuna on the seventeenth day while struggling to free his chariot wheel.'),
-- 4
('Abhimanyu',
 'Pandava',
 'Arjuna',
 'Subhadra',
 'Killed inside the Chakravyuha on the thirteenth day, surrounded and overwhelmed.'),
-- 5
('Ghatotkacha',
 'Pandava',
 'Bhima',
 'Hidimbi',
 'Slain by Karna with the Vasavi Shakti on the fourteenth day at nightfall.'),
-- 6
('Shikhandi',
 'Pandava',
 'Drupada',
 'Prishati',
 'Survived the main war; killed by Ashwatthama on the final night in the camp attack.'),
-- 7 ← MYSTERY WARRIOR (Iravan — name hidden)
('???',
 'Pandava',
 'Arjuna',
 'Ulupi (a Naga princess)',
 'Offered himself to Goddess Kali on the eighth day, then fell in battle the same day.'),
-- 8
('Satyaki',
 'Pandava',
 'Shini',
 'Unknown',
 'One of the few key warriors to survive the entire Kurukshetra war.'),
-- 9
('Kripacharya',
 'Kaurava',
 'Sage Sharadvana',
 'Janapadi',
 'One of the chiranjivis — survived the war due to his immortal nature.');
