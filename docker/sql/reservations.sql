SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

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
INSERT INTO `reservations` (`id`, `user_id`, `service_id`, `barber_id`, `reservation_date`, `created_at`, `note`, `status`, `guest_name`, `guest_email`, `guest_phone`) VALUES
                                                                                                                                                                            (1,	4,	1,	1,	'2026-01-22 09:00:00',	'2026-01-21 20:04:04',	'',	'completed',	NULL,	NULL,	NULL),
                                                                                                                                                                            (2,	4,	2,	1,	'2026-01-22 10:30:00',	'2026-01-21 20:05:09',	'',	'completed',	NULL,	NULL,	NULL),
                                                                                                                                                                            (3,	4,	3,	1,	'2026-01-23 12:00:00',	'2026-01-21 20:05:58',	'',	'pending',	NULL,	NULL,	NULL),
                                                                                                                                                                            (4,	5,	2,	2,	'2026-01-22 11:30:00',	'2026-01-21 20:07:42',	'',	'completed',	NULL,	NULL,	NULL),
                                                                                                                                                                            (5,	5,	3,	2,	'2026-01-22 12:00:00',	'2026-01-21 20:08:29',	'',	'completed',	NULL,	NULL,	NULL),
                                                                                                                                                                            (6,	5,	1,	1,	'2026-01-23 09:00:00',	'2026-01-21 20:10:57',	'',	'cancelled',	NULL,	NULL,	NULL);