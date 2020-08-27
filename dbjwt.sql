-- SQL Dump

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `slim4api`
--

-- --------------------------------------------------------

--
-- Table structure for table `usersjwt`
--

DROP TABLE IF EXISTS `usersjwt`;
CREATE TABLE IF NOT EXISTS `usersjwt` (
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
-- Dumping data for table `usersjwt`
--

-- logins are MikeD/password user/secret nogo/password
INSERT INTO `usersjwt` (`JWUID`, `JWUUSERNAME`, `JWUPASSWORD`, `JWUEMAIL`, `JWUDESCRIPTION`, `JWULASTTOKEN`, `JWUSTATUS`, `JWUCREATED`, `JWUMODIFIED`) VALUES
(0, 'MikeD', '$2y$10$2OdnMJ2v23vmmv7PzgFjr.exxcGinHucbAFqkM38qbBlOJOfQ0Age', 'mike@codeofaninja.com', 'Mike Dalisay codeofaninja.com', false, '1', now(), now()),
(0, 'user', '$2y$10$zz81A20v7euIGy/GBtd4aePUXN9/ydknQgqt.cGINk4FeDASiS1tS', 'user@slim4api.com', 'user/password login for testing JWT in Slim4API', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImY0ZDY3OWVhLWNiMGEtNDg4Ny1iY2RlLWVlYTNiOTg3OGUzMyJ9.eyJpc3MiOiJ3d3cuZXhhbXBsZS5jb20iLCJqdGkiOiJmNGQ2NzllYS1jYjBhLTQ4ODctYmNkZS1lZWEzYjk4NzhlMzMiLCJpYXQiOjE1OTg0NDE4MjgsIm5iZiI6MTU5ODQ0MTgyOCwiZXhwIjoxNTk4NDU2MjI4LCJ1aWQiOiJ1c2VyIn0.ueZ8wEx2JuDG9xyzNp2wekNDzQn8GLlmkxGiKqJ5opbk3_j5Ce4tNjFfWDBkIIT_dhdnB1UFfy4NnO9-4k-rcWUe3X21AJfztgguieFBQIEb5HrFh3xfl0JpuuWtZkw_474P5lA5w446SESue4BF0kDz9tyCX5SpMn7o3wTnDpTqoGIaVfJM5UcFlWVj-73UEgaN_WR627OIQF9gkmFWKM6FSvZcMFEFUwq26NnrW5Q2wGrNe8uHEXQ-J0cXJKFh88NsA34X0OUBrPCbkI_cJkz4BiJGJrm8MNvPg6e-PV91dAJase7RfM4zfwAwIqFdiYrIeXX5O5XX9Kcjv6U6OA', '1', now(), now()),
(0, 'nogo', '$2y$10$zz81A20v7euIGy/GBtd4aePUXN9/ydknQgqt.cGINk4FeDASiS1tS', 'inactive@user.com', 'Inactive user to test exception on login function', false, '0', now(), now())
;
