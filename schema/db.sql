-- SQL Dump

create schema if not exisTS slim;

use slim;

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `slim4api`
--

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `CUSID` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `CUSNAME` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CUSADDRESS` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `CUSCITY` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CUSPHONE` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `CUSEMAIL` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `CUSUPDATEDBY` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `CUSUPDATEDAT` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`CUSID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`CUSID`, `CUSNAME`, `CUSADDRESS`, `CUSCITY`, `CUSPHONE`, `CUSEMAIL`, `CUSUPDATEDBY`, `CUSUPDATEDAT`) VALUES
(1, 'Claude Germain / Descamps Faure SAS', '15, place Claude Pierre', '05 672 Marchal-sur-Mer', '+33 (0)7 68 93 87 99', 'maurice.veronique@schneider.com', 'System', '2020-08-01 04:25:49'),
(2, 'René Le Albert / Robert Dufour S.A.S.', '96, chemin de Regnier', '86 168 Rousset', '0497599904', 'marchand.susan@duhamel.com', 'System', '2020-08-01 04:25:49'),
(3, 'Luc Schmitt / Courtois', '9, rue de Gros', '54370 Leclerc', '08 92 23 34 51', 'josette43@lacombe.com', 'System', '2020-08-01 04:25:49'),
(4, 'Andrée Caron-Aubry / Blanchard et Fils', '52, avenue Charles Lefort', '49 163 Reynaudboeuf', '+33 1 40 92 55 82', 'paulette56@mahe.fr', 'System', '2020-08-01 04:25:49'),
(5, 'Manon Navarro / Martinez Perez S.A.R.L.', '468, impasse Bouchet', '44 640 Marques', '+33 (0)1 86 18 29 57', 'boutin.valerie@barre.fr', 'System', '2020-08-01 04:25:49');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `USRID` int(8) NOT NULL AUTO_INCREMENT,
  `USRNAME` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `USRPASS` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `USRFIRSTNAME` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `USRLASTNAME` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `USREMAIL` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `USRPROFILE` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `USRUPDATEDBY` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `USRUPDATEDAT` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`USRID`),
  UNIQUE KEY `USRNAME` (`USRNAME`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`USRID`, `USRNAME`, `USRPASS`, `USRFIRSTNAME`, `USRLASTNAME`, `USREMAIL`, `USRPROFILE`, `USRUPDATEDBY`, `USRUPDATEDAT`) VALUES
(1, 'margaux17', '5;<xbI`cjbfqBU7T', 'Victoire', 'Moulin', 'richard97@mallet.org', 'users ', 'root', '2019-12-02 06:29:12'),
(2, 'luce.barbier', 'k4v\'h$QK0L', 'Laurence', 'Gonzalez', 'gilbert93@lefevre.com', 'users ', 'root', '2019-12-03 00:34:03'),
(3, 'caroline.colin', 'I%*NZ\'\'hhbfwBGgf', 'Constance', 'Poirier', 'jourdan.raymond@delannoy.org', 'customers stocks articles ', 'system', '2020-03-30 09:58:07'),
(4, 'xavier44', 'v1f<UN}J$:~kD', 'Stéphane', 'Leger', 'dossantos.jacqueline@cousin.fr', 'customers ', 'admin', '2020-05-22 12:19:59'),
(5, 'mperrier', '=/*MO|Q', 'Michèle', 'Martineau', 'durand.renee@nguyen.fr', 'articles users stocks ', 'root', '2020-01-16 00:45:12');


--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `logins`;
CREATE TABLE IF NOT EXISTS `logins` (
  `JWUID` int(8) NOT NULL AUTO_INCREMENT,
  `JWUUSERNAME` varchar(50) NOT NULL,
  `JWUPASSWORD` varchar(2048) NOT NULL,
  `JWUEMAIL` varchar(100) NOT NULL,
  `JWUDESCRIPTION` varchar(200) NOT NULL,
  `JWULASTTOKEN` varchar(1000) NOT NULL,
  `JWUSTATUS` varchar(1) NOT NULL,
  `JWUCREATED` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `JWUMODIFIED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`JWUID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login`
--

INSERT INTO `logins` (`JWUID`, `JWUUSERNAME`, `JWUPASSWORD`, `JWUEMAIL`, `JWUDESCRIPTION`, `JWULASTTOKEN`, `JWUSTATUS`, `JWUCREATED`, `JWUMODIFIED`) VALUES
(1, 'MikeD', '$2y$10$0VkUbW.zvlAwtRESZPcp.OEAsndWTl3kSReSQdgK6bUcvfc63JQTe', 'mike@codeofaninja.com', 'Login [MikeD/password] for Mike Dalisay CodeOfANinja', false, '1', now(), now()),
(2, 'user', '$2y$10$zz81A20v7euIGy/GBtd4aePUXN9/ydknQgqt.cGINk4FeDASiS1tS', 'user@slim4api.com', 'Login [user/secret] with autologin function to easily test JWT', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY0ZDY3OWVhLWNiMGEtNDg4Ny1iY2RlLWVlYTNiOTg3OGUzMyJ9.eyJpc3MiOiJ3d3cuZXhhbXBsZS5jb20iLCJqdGkiOiJmNGQ2NzllYS1jYjBhLTQ4ODctYmNkZS1lZWEzYjk4NzhlMzMiLCJpYXQiOjE1OTg0NDE4MjgsIm5iZiI6MTU5ODQ0MTgyOCwiZXhwIjoxNTk4NDU2MjI4LCJ1aWQiOiJ1c2VyIn0.ueZ8wEx2JuDG9xyzNp2wekNDzQn8GLlmkxGiKqJ5opbk3_j5Ce4tNjFfWDBkIIT_dhdnB1UFfy4NnO9-4k-rcWUe3X21AJfztgguieFBQIEb5HrFh3xfl0JpuuWtZkw_474P5lA5w446SESue4BF0kDz9tyCX5SpMn7o3wTnDpTqoGIaVfJM5UcFlWVj-73UEgaN_WR627OIQF9gkmFWKM6FSvZcMFEFUwq26NnrW5Q2wGrNe8uHEXQ-J0cXJKFh88NsA34X0OUBrPCbkI_cJkz4BiJGJrm8MNvPg6e-PV91dAJase7RfM4zfwAwIqFdiYrIeXX5O5XX9Kcjv6U6OA', '1', now(), now()),
(3, 'nogo', '$2y$10$0VkUbW.zvlAwtRESZPcp.OEAsndWTl3kSReSQdgK6bUcvfc63JQTe', 'inactive@user.com', 'Login [nogo/password] to test inactive user and exception', false, '0', now(), now())
;

--
-- Table structure for table `loglogin`
--

DROP TABLE IF EXISTS `loglogins`;
CREATE TABLE IF NOT EXISTS `loglogins` (
  `LOGID` int(8) NOT NULL AUTO_INCREMENT,
  `LOGUSERNAME` varchar(100) NOT NULL,
  `LOGSOURCEIP` varchar(100) NOT NULL,
  `LOGRESULT` varchar(100) NOT NULL,
  `LOGUPDATEDAT` datetime NOT NULL,
  UNIQUE KEY `LOGID` (`LOGID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `loglogin`
--

INSERT INTO `loglogins` VALUES (1, 'miked', '127.0.0.1', 'ok', now());
INSERT INTO `loglogins` VALUES (2, 'user', '127.0.0.1', 'ok', now());
INSERT INTO `loglogins` VALUES (3, 'nogo', '127.0.0.1', 'Access locked: nogo', now());

--
-- Table structure for table `logtokens`
--

DROP TABLE IF EXISTS `logtokens`;
CREATE TABLE IF NOT EXISTS `logtokens` (
  `TOKID` int(8) NOT NULL AUTO_INCREMENT,
  `TOKUSERNAME` varchar(100) NOT NULL,
  `TOKTOKEN` varchar(1000) NOT NULL,
  `TOKSTATUS` varchar(2) NOT NULL,
  `TOKISSUEDAT` datetime NOT NULL,
  `TOKEXPIREDAT` datetime NOT NULL,
  PRIMARY KEY (`TOKID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `logtokens`
--

INSERT INTO `logtokens` (`TOKID`, `TOKUSERNAME`, `TOKTOKEN`, `TOKSTATUS`, `TOKISSUEDAT`, `TOKEXPIREDAT`) VALUES
(0, 'user', 'blabla', '1', '2020-08-28 07:29:00', '2020-08-28 07:29:00');

--
-- Table structure for table `login`
--

DROP TABLE IF EXISTS `logins`;
CREATE TABLE IF NOT EXISTS `logins` (
  `JWUID` int(8) NOT NULL AUTO_INCREMENT,
  `JWUUSERNAME` varchar(50) NOT NULL,
  `JWUPASSWORD` varchar(2048) NOT NULL,
  `JWUEMAIL` varchar(100) NOT NULL,
  `JWUDESCRIPTION` varchar(200) NOT NULL,
  `JWULASTTOKEN` varchar(1000) NOT NULL,
  `JWUSTATUS` varchar(1) NOT NULL,
  `JWUCREATED` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `JWUMODIFIED` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`JWUID`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `login`
--

INSERT INTO `logins` (`JWUID`, `JWUUSERNAME`, `JWUPASSWORD`, `JWUEMAIL`, `JWUDESCRIPTION`, `JWULASTTOKEN`, `JWUSTATUS`, `JWUCREATED`, `JWUMODIFIED`) VALUES
(0, 'MikeD', '$2y$10$0VkUbW.zvlAwtRESZPcp.OEAsndWTl3kSReSQdgK6bUcvfc63JQTe', 'mike@codeofaninja.com', 'Login [MikeD/password] for Mike Dalisay CodeOfANinja', false, '1', now(), now()),
(0, 'user', '$2y$10$zz81A20v7euIGy/GBtd4aePUXN9/ydknQgqt.cGINk4FeDASiS1tS', 'user@slim4api.com', 'Login [user/secret] with autologin function to easily test JWT', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY0ZDY3OWVhLWNiMGEtNDg4Ny1iY2RlLWVlYTNiOTg3OGUzMyJ9.eyJpc3MiOiJ3d3cuZXhhbXBsZS5jb20iLCJqdGkiOiJmNGQ2NzllYS1jYjBhLTQ4ODctYmNkZS1lZWEzYjk4NzhlMzMiLCJpYXQiOjE1OTg0NDE4MjgsIm5iZiI6MTU5ODQ0MTgyOCwiZXhwIjoxNTk4NDU2MjI4LCJ1aWQiOiJ1c2VyIn0.ueZ8wEx2JuDG9xyzNp2wekNDzQn8GLlmkxGiKqJ5opbk3_j5Ce4tNjFfWDBkIIT_dhdnB1UFfy4NnO9-4k-rcWUe3X21AJfztgguieFBQIEb5HrFh3xfl0JpuuWtZkw_474P5lA5w446SESue4BF0kDz9tyCX5SpMn7o3wTnDpTqoGIaVfJM5UcFlWVj-73UEgaN_WR627OIQF9gkmFWKM6FSvZcMFEFUwq26NnrW5Q2wGrNe8uHEXQ-J0cXJKFh88NsA34X0OUBrPCbkI_cJkz4BiJGJrm8MNvPg6e-PV91dAJase7RfM4zfwAwIqFdiYrIeXX5O5XX9Kcjv6U6OA', '1', now(), now()),
(0, 'nogo', '$2y$10$0VkUbW.zvlAwtRESZPcp.OEAsndWTl3kSReSQdgK6bUcvfc63JQTe', 'inactive@user.com', 'Login [nogo/password] to test inactive user and exception', false, '0', now(), now())
;

--
-- Table structure for table `loginlog`
--

DROP TABLE IF EXISTS `loginlog`;
CREATE TABLE IF NOT EXISTS `loginlog` (
  `LOGID` int(8) NOT NULL AUTO_INCREMENT,
  `LOGUSERNAME` varchar(100) NOT NULL,
  `LOGSOURCEIP` varchar(100) NOT NULL,
  `LOGRESULT` varchar(100) NOT NULL,
  `LOGUPDATEDAT` datetime NOT NULL,
  UNIQUE KEY `LOGID` (`LOGID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

--
-- Dumping data for table `loginlog`
--

INSERT INTO `loginlog` VALUES (0, 'miked', '127.0.0.1', 'ok', now());
INSERT INTO `loginlog` VALUES (0, 'user', '127.0.0.1', 'ok', now());
INSERT INTO `loginlog` VALUES (0, 'nogo', '127.0.0.1', 'Access locked: nogo', now());

--
-- Table structure for table `tokens`
--

DROP TABLE IF EXISTS `tokens`;
CREATE TABLE IF NOT EXISTS `tokens` (
  `TOKID` int(8) NOT NULL AUTO_INCREMENT,
  `TOKUSERNAME` varchar(100) NOT NULL,
  `TOKTOKEN` varchar(1000) NOT NULL,
  `TOKSTATUS` varchar(2) NOT NULL,
  `TOKISSUEDAT` datetime NOT NULL,
  `TOKEXPIREDAT` datetime NOT NULL,
  PRIMARY KEY (`TOKID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `tokens`
--

INSERT INTO `tokens` (`TOKID`, `TOKUSERNAME`, `TOKTOKEN`, `TOKSTATUS`, `TOKISSUEDAT`, `TOKEXPIREDAT`) VALUES
(0, 'user', 'blabla', '1', '2020-08-28 07:29:00', '2020-08-28 07:29:00');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
