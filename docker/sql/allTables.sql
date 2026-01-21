
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


DROP TABLE IF EXISTS `galleries`;
CREATE TABLE `galleries` (
                             `id` int(11) NOT NULL AUTO_INCREMENT,
                             `barber_id` int(11) DEFAULT NULL,
                             `photo_path` varchar(255) NOT NULL,
                             `services` varchar(255) DEFAULT NULL,
                             `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                             PRIMARY KEY (`id`),
                             KEY `barber_id` (`barber_id`),
                             CONSTRAINT `galleries_ibfk_1` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


DROP TABLE IF EXISTS `reservations`;
CREATE TABLE `reservations` (
                                `id` int(11) NOT NULL AUTO_INCREMENT,
                                `user_id` int(11) DEFAULT NULL,
                                `service_id` int(11) NOT NULL,
                                `barber_id` int(11) DEFAULT NULL,
                                `reservation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                                `note` varchar(500) DEFAULT NULL,
                                `status` enum('pending','cancelled','completed') NOT NULL DEFAULT 'pending',
                                `guest_name` varchar(100) DEFAULT NULL,
                                `guest_email` varchar(200) DEFAULT NULL,
                                `guest_phone` varchar(15) DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `user_id` (`user_id`),
                                KEY `service_id` (`service_id`),
                                KEY `barber_id` (`barber_id`),
                                CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
                                CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE,
                                CONSTRAINT `reservations_ibfk_5` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `user_id` int(11) NOT NULL,
                           `barber_id` int(11) NOT NULL,
                           `reservation_id` int(11) DEFAULT NULL,
                           `rating` int(11) NOT NULL,
                           `created_at` datetime NOT NULL DEFAULT current_timestamp(),
                           PRIMARY KEY (`id`),
                           UNIQUE KEY `unique_reservation_review` (`reservation_id`),
                           KEY `barber_id` (`barber_id`),
                           KEY `user_id` (`user_id`),
                           KEY `reservation_id` (`reservation_id`),
                           CONSTRAINT `reviews_ibfk_2` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE CASCADE,
                           CONSTRAINT `reviews_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
                           CONSTRAINT `reviews_ibfk_4` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `title` varchar(100) NOT NULL,
                            `price` int(11) NOT NULL,
                            `duration` int(11) NOT NULL,
                            `description` varchar(500) DEFAULT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;


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
                                                                                                                    (1,	'admin',	'admin@gmail.com',	'$2y$10$wFWmc.UlFShF63Yc4wsSeuoTXRNeRQr4H0GHq8lBsn1NMbW9h2h2O',	'+421918165642',	2,	'2025-12-01 20:00:00',	'2026-01-21 20:53:43'),
                                                                                                                    (2,	'Róbert Cedzo',	'cedzorobo@gmail.com',	'$2y$10$jKqnMu9BtN1lyKeOSufZMe7B7n3SoXFjRpTE2ZKhyb3ui1o3rLUUe',	'+421918165642',	1,	'2026-01-21 18:51:47',	'2026-01-21 20:54:17'),
                                                                                                                    (3,	'Johnny Depp',	'janko@gmail.com',	'$2y$10$vw2z/vLD2/sbSI7uEpglGOANjZwjtLgznL0WfhwvNUqZ8/hqRRlA6',	'0908243068',	1,	'2026-01-21 18:52:39',	'2026-01-21 20:10:06'),
                                                                                                                    (4,	'Malicka Rakovanova',	'rkvnv7@gmail.com',	'$2y$10$zgk2HGC1Lvhlfxqt1YzhsuG2wmlwhJ39zc5bE3gDXDE5lkKIDWLRK',	'+421949657156',	0,	'2026-01-21 18:53:42',	'2026-01-21 21:39:39'),
                                                                                                                    (5,	'Miroslav Cedzo',	'm.cedzo008@gmail.com',	'$2y$10$dH8D8x/D8lDd6oFGmiNbBuAyTWSYbbE5k5.k4jkLeVJrdl7K09dYa',	'+421918538248',	0,	'2026-01-21 18:55:00',	'2026-01-21 20:13:28');