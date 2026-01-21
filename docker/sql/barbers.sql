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
                           `photo_path` varchar(255) DEFAULT NULL,
                           `is_active` tinyint(1) NOT NULL DEFAULT 1,
                           `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                           PRIMARY KEY (`id`),
                           KEY `user_id` (`user_id`),
                           CONSTRAINT `barbers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
INSERT INTO `barbers` (`id`, `user_id`, `bio`, `photo_path`, `is_active`, `created_at`) VALUES
                                                                                            (1,	2,	'Nadšený mladý barber s moderným pohľadom na tradičné remeslo. Svojou kreativitou a presnosťou pretvára každú návštevu na zážitok.',	'/uploads/barbers/barber_20260121_185147_1945dcf0.png',	1,	'2026-01-21 18:51:47'),
                                                                                            (2,	3,	'Šarmantný majster nožníc s hereckým šľachtom. Každý strih je pre neho divadelnou rolou, kde dokáže premeniť vzhľad a sebavedenie klienta.',	'/uploads/barbers/barber_20260121_185239_1ad44436.jpg',	1,	'2026-01-21 18:52:39');