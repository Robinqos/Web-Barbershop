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
                                `reservation_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
                                `created_at` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE current_timestamp(),
                                `note` varchar(500) DEFAULT NULL,
                                `status` enum('pending','cancelled','completed') NOT NULL DEFAULT 'pending',
                                `guest_name` varchar(100) DEFAULT NULL,
                                `guest_email` varchar(200) DEFAULT NULL,
                                `guest_phone` varchar(15) DEFAULT NULL,
                                PRIMARY KEY (`id`),
                                KEY `user_id` (`user_id`),
                                KEY `service_id` (`service_id`),
                                CONSTRAINT `reservations_ibfk_3` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
                                CONSTRAINT `reservations_ibfk_4` FOREIGN KEY (`service_id`) REFERENCES `services` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;