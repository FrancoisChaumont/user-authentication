SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE DATABASE IF NOT EXISTS `tests_authclass` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `tests_authclass`;

CREATE USER IF NOT EXISTS 'uauthclass'@'localhost' IDENTIFIED BY 'uauthclasspassw';
GRANT SELECT, INSERT, UPDATE, DELETE ON `tests_authclass`.* TO 'uauthclass'@'localhost';

DROP TABLE IF EXISTS `is_users`;
CREATE TABLE `is_users` (
  `id` int(11) NOT NULL,
  `name` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `username` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `email` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `password` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `params` text COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `is_users`;
INSERT INTO `is_users` (`id`, `name`, `username`, `email`, `password`, `block`, `sendEmail`, `registerDate`, `lastvisitDate`, `activation`, `params`) VALUES
(1, 'Tom Cruise', 'TC', 'tc@php.com', '$2y$10$O4syTjtJwp7oW2owfSWBtu2w6WH6kg8P0tuFsPmBTvzWV2opQwdLS', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(2, 'Angelina Jolie', 'AJ', 'aj@php.com', '$2y$10$V51ZslVY9/OjFpVq.Z9uq.N6bEkcFMtywebH5BG0wzaz6COZGFtAW', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(3, 'Megan Fox', 'MF', 'mf@php.com', '$2y$10$fx.227sM.uQz4IiXMq0agu.uJDoVxEjFiBwb9e8C0MAMcAiWDRRO.', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(4, 'Donnie Yen', 'DY', 'dy@php.com', '$2y$10$6pC9qb7rtUMkYdNgV6Bki.UoXiAFJcS77.fDKyyAPwTnbikF3qJBe', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', ''),
(5, 'Gal Gadot', 'GG', 'gg@php.com', '$2y$10$YPlhWy.MNDHZqRjKLvuRZOR7BcfXNNewiZyl0jPSrNrpzN57sz382', 0, 0, '0000-00-00 00:00:00', '0000-00-00 00:00:00', '', '');

DROP TABLE IF EXISTS `users_actions_permissions`;
CREATE TABLE `users_actions_permissions` (
  `user_id` int(11) NOT NULL,
  `app_id` int(11) NOT NULL,
  `C` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `M` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `D` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A',
  `R` char(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'A'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

TRUNCATE TABLE `users_actions_permissions`;
INSERT INTO `users_actions_permissions` (`user_id`, `app_id`, `C`, `M`, `D`, `R`) VALUES
(1, '1', 'A', 'A', 'A', 'A'),
(1, '2', 'U', 'A', 'U', 'A'),
(2, '1', 'U', 'U', 'U', 'U'),
(2, '2', 'I', 'U', 'I', 'U'),
(3, '1', 'I', 'I', 'I', 'I'),
(3, '2', 'I', 'A', 'I', 'A'),
(4, '1', 'A', 'U', 'I', 'A'),
(4, '2', 'U', 'U', 'U', 'A'),
(5, '1', 'I', 'A', 'U', 'I'),
(5, '2', 'I', 'A', 'U', 'I');

ALTER TABLE `is_users` ADD PRIMARY KEY (`id`);
ALTER TABLE `is_users` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
ALTER TABLE `users_actions_permissions` ADD PRIMARY KEY( `user_id`, `app_id`);

SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
