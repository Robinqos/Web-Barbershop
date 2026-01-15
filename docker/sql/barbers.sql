SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `barbers`;
CREATE TABLE `barbers` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `user_id` int(11) NOT NULL,
                           `bio` text DEFAULT NULL,
                           `photo_url` varchar(255) DEFAULT NULL,
                           `is_active` tinyint(1) NOT NULL DEFAULT 1,
                           `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                           PRIMARY KEY (`id`),
                           KEY `user_id` (`user_id`),
                           CONSTRAINT `barbers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
INSERT INTO `barbers` (`id`, `user_id`, `bio`, `photo_url`, `is_active`, `created_at`) VALUES
                                                                                           (1,	30,	'Nadšený mladý barber s moderným pohľadom na tradičné remeslo. Svojou kreativitou a presnosťou pretvára každú návštevu na zážitok.\n',	'',	1,	'2026-01-14 20:03:11'),
                                                                                           (2,	31,	'Šarmantný majster nožníc s hereckým šľachtom. Každý strih je pre neho divadelnou rolou, kde dokáže premeniť vzhľad a sebavedenie klienta.',	'',	1,	'2026-01-15 20:17:13');