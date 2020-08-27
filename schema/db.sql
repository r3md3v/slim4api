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
) ENGINE=InnoDB AUTO_INCREMENT=5503 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

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
) ENGINE=InnoDB AUTO_INCREMENT=2001 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`USRID`, `USRNAME`, `USRPASS`, `USRFIRSTNAME`, `USRLASTNAME`, `USREMAIL`, `USRPROFILE`, `USRUPDATEDBY`, `USRUPDATEDAT`) VALUES
(1, 'margaux17', '5;<xbI`cjbfqBU7T', 'Victoire', 'Moulin', 'richard97@mallet.org', 'users ', 'root', '2019-12-02 06:29:12'),
(2, 'luce.barbier', 'k4v\'h$QK0L', 'Laurence', 'Gonzalez', 'gilbert93@lefevre.com', 'users ', 'root', '2019-12-03 00:34:03'),
(3, 'caroline.colin', 'I%*NZ\'\'hhbfwBGgf', 'Constance', 'Poirier', 'jourdan.raymond@delannoy.org', 'customers stocks articles ', 'system', '2020-03-30 09:58:07'),
(4, 'xavier44', 'v1f<UN}J$:~kD', 'Stéphane', 'Leger', 'dossantos.jacqueline@cousin.fr', 'customers ', 'admin', '2020-05-22 12:19:59'),
(5, 'mperrier', '=/*MO|Q', 'Michèle', 'Martineau', 'durand.renee@nguyen.fr', 'articles users stocks ', 'root', '2020-01-16 00:45:12');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
