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
                                                                                                                    (1,	'admin',	'admin@gmail.com',	'$2y$10$wFWmc.UlFShF63Yc4wsSeuoTXRNeRQr4H0GHq8lBsn1NMbW9h2h2O',	'+421918165642',	2,	'2025-12-01 20:00:00',	'2026-01-21 19:54:10'),
                                                                                                                    (2,	'Róbert Cedzo',	'cedzorobo@gmail.com',	'$2y$10$jKqnMu9BtN1lyKeOSufZMe7B7n3SoXFjRpTE2ZKhyb3ui1o3rLUUe',	'+421918165642',	1,	'2026-01-21 18:51:47',	NULL),
                                                                                                                    (3,	'Johnny Depp',	'janko@gmail.com',	'$2y$10$vw2z/vLD2/sbSI7uEpglGOANjZwjtLgznL0WfhwvNUqZ8/hqRRlA6',	'0908243068',	1,	'2026-01-21 18:52:39',	NULL),
                                                                                                                    (4,	'Malicka Rakovanova',	'rkvnv7@gmail.com',	'$2y$10$IOJysop1SPAvGiWIjwjaC.ZCK/6uJHUyZw67w0l9xQj4sKsaemgxa',	'+421949657156',	0,	'2026-01-21 18:53:42',	'2026-01-21 19:53:58'),
                                                                                                                    (5,	'Miroslav Cedzo',	'm.cedzo008@gmail.com',	'$2y$10$dH8D8x/D8lDd6oFGmiNbBuAyTWSYbbE5k5.k4jkLeVJrdl7K09dYa',	'+421918538248',	0,	'2026-01-21 18:55:00',	NULL);