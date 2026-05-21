# AstroGuide

AstroGuide is a PHP and MySQL based astronomy learning platform. It helps users explore planets, space missions, galaxies, exoplanets, astronomy terms, telescopes, and space events through a structured web interface.

## Features

- Planet catalog with detailed pages and planet type comparisons
- Space mission pages with mission detail views
- Astronomy glossary and learning modules
- Galaxy, exoplanet, telescope, and event sections
- User authentication with login, register, logout, and favorites
- Admin panel for managing users, planets, missions, events, galaxies, exoplanets, and suggestions
- NASA APOD integration using a configurable API key
- MySQL database schema and upgrade migrations included

## Tech Stack

- PHP
- MySQL
- HTML
- CSS
- PDO

## Project Structure

```text
config/       Application and database configuration
database/     SQL schema and migration files
includes/     Shared layout and authentication helpers
public/       Main application pages and assets
```

## Setup

1. Clone the repository.
2. Create a MySQL database named `astroguide`.
3. Import `database/astroguide.sql`.
4. Update database settings in `config/db.php` if needed.
5. Set a NASA API key as an environment variable named `NASA_API_KEY`, or use the default demo key.
6. Serve the project with a local PHP server or a local stack such as XAMPP.

Example local URL:

```text
http://localhost/astroguide_project/public
```

## Notes

This project was built as a full-stack web application for learning and exploring astronomy topics. It includes both visitor-facing pages and admin management screens.
