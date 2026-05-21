USE astroguide;

DROP TABLE IF EXISTS telescopes;
DROP TABLE IF EXISTS planet_types;
DROP TABLE IF EXISTS astronomy_terms;

CREATE TABLE astronomy_terms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    term VARCHAR(120) NOT NULL,
    category VARCHAR(80) NOT NULL,
    short_definition VARCHAR(255) NOT NULL,
    detailed_definition TEXT NOT NULL,
    example VARCHAR(500) NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE planet_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(120) NOT NULL,
    summary VARCHAR(255) NOT NULL,
    description TEXT NOT NULL,
    composition VARCHAR(255) NOT NULL,
    examples VARCHAR(255) NOT NULL,
    importance VARCHAR(500) NOT NULL,
    display_order INT NOT NULL DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE telescopes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    platform ENUM('Yer tabanlı', 'Uzay tabanlı') NOT NULL,
    agency VARCHAR(150) NOT NULL,
    location_or_orbit VARCHAR(180) NOT NULL,
    wavelength VARCHAR(150) NOT NULL,
    launch_or_first_light_year INT NULL,
    main_goal VARCHAR(255) NOT NULL,
    discoveries VARCHAR(500) NOT NULL,
    description TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);