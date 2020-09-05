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
$dt = "logtokens";
$nbl = 500; // nb of inserts
$fields = array(
    ['TOKUSERNAME', 'userName'],
    ['TOKSOURCEIP', 'string'],
    ['TOKRESULT', "randomElement(array('0','1'))"],
	['TOKUPDATEDAT', "dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')"],
	['TOKEXPIREDAT', "dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s')"]
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
	$p2 = addslashes($faker->realText($maxNbChars = 200, $indexSize = 2));
	$p3 = $faker->randomElement(array('0','1'));
	$p4 = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'); // anytime from now back to 1 year
	$p5 = $faker->dateTimeBetween('-1 year', 'now')->format('Y-m-d H:i:s'); // anytime from now back to 1 year

	$req = "INSERT INTO $dt VALUES (0,'$p1','$p2','$p3','$p4','$p5');";
	//echo "$req<br />";
	mysqli_set_charset($connexion,'utf8');
	$res = mysqli_query($connexion,$req);
}

$req = "SELECT COUNT(*) FROM $dt;";
$res = mysqli_query($connexion,$req);
$row = mysqli_fetch_row($res);

$time = explode(' ', microtime());
$benchend = $time[1] + $time[0];

echo "$nbl rows inserted in ".number_format($benchend - $benchstart,5)."s / $row[0] rows in total";

mysqli_close($connexion);

?>