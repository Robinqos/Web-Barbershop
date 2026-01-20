SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `galleries`;
CREATE TABLE `galleries` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `barber_id` int(11) NULL,
                           `photo_path` varchar(255) NOT NULL,
                           `services` varchar(255) DEFAULT NULL,
                           `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                           `is_active` tinyint(1) NOT NULL DEFAULT 1,
                           PRIMARY KEY (`id`),
                           KEY `barber_id` (`barber_id`),
                           KEY `is_active` (`is_active`),
                           CONSTRAINT `galleries_ibfk_1` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;