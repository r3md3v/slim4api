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
$dt = "customers";
$nbl = 500; // nb of inserts
$fields = array(
    ['CUSNAME', 'userName'],
    ['CUSADDRESS', 'password'],
    ['CUSCITY', 'postCode city / countryCode'],
    ['CUSPHONE', 'phoneNumber'],
    ['CUSEMAIL', 'companyEmail'],
    ['CUSUPDATEDBY', 'randomElement(array("admin","system","root"))'],
    ['CUSUPDATEDAT', "dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')"]
	);

echo "<!doctype html>
<head><title>Faker to fill-in '$dt' table</title>
<meta http-equiv=\"Content-Type\" content=\"text/html;charset=UTF-8\" />
</head><body>";

$connexion = $connexion = mysqli_connect($dh, $du, $dp, $db);
$faker = Faker\Factory::create('fr_FR', 'en_US'); // enable both FR and US data

echo "<H2>Faker to fill in table '$dt' in database '$db'</h2>";

// table definition
echo "<hr><PRE>
DROP TABLE IF EXISTS `customers`;
CREATE TABLE IF NOT EXISTS `customers` (
  `CUSID` int(8) UNSIGNED NOT NULL AUTO_INCREMENT,
  `CUSNAME` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CUSADDRESS` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `CUSCITY` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `CUSPHONE` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `CUSEMAIL` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `CUSUPDATEDBY` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `CUSUPDATEDAT` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`CUSID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
</PRE><hr>";

echo "These fileds will be populated :<ul>";
for($n = 0;$n < count($fields); $n++)
	echo "<li>".$fields[$n][0]." = faker->".$fields[$n][1]."</li>";
echo "</ul>";

for ($i = 0; $i < $nbl; $i++){
	$p1 = addslashes($faker->name);
	$p2 = addslashes($faker->company);
    $p3 = addslashes($faker->streetAddress);
    $p4 = $faker->postCode.' '.addslashes($faker->city).' / '.$faker->countryCode;
    $p5 = $faker->phoneNumber;
	$p6 = $faker->companyEmail;
	$p7 = $faker->randomElement(array('admin','system','root'));
	$p8 = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'); // anytime from now back to 1 year

	$req = "INSERT INTO $dt VALUES (0,'$p1 / $p2','$p3','$p4','$p5','$p6','$p7','$p8');";
	mysqli_set_charset($connexion,'utf8');
	$res = mysqli_query($connexion,$req);
	//echo "$req<br />";
}

$req = "SELECT COUNT(*) FROM $dt;";
$res = mysqli_query($connexion,$req);
$row = mysqli_fetch_row($res);

$time = explode(' ', microtime());
$benchend = $time[1] + $time[0];

echo "$nbl rows inserted in ".number_format($benchend - $benchstart,5)."s / $row[0] rows in total";

mysqli_close($connexion);

?>