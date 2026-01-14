SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
                         `id` int(11) NOT NULL AUTO_INCREMENT,
                         `fullname` varchar(100) NOT NULL,
                         `email` varchar(200) NOT NULL,
                         `password` varchar(255) NOT NULL,
                         `phone` varchar(13) DEFAULT NULL,
                         `permissions` int(1) NOT NULL,
                         `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                         `last_login` datetime DEFAULT NULL,
                         PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `permissions`, `created_at`, `last_login`) VALUES
                                                                                                                    (1,	'admin',	'admin@gmail.com',	'admin',	'+421918165642',	2,	'2025-12-01 20:00:00',	'2025-12-01 20:00:00'),
                                                                                                                    (2,	'Matka Rakovanova',	'rkvnv7@gmail.com',	'12345',	'+421900000000',	0,	'2025-12-01 19:02:17',	NULL);