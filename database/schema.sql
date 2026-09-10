CREATE DATABASE IF NOT EXISTS nepal_disaster_archive
CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE nepal_disaster_archive;

CREATE TABLE IF NOT EXISTS admins (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(120) NOT NULL,
 email VARCHAR(190) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 role ENUM('ADMIN','EDITOR') NOT NULL DEFAULT 'EDITOR',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS categories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL UNIQUE,
 slug VARCHAR(120) NOT NULL UNIQUE,
 description TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS stories (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(255) NOT NULL,
 slug VARCHAR(255) NOT NULL UNIQUE,
 category_id INT UNSIGNED NULL,
 event_date DATE NULL,
 year_label VARCHAR(30),
 location VARCHAR(255),
 district VARCHAR(120),
 province VARCHAR(120),
 latitude DECIMAL(10,7) NULL,
 longitude DECIMAL(10,7) NULL,
 image_path VARCHAR(255),
 summary TEXT,
 content LONGTEXT NOT NULL,
 deaths VARCHAR(100),
 injuries VARCHAR(100),
 magnitude VARCHAR(100),
 impact TEXT,
 sources TEXT,
 featured TINYINT(1) NOT NULL DEFAULT 0,
 status ENUM('DRAFT','REVIEW','PUBLISHED') NOT NULL DEFAULT 'DRAFT',
 reviewed_at DATETIME NULL,
 reviewed_by INT UNSIGNED NULL,
 created_by INT UNSIGNED NULL,
 updated_by INT UNSIGNED NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
 FOREIGN KEY(category_id) REFERENCES categories(id) ON DELETE SET NULL,
 FOREIGN KEY(created_by) REFERENCES admins(id) ON DELETE SET NULL,
 FOREIGN KEY(updated_by) REFERENCES admins(id) ON DELETE SET NULL,
 INDEX(status), INDEX(event_date), INDEX(category_id)
);

CREATE TABLE IF NOT EXISTS story_revisions (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 story_id INT UNSIGNED NOT NULL,
 title VARCHAR(255) NOT NULL,
 content LONGTEXT NOT NULL,
 saved_by INT UNSIGNED NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(story_id) REFERENCES stories(id) ON DELETE CASCADE,
 FOREIGN KEY(saved_by) REFERENCES admins(id) ON DELETE SET NULL,
 INDEX(story_id)
);

CREATE TABLE IF NOT EXISTS story_images (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 story_id INT UNSIGNED NOT NULL,
 image_path VARCHAR(255) NOT NULL,
 caption VARCHAR(255),
 credit VARCHAR(255),
 sort_order INT NOT NULL DEFAULT 0,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(story_id) REFERENCES stories(id) ON DELETE CASCADE,
 INDEX(story_id)
);

CREATE TABLE IF NOT EXISTS story_sources (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 story_id INT UNSIGNED NOT NULL,
 title VARCHAR(255) NOT NULL,
 publisher VARCHAR(255),
 url VARCHAR(500),
 published_on DATE NULL,
 verified TINYINT(1) NOT NULL DEFAULT 0,
 FOREIGN KEY(story_id) REFERENCES stories(id) ON DELETE CASCADE,
 INDEX(story_id)
);

CREATE TABLE IF NOT EXISTS correction_reports (
 id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 story_id INT UNSIGNED NULL,
 name VARCHAR(120),
 email VARCHAR(190),
 message TEXT NOT NULL,
 status ENUM('OPEN','RESOLVED') NOT NULL DEFAULT 'OPEN',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(story_id) REFERENCES stories(id) ON DELETE SET NULL,
 INDEX(status)
);

CREATE TABLE IF NOT EXISTS analytics_events (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 story_id INT UNSIGNED NULL,
 event_type VARCHAR(40) NOT NULL,
 search_term VARCHAR(255),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(story_id) REFERENCES stories(id) ON DELETE SET NULL,
 INDEX(event_type), INDEX(story_id), INDEX(created_at)
);

CREATE TABLE IF NOT EXISTS admin_activity (
 id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
 admin_id INT UNSIGNED NULL,
 action VARCHAR(80) NOT NULL,
 details VARCHAR(500),
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(admin_id) REFERENCES admins(id) ON DELETE SET NULL,
 INDEX(created_at)
);

INSERT IGNORE INTO categories(name,slug,description) VALUES
('Earthquake','earthquake','Earthquake events and seismic history.'),
('Flood','flood','River floods, flash floods and monsoon flooding.'),
('Landslide','landslide','Rainfall, earthquake and human-triggered slope failures.'),
('Avalanche','avalanche','Snowstorms and avalanche disasters.'),
('GLOF','glof','Glacial Lake Outburst Floods.'),
('Lightning','lightning','Lightning and severe electrical storms.'),
('Storm','storm','Thunderstorms, hailstorms and windstorms.'),
('Tornado','tornado','Tornado events.'),
('Drought','drought','Meteorological, agricultural and hydrological drought.'),
('Heat Wave','heat-wave','Extreme heat events.'),
('Cold Wave','cold-wave','Extreme cold and winter hazards.'),
('Wildfire','wildfire','Forest and vegetation fires.'),
('Other','other','Other natural hazards.');

INSERT IGNORE INTO stories
(title,slug,category_id,event_date,year_label,location,summary,content,magnitude,deaths,status,featured)
VALUES
(
 '2015 Gorkha Earthquake',
 '2015-gorkha-earthquake',
 (SELECT id FROM categories WHERE slug='earthquake'),
 '2015-04-25','2015','Gorkha / Kathmandu Valley',
 'The Mw 7.8 earthquake of 25 April 2015 became one of the defining disasters of modern Nepal.',
 'This starter article is intentionally concise. Editors should expand it using verified government, scientific and historical sources. The earthquake caused widespread structural damage, landslides and major heritage losses across central Nepal. A major aftershock occurred on 12 May 2015.',
 'Mw 7.8','about 9,000','PUBLISHED',1
);
    