<?php

// Set default timezone to Europe/Paris if PHP 5.4 // Keep this a very first data loaded by PHP
if (@function_exists('date_default_timezone_set') && @function_exists('date_default_timezone_get'))
    @date_default_timezone_set(@date_default_timezone_get());
if (version_compare(PHP_VERSION, '5.4.0') >= 0)
    date_default_timezone_set('Europe/Paris');

require_once 'vendor/autoload.php';
require __DIR__ . '/config/settings.php';

// Benmark start for page load
$time = explode(' ', microtime());
$benchstart = $time[1] + $time[0];

// db settings
$dh = $settings['db']['host'];
$db = $settings['db']['database'];
$du = $settings['db']['username'];
$dp = $settings['db']['password'];
$dt = "users";
$nbl = 500; // nb of inserts
$fields = array(
    ['USRNAME', 'userName'],
    ['USRPASS', 'password'],
    ['USRFIRSTNAME', 'firstName'],
    ['USRLASTNAME', 'lastName'],
    ['USREMAIL', 'companyEmail'],
    ['USRPROFILE', "randomElements(array('users','customers','articles','stocks'), 2)"],
    ['USRUPDATEDBY', 'randomElement(array("admin","system","root"))'],
	['USRUPDATEDAT', "dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')"]
	);

echo "<!doctype html>
<head><title>Faker to fill-in '$dt' table</title>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\" />
</head><body>";

$connexion = $connexion = mysqli_connect($dh, $du, $dp, $db);
$faker = Faker\Factory::create('fr_FR', 'en_US'); // enable both FR and US data

echo "<H2>Faker to fill in table '$dt' in database '$db'</h2>
Rmk : duplicate USRNAME/userName will be ignored<br /><br />";

// table definition
echo "<hr><PRE>
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
</PRE><hr>";

echo "These fileds will be populated :<ul>";
for($n = 0;$n < count($fields); $n++)
	echo "<li>".$fields[$n][0]." = faker->".$fields[$n][1]."</li>";
echo "</ul>";
	
for ($i = 0; $i < $nbl; $i++){
	/*$req = "INSERT INTO $dt VALUES (0,";
	$sep = "";
	for($n=0;$n < count($fields); $n++){
		$fn = $fields[$n][1];
		$req .= "$sep '".addslashes($faker->$fn)."'";
		$sep = ",";
	}
	$ts = date("Y-m-d H:i:s");
	$req .= ", 'System', '$ts');";*/

	$p1 = addslashes($faker->userName);
	$p2 = addslashes($faker->password);
	$p3 = addslashes($faker->firstName);
	$p4 = addslashes($faker->lastName);
	$p5 = $faker->companyEmail;

	$p6s = $faker->randomElements(array('users','customers','articles','stocks'), $faker->numberBetween(1, 4)); // 1 to 4 of table name
	$p6 = "";
	foreach($p6s as $key=>$value)
		$p6 .= $value." ";

	$p7 = $faker->randomElement(array('admin','system','root'));
	$p8 = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'); // anytime from now back to 1 year

	$req = "INSERT INTO users VALUES (0,'$p1','$p2','$p3','$p4','$p5','$p6','$p7','$p8');";
	//echo "$req<br />";
	mysqli_set_charset($connexion,'utf8');
	$res = mysqli_query($connexion,$req);
}

$req = "SELECT COUNT(*) FROM users;";
$res = mysqli_query($connexion,$req);
$row = mysqli_fetch_row($res);

$time = explode(' ', microtime());
$benchend = $time[1] + $time[0];

echo "$nbl rows inserted in ".number_format($benchend - $benchstart,5)."s / $row[0] rows in total";

mysqli_close($connexion);

?>