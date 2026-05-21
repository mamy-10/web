USE astroguide;

ALTER TABLE planets
    ADD COLUMN radius_km DECIMAL(12,1) NULL AFTER gravity_multiplier,
    ADD COLUMN distance_from_sun_million_km DECIMAL(12,1) NULL AFTER radius_km,
    ADD COLUMN moons INT NULL AFTER distance_from_sun_million_km,
    ADD COLUMN average_temperature_c INT NULL AFTER moons;

UPDATE planets SET radius_km = 2439.7, distance_from_sun_million_km = 57.9, moons = 0, average_temperature_c = 167 WHERE name = 'Merkür';
UPDATE planets SET radius_km = 6051.8, distance_from_sun_million_km = 108.2, moons = 0, average_temperature_c = 464 WHERE name = 'Venüs';
UPDATE planets SET radius_km = 6371.0, distance_from_sun_million_km = 149.6, moons = 1, average_temperature_c = 15 WHERE name = 'Dünya';
UPDATE planets SET radius_km = 3389.5, distance_from_sun_million_km = 227.9, moons = 2, average_temperature_c = -65 WHERE name = 'Mars';
UPDATE planets SET radius_km = 69911.0, distance_from_sun_million_km = 778.5, moons = 95, average_temperature_c = -110 WHERE name = 'Jüpiter';
UPDATE planets SET radius_km = 58232.0, distance_from_sun_million_km = 1434.0, moons = 146, average_temperature_c = -140 WHERE name = 'Satürn';
UPDATE planets SET radius_km = 1737.4, distance_from_sun_million_km = NULL, moons = 0, average_temperature_c = -20 WHERE name = 'Ay';
UPDATE planets SET radius_km = 1188.3, distance_from_sun_million_km = 5900.0, moons = 5, average_temperature_c = -229 WHERE name = 'Plüton';
