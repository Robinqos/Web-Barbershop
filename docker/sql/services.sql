SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
                            `id` int(11) NOT NULL AUTO_INCREMENT,
                            `title` varchar(100) NOT NULL,
                            `price` int(11) NOT NULL,
                            `duration` int(11) NOT NULL,
                            `description` varchar(500) DEFAULT NULL,
                            PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_uca1400_ai_ci;

INSERT INTO `services` (`id`, `title`, `price`, `duration`, `description`) VALUES
                                                                               (1,	'Pánsky strih',	15,	60,	'Profesionálne strihanie vlasov podľa vášho vlastného štýlu.'),
                                                                               (2,	'Úprava brady',	10,	30,	'Tvarovanie a úprava brady podľa a vášho vlastného štýlu.'),
                                                                               (3,	'Kompletná úprava',	20,	60,	'Pánsky strih + úprava brady v jednom. Komplexná pánska úprava s dôrazom na detail a precíznosť.'),
                                                                               (4,	'Junior strih',	10,	30,	'Moderné trendy účesy pre mládež. Pomôžeme vybrať štýl, ktorý sa páči vám aj vášmu teenagerovi.');