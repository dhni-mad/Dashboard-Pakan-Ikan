CREATE DATABASE db_pakan_ikan
    DEFAULT CHARACTER SET = 'utf8mb4';

USE db_pakan_ikan;

CREATE TABLE `status_sistem` (
  `id` INT(11) NOT NULL DEFAULT 1,
  `feed_status` VARCHAR(50) NOT NULL DEFAULT 'Penuh',
  `water_status` VARCHAR(50) NOT NULL DEFAULT 'Jernih',
  `last_update` TIMESTAMP NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
);

INSERT INTO `status_sistem` (id) VALUES (1);


CREATE TABLE `log_pemberian_pakan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `berat_pakan` FLOAT NOT NULL,
  `waktu` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
);

CREATE TABLE `log_kekeruhan_air` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `nilai_kekeruhan` INT(11) NOT NULL,
  `waktu` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
);


CREATE TABLE `log_jarak_pakan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `jarak_cm` INT(11) NOT NULL,
  `waktu` TIMESTAMP NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
);


ALTER TABLE `status_sistem`
  ADD COLUMN `jarak_cm` INT(11) NOT NULL DEFAULT 0 AFTER `water_status`,
  ADD COLUMN `manual_feed_request` TINYINT(1) NOT NULL DEFAULT 0 AFTER `jarak_cm`;


CREATE TABLE `pengaturan` (
  `id` INT(11) NOT NULL DEFAULT 1,
  `batas_pakan_habis` INT(11) NOT NULL DEFAULT 15,
  `batas_air_keruh` INT(11) NOT NULL DEFAULT 700, 
  PRIMARY KEY (`id`)
);

INSERT INTO `pengaturan` (id, batas_pakan_habis, batas_air_keruh) VALUES (1, 15, 700);

CREATE TABLE `jadwal_pakan` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `jam` TIME NOT NULL,
  `berat_pakan` FLOAT NOT NULL DEFAULT 50,
  `aktif` TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
);

INSERT INTO `jadwal_pakan` (jam, berat_pakan, aktif) VALUES 
('07:00:00', 50, 1),
('12:00:00', 50, 1),
('16:00:00', 50, 1);