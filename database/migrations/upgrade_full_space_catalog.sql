USE astroguide;

ALTER TABLE users MODIFY role ENUM('traveler', 'contributor', 'editor') NOT NULL DEFAULT 'traveler';
ALTER TABLE planets ADD COLUMN IF NOT EXISTS live_distance_note VARCHAR(255) NULL;
ALTER TABLE missions ADD COLUMN IF NOT EXISTS image_url VARCHAR(500) NULL;
ALTER TABLE missions MODIFY image_url VARCHAR(500) NULL;

CREATE TABLE IF NOT EXISTS exoplanets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    system_name VARCHAR(120) NOT NULL,
    distance_light_years DECIMAL(10,2) NULL,
    discovery_year INT NULL,
    discovery_method VARCHAR(120) NULL,
    planet_type VARCHAR(100) NULL,
    habitability_note VARCHAR(255) NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS galaxies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    galaxy_type VARCHAR(100) NOT NULL,
    distance_light_years DECIMAL(14,1) NULL,
    constellation VARCHAR(120) NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(500) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS content_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    suggested_by_name VARCHAR(100) NULL,
    category ENUM('gezegen', 'uydu', 'cüce gezegen', 'ötegezegen', 'galaksi', 'görev', 'gök olayı', 'diğer') NOT NULL DEFAULT 'diğer',
    title VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    source_url VARCHAR(500) NULL,
    status ENUM('pending', 'approved', 'rejected') NOT NULL DEFAULT 'pending',
    admin_note VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    reviewed_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS exoplanet_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    exoplanet_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_exoplanet_favorite (user_id, exoplanet_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (exoplanet_id) REFERENCES exoplanets(id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS galaxy_favorites (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    galaxy_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_galaxy_favorite (user_id, galaxy_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (galaxy_id) REFERENCES galaxies(id) ON DELETE CASCADE
);
