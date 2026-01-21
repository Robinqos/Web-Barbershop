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
                           PRIMARY KEY (`id`),
                           KEY `barber_id` (`barber_id`),
                           CONSTRAINT `galleries_ibfk_1` FOREIGN KEY (`barber_id`) REFERENCES `barbers` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;
INSERT INTO `galleries` (`id`, `barber_id`, `photo_path`, `services`, `created_at`) VALUES
                                                                                        (1,	2,	'/uploads/gallery/gallery_6971213764ff8_94e493c5.png',	'Mid fade to V design',	'2026-01-21 18:55:51'),
                                                                                        (2,	2,	'/uploads/gallery/gallery_6971217730886_b54d6717.png',	'High fade buzz cut',	'2026-01-21 18:56:55'),
                                                                                        (3,	2,	'/uploads/gallery/gallery_6971218fee376_6e5b2612.png',	'Mid fade-messy french top',	'2026-01-21 18:57:19'),
                                                                                        (4,	2,	'/uploads/gallery/gallery_697121bc30765_76b89904.png',	'Mid fade design',	'2026-01-21 18:58:04'),
                                                                                        (5,	1,	'/uploads/gallery/gallery_6971223bc12a7_8c246b8f.jpg',	'Low taper fade-long top',	'2026-01-21 19:00:11'),
                                                                                        (6,	1,	'/uploads/gallery/gallery_6971224fc6e37_0bf83057.jpg',	'Low taper fade-buzz cut',	'2026-01-21 19:00:31'),
                                                                                        (7,	1,	'/uploads/gallery/gallery_6971225c04c3a_00744395.jpg',	'Low taper fade-messy top',	'2026-01-21 19:00:44'),
                                                                                        (8,	1,	'/uploads/gallery/gallery_6971226d2834f_c87bd259.jpg',	'Mid fade-top to side',	'2026-01-21 19:01:01');