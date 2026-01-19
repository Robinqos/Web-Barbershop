SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
                           `id` int(11) NOT NULL AUTO_INCREMENT,
                           `user_id` int(11) NOT NULL,
                           `barber_id` int(11) NOT NULL,
                           `reservation_id` int(11) NULL,
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